<?php
/**
 * MySQL to PostgreSQL Converter
 * Run from command line: php utility/mysql_to_postgres.php
 * 
 * Input:  c:\Users\pulki\Downloads\htex_backup_18_04_2026_13_16_29.sql.txt
 * Output: c:\Users\pulki\Downloads\htex_postgres_migration.sql
 */

$inputFile  = 'C:/Users/pulki/Downloads/htex_backup_18_04_2026_13_16_29.sql.txt';
$outputFile = 'C:/Users/pulki/Downloads/htex_postgres_migration.sql';

echo "Reading MySQL dump...\n";
$lines = file($inputFile, FILE_IGNORE_NEW_LINES);
$total = count($lines);
echo "Total lines: $total\n";

$out = [];
$out[] = "-- PostgreSQL Migration Script";
$out[] = "-- Converted from MySQL dump (htex_backup_18_04_2026_13_16_29.sql.txt)";
$out[] = "-- Generated: " . date('Y-m-d H:i:s');
$out[] = "";
$out[] = "SET client_encoding = 'UTF8';";
$out[] = "SET standard_conforming_strings = on;";
$out[] = "";

$inCreateTable = false;
$createBuffer  = [];

foreach ($lines as $i => $line) {
    $trimmed = trim($line);

    // Skip blank lines and MySQL-specific directives
    if ($trimmed === '') { $out[] = ''; continue; }
    if (str_starts_with($trimmed, '--') && !str_starts_with($trimmed, '-- ')) continue;
    if (str_starts_with($trimmed, 'SET ') && strpos($trimmed, 'NAMES') !== false) continue;
    if (str_starts_with($trimmed, 'SET ') && strpos($trimmed, 'FOREIGN_KEY') !== false) continue;

    // DROP TABLE
    if (preg_match('/DROP TABLE IF EXISTS `?(\w+)`?/i', $trimmed, $m)) {
        $out[] = "DROP TABLE IF EXISTS {$m[1]} CASCADE;";
        $out[] = "";
        continue;
    }

    // START of CREATE TABLE
    if (preg_match('/CREATE TABLE `?(\w+)`?/i', $trimmed, $m)) {
        $inCreateTable = true;
        $createBuffer = ["CREATE TABLE {$m[1]} ("];
        continue;
    }

    // Inside CREATE TABLE block
    if ($inCreateTable) {
        // End of CREATE TABLE
        if (preg_match('/^\)\s*ENGINE=/i', $trimmed) || preg_match('/^\);/', $trimmed)) {
            // Remove trailing comma from last column
            $last = count($createBuffer) - 1;
            $createBuffer[$last] = rtrim($createBuffer[$last], ',');
            $createBuffer[] = ");";
            $out[] = implode("\n", $createBuffer);
            $out[] = "";
            $inCreateTable = false;
            $createBuffer = [];
            continue;
        }

        // Remove backticks
        $col = str_replace('`', '', $trimmed);

        // Skip: KEY (non-primary), UNIQUE KEY that is not PRIMARY
        if (preg_match('/^KEY\s+\w+/i', $col)) continue;

        // Convert data types
        $col = preg_replace('/\bbigint\(\d+\)\s+NOT NULL\s+AUTO_INCREMENT/i', 'BIGSERIAL NOT NULL', $col);
        $col = preg_replace('/\bint\(\d+\)\s+NOT NULL\s+AUTO_INCREMENT/i', 'SERIAL NOT NULL', $col);
        $col = preg_replace('/\bint\(\d+\)/i', 'INTEGER', $col);
        $col = preg_replace('/\bbigint\(\d+\)/i', 'BIGINT', $col);
        $col = preg_replace('/\bmediumint\(\d+\)/i', 'INTEGER', $col);
        $col = preg_replace('/\bsmallint\(\d+\)/i', 'SMALLINT', $col);
        $col = preg_replace('/\btinyint\(\d+\)/i', 'SMALLINT', $col);
        $col = preg_replace('/\bdecimal\((\d+),(\d+)\)/i', 'NUMERIC($1,$2)', $col);
        $col = preg_replace('/\bdatetime\b/i', 'TIMESTAMP', $col);
        $col = preg_replace('/\btimestamp\b(?!\s+NULL\s+DEFAULT\s+current_timestamp)/i', 'TIMESTAMP', $col);
        $col = preg_replace('/\bcurrent_timestamp\(\)/i', 'CURRENT_TIMESTAMP', $col);
        $col = preg_replace('/\bDATE\b/i', 'DATE', $col);
        $col = preg_replace('/\btext\b/i', 'TEXT', $col);
        $col = preg_replace('/\blongtext\b/i', 'TEXT', $col);
        $col = preg_replace('/\bmediumtext\b/i', 'TEXT', $col);
        $col = preg_replace('/\btinytext\b/i', 'TEXT', $col);

        // Convert enum to VARCHAR with CHECK
        if (preg_match("/enum\((.+?)\)/i", $col, $em)) {
            $values = $em[1];
            $col = preg_replace("/enum\(.+?\)/i", "VARCHAR(50) CHECK (column_name IN ($values))", $col);
        }

        // Remove MySQL-specific column options
        $col = preg_replace('/\bAUTO_INCREMENT\b/i', '', $col);
        $col = preg_replace('/\bCOMMENT\s+\'[^\']*\'/i', '', $col);
        $col = preg_replace('/\bCOLLATE\s+\w+/i', '', $col);
        $col = preg_replace('/\bCHARACTER SET\s+\w+/i', '', $col);
        $col = preg_replace('/\bCHARSET\s+\w+/i', '', $col);
        $col = preg_replace('/\bunsigned\b/i', '', $col);

        // UNIQUE KEY → UNIQUE constraint inline
        if (preg_match('/UNIQUE KEY\s+\w+\s+\((.+?)\)/i', $col, $uk)) {
            $createBuffer[] = "  UNIQUE ({$uk[1]}),";
            continue;
        }

        // PRIMARY KEY
        if (preg_match('/PRIMARY KEY\s*\((.+?)\)/i', $col, $pk)) {
            $createBuffer[] = "  PRIMARY KEY ({$pk[1]}),";
            continue;
        }

        // Clean up double spaces
        $col = preg_replace('/  +/', ' ', $col);
        $col = trim($col);

        if ($col !== '') {
            $createBuffer[] = "  $col";
        }
        continue;
    }

    // INSERT statements - convert double-quoted values to single-quoted
    if (preg_match('/^INSERT INTO/i', $trimmed)) {
        // Remove backticks from table name
        $line = str_replace('`', '', $trimmed);

        // The dump uses "value1","value2" format - convert to 'value1','value2'
        // Strategy: match VALUES(...) and replace " delimiters carefully
        $line = preg_replace_callback(
            '/VALUES\s*\((.+)\);$/i',
            function($m) {
                $inner = $m[1];
                // Replace "," with ',' and leading/trailing quotes
                // Values are delimited by "," - convert smartly
                $inner = preg_replace_callback(
                    '/"((?:[^"\\\\]|\\\\.)*)"/s',
                    function($v) {
                        // Escape single quotes inside value
                        $val = str_replace("'", "''", $v[1]);
                        return "'" . $val . "'";
                    },
                    $inner
                );
                return "VALUES($inner);";
            },
            $line
        );

        $out[] = $line;
        continue;
    }

    // Everything else - pass through cleaned up
    $line = str_replace('`', '', $trimmed);
    $out[] = $line;
}

echo "Writing PostgreSQL migration file...\n";
file_put_contents($outputFile, implode("\n", $out));
echo "Done! Output: $outputFile\n";
echo "Lines generated: " . count($out) . "\n";
