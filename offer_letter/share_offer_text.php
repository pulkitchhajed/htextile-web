<?php
// share_offer_text.php
// Sends offer letter details as text message via WhatsApp Business API

include_once '../includes/config.php';

if (!isset($_GET['offer_id'])) {
    echo "Missing offer_id";
    exit();
}

$offer_id = intval($_GET['offer_id']);
$con = get_connection();

// Fetch offer details
$result = mysqli_query($con, "SELECT supplier, buyer, buyer_gst, transport, description FROM offer_letters WHERE offer_id = $offer_id");
$offer = mysqli_fetch_assoc($result);

if (!$offer) {
    echo "Offer not found";
    exit();
}

// Compose message text
$message_text = "Offer Letter\n";

// Hardcoded agent name
$message_text .= "Agent: Heera Textiles\n";

// Fetch offer number from offer_letters table
$offer_number_result = mysqli_query($con, "SELECT offer_number FROM offer_letters WHERE offer_id = $offer_id");
$offer_number_row = mysqli_fetch_assoc($offer_number_result);
$offer_number = $offer_number_row['offer_number'] ?? 'N/A';

$message_text .= "Offer Number: $offer_number\n";

$message_text .= "Supplier: {$offer['supplier']}\n";

/**
 * Trim GST number ahead of buyer name if appended
 * Example: "BuyerName GSTNumber" => "BuyerName"
 */
$buyer_name_trimmed = preg_replace('/\s+[A-Z0-9]{15}$/i', '', $offer['buyer']);
$message_text .= "Buyer: {$buyer_name_trimmed}\n";

$message_text .= "GST: {$offer['buyer_gst']}\n";
$message_text .= "Transport: {$offer['transport']}\n\n";

// Fetch offer details for message body
$details_result = mysqli_query($con, "SELECT quality, meter, price FROM offer_letter_details WHERE offer_id = $offer_id");
$message_text .= "Details:\n";
$counter = 1;
while ($detail = mysqli_fetch_assoc($details_result)) {
    $message_text .= $counter . ".\n";
    $message_text .= "Quality: {$detail['quality']}, Meter: {$detail['meter']}, Price: {$detail['price']}\n\n";
    $counter++;
}

$message_text .= "\nDescription:\n{$offer['description']}\n";

/**
 * TODO: Replace the following placeholders with your actual WhatsApp Business API credentials.
 * You need to obtain these from your WhatsApp Business API provider or Facebook Business Manager.
 */
$whatsapp_api_url = 'https://graph.facebook.com/v15.0/YOUR_PHONE_NUMBER_ID/messages'; // Example for Facebook Graph API
$whatsapp_token = 'YOUR_LONG_LIVED_ACCESS_TOKEN';
$recipient_phone_number = 'RECIPIENT_PHONE_NUMBER'; // E.g., '919999999999'

// Note: Ensure your server can reach the WhatsApp API endpoint and the token is valid.
// You may need to configure SSL, firewall, and other network settings accordingly.

/**
 * Since you want to share via normal WhatsApp (not WhatsApp Business API),
 * the best approach is to redirect the user to WhatsApp Web or WhatsApp app
 * with a pre-filled message using a URL.
 *
 * This script will generate a WhatsApp URL with the message text and redirect the user.
 */

$phone_number = ''; // Optional: recipient phone number with country code, e.g. '919999999999'. Leave empty to let user choose.

$encoded_message = urlencode($message_text);

if (!empty($phone_number)) {
    $whatsapp_url = "https://wa.me/$phone_number?text=$encoded_message";
} else {
    $whatsapp_url = "https://wa.me/?text=$encoded_message";
}

// Redirect to WhatsApp URL
header("Location: $whatsapp_url");
exit();
?>
