<?php
/**
 * ============================================================
 *  HTextile — Supabase Storage API Utility
 *  Handles serverless file uploads without using local disk
 * ============================================================
 */

require_once(__DIR__ . '/config.php');

/**
 * Uploads a local temp file to Supabase Storage.
 *
 * @param string $localFilePath The path to the uploaded temp file (e.g., $_FILES['file']['tmp_name'])
 * @param string $fileName      The desired file name in Supabase (e.g., '12345_bill.pdf')
 * @param string $contentType   The MIME type of the file (e.g., $_FILES['file']['type'])
 * @param string $bucket        The Supabase storage bucket name (default 'htextile-uploads')
 * @return array                ['success' => bool, 'error' => string|null, 'path' => string|null]
 */
function supabase_upload_file(string $localFilePath, string $fileName, string $contentType = 'application/octet-stream', string $bucket = 'htextile-uploads'): array {
    $projectRef = explode('.', getenv('DB_HOST'))[1] ?? '';
    // If DB_HOST is using pooler, we need project ID differently. It's better to extract from DB_HOST unless it's pooler.
    // Assuming DB_HOST = db.zedcauxdecmotvbnbqrv.supabase.co
    if (str_contains(getenv('DB_HOST'), 'pooler')) {
        // Fallback or needs SUPABASE_URL in .env
        $projectUrl = getenv('SUPABASE_URL');
        if (!$projectUrl) return ['success' => false, 'error' => 'SUPABASE_URL not set in .env'];
    } else {
        $projectUrl = 'https://' . $projectRef . '.supabase.co';
    }

    $supabaseKey = getenv('SUPABASE_KEY');
    if (!$supabaseKey) {
        return ['success' => false, 'error' => 'SUPABASE_KEY not set in .env. It is required for uploads.'];
    }

    $url = $projectUrl . "/storage/v1/object/$bucket/" . urlencode($fileName);
    $fileData = file_get_contents($localFilePath);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $supabaseKey",
        "apikey: $supabaseKey",
        "Content-Type: $contentType",
        "x-upsert: true"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300) {
        return [
            'success' => true,
            'error' => null,
            'path' => $fileName
        ];
    } else {
        return [
            'success' => false,
            'error' => $result['message'] ?? 'Unknown error uploading to Supabase',
            'path' => null
        ];
    }
}

/**
 * Returns the public URL for a file in Supabase Storage.
 *
 * @param string $fileName The file name stored in Supabase
 * @param string $bucket   The Supabase storage bucket name
 * @return string          The full public URL
 */
function supabase_get_public_url(string $fileName, string $bucket = 'htextile-uploads'): string {
    if (empty($fileName)) return '';
    
    // Project URL derivation
    if (str_contains(getenv('DB_HOST'), 'pooler')) {
        $projectUrl = getenv('SUPABASE_URL');
    } else {
        $projectRef = explode('.', getenv('DB_HOST'))[1] ?? '';
        $projectUrl = 'https://' . $projectRef . '.supabase.co';
    }

    return $projectUrl . "/storage/v1/object/public/$bucket/" . urlencode($fileName);
}
