<?php
// share_offer.php
// Sends offer letter PDF via WhatsApp Business API

include_once '../includes/config.php';

if (!isset($_GET['offer_id'])) {
    echo "Missing offer_id";
    exit();
}

$offer_id = intval($_GET['offer_id']);
$con = get_connection();

// Fetch offer and PDF path and description
$result = mysqli_query($con, "SELECT pdf_path, supplier, buyer, buyer_gst, transport, description FROM offer_letters WHERE offer_id = $offer_id");
$offer = mysqli_fetch_assoc($result);

if (!$offer || empty($offer['pdf_path'])) {
    echo "Offer or PDF not found";
    exit();
}

$pdf_path = $offer['pdf_path'];
$pdf_full_path = realpath(__DIR__ . '/../' . $pdf_path);

if (!file_exists($pdf_full_path)) {
    echo "PDF file not found on server";
    exit();
}

// WhatsApp Business API credentials - replace with your actual values
$whatsapp_api_url = 'https://your-whatsapp-business-api-url/v1/messages';
$whatsapp_token = 'YOUR_ACCESS_TOKEN';
$recipient_phone_number = 'RECIPIENT_PHONE_NUMBER'; // E.g., '919999999999'

// Step 1: Upload media (PDF) to WhatsApp API
$media_upload_url = 'https://your-whatsapp-business-api-url/v1/media';
$media_type = 'application/pdf';
$media_name = basename($pdf_full_path);

$media_data = file_get_contents($pdf_full_path);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $media_upload_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $whatsapp_token",
    "Content-Type: $media_type",
    "Content-Disposition: attachment; filename=\"$media_name\""
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $media_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$upload_response = curl_exec($ch);
$upload_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($upload_http_code != 201) {
    echo "Failed to upload media to WhatsApp API. Response: $upload_response";
    exit();
}

$upload_response_data = json_decode($upload_response, true);
if (!isset($upload_response_data['media'][0]['id'])) {
    echo "Invalid upload response: $upload_response";
    exit();
}

$media_id = $upload_response_data['media'][0]['id'];

$offer_summary = "Offer Letter\nSupplier: {$offer['supplier']}\nBuyer: {$offer['buyer']}\nGST: {$offer['buyer_gst']}\nTransport: {$offer['transport']}\n\nDescription:\n{$offer['description']}\n\n";


// Step 2: Send media message with caption
$message_data = [
    'to' => $recipient_phone_number,
    'type' => 'document',
    'document' => [
        'id' => $media_id,
        'filename' => $media_name,
        'caption' => $offer_summary
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $whatsapp_api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $whatsapp_token",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$send_response = curl_exec($ch);
$send_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($send_http_code != 200 && $send_http_code != 201) {
    echo "Failed to send WhatsApp message. Response: $send_response";
    exit();
}

echo "WhatsApp message sent successfully.";
?>
