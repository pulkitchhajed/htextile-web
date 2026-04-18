<?php
/**
 * ============================================================
 *  HTextile — Logging Utilities
 *  Fixes:
 *    - Timezone now uses named zone (Asia/Kolkata) not +19800 offset
 *    - Log directory auto-created if missing
 *    - Debug/error flags stay the same for backward compatibility
 * ============================================================
 */

// ── Log Switches ─────────────────────────────────────────────
// err_log  : ALWAYS ON  — captures real errors
// debug_log: OFF in production, switch ON when debugging locally
$err_log             = 'ON';
$debug_log           = 'OFF';
$process_page_log    = 'OFF';
$process_page_redirect = 'ON';

// ── Timezone-safe Date Functions ──────────────────────────────
// Uses named timezone — no hardcoded +19800 offset needed.
date_default_timezone_set('Asia/Kolkata');

function date_time(): string {
    return date('d-m-Y H:i:s');
}

function date_time_filename(): string {
    return date('d_m_Y_H_i_s');
}

// ── Auto-create log directory if missing ─────────────────────
$_log_dir = __DIR__ . '/../log';
if (!is_dir($_log_dir)) {
    mkdir($_log_dir, 0755, true);
}
unset($_log_dir);

// ── Core Log Functions ────────────────────────────────────────

function parent_error_log(string $str, string $log_file): void {
    if ($GLOBALS['err_log'] === 'ON') {
        error_log("\n" . date_time() . "\n" . $str . "\n", 3, $log_file);
    }
}

function parent_debug_log(string $str, string $log_file): void {
    if ($GLOBALS['debug_log'] === 'ON') {
        error_log("\n" . date_time() . "\n" . $str . "\n", 3, $log_file);
    }
}

function process_logging_disp(string $str): void {
    if ($GLOBALS['process_page_log'] === 'ON') {
        echo '<br>' . htmlspecialchars($str) . '<br>';
    }
}

function process_redirect(string $str): void {
    if ($GLOBALS['process_page_redirect'] === 'ON') {
        echo $str;
    }
}

// ── Module-specific Log Functions ─────────────────────────────

function comm_log(string $str): void {
    parent_debug_log($str, __DIR__ . '/../log/comm_summ_disp_php_debug.log');
}

function add_payment_log(string $str): void {
    parent_debug_log($str, __DIR__ . '/../log/add_payment_php_debug.log');
}

function add_receipt_log(string $str): void {
    parent_debug_log($str, __DIR__ . '/../log/add_receipt_php_debug.log');
}

function edit_payment_log(string $str): void {
    parent_debug_log($str, __DIR__ . '/../log/edit_payment_php_debug.log');
}

function process_payment_entry_logging_disp(string $str): void {
    process_logging_disp($str);
    parent_debug_log($str, __DIR__ . '/../log/process_payment_php_debug.log');
}

function process_payment_entry_error_log(string $str): void {
    parent_error_log($str, __DIR__ . '/../log/process_payment_php_error.log');
}

function process_payment_entry_redirect(string $str): void {
    process_redirect($str);
}

function xls_report_log(string $str): void {
    parent_debug_log($str, __DIR__ . '/../log/xls_report.log');
}