<?php
session_start();
include_once '../includes/config.php';
include_once 'pdf_generator.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $con = get_connection();

    //$offer_number = intval($_POST['offer_number']);
    $supplier = mysqli_real_escape_string($con, $_POST['supplier']);
    $buyer = mysqli_real_escape_string($con, $_POST['buyer']);
    $gst_number = mysqli_real_escape_string($con, $_POST['gst_number']);
    $transport = mysqli_real_escape_string($con, $_POST['transport']);
    $qualities = $_POST['quality'];
    $meters = $_POST['meter'];
    $prices = $_POST['price'];
    $description = isset($_POST['description']) ? mysqli_real_escape_string($con, $_POST['description']) : null;

    // Generate next unique offer_number
    $offer_number_result = mysqli_query($con, "SELECT MAX(offer_number) AS max_offer_number FROM offer_letters");
    $max_offer_number_row = mysqli_fetch_assoc($offer_number_result);
    $offer_number = $max_offer_number_row ? intval($max_offer_number_row['max_offer_number']) + 1 : 1;

    // Insert into offer_letters table
    $insert_offer_sql = "INSERT INTO offer_letters (offer_number, supplier, buyer, buyer_gst, transport, description) VALUES ($offer_number, '$supplier', '$buyer', '$gst_number', '$transport', " . ($description !== null ? "'$description'" : "NULL") . ")";
    if (mysqli_query($con, $insert_offer_sql)) {
        $offer_id = mysqli_insert_id($con);

        // Insert offer details
        for ($i = 0; $i < count($qualities); $i++) {
            $quality = mysqli_real_escape_string($con, $qualities[$i]);
            $meter = mysqli_real_escape_string($con, $meters[$i]);
            $price = mysqli_real_escape_string($con, $prices[$i]);
            $insert_detail_sql = "INSERT INTO offer_letter_details (offer_id, quality, meter, price, display_order) VALUES ($offer_id, '$quality', '$meter', '$price', $i)";
            mysqli_query($con, $insert_detail_sql);
        }

        // Generate offer summary text for WhatsApp
        $offer_summary = "Offer Letter\nSupplier: $supplier\nBuyer: $buyer\nGST: $gst_number\nTransport: $transport\nOffer Number: $offer_number\n\nDetails:\n";
        for ($i = 0; $i < count($qualities); $i++) {
            $q = $qualities[$i];
            $m = $meters[$i];
            $p = $prices[$i];
            $offer_summary .= "Quality: $q, Meter: $m, Price: $p\n";
        }
        if ($description !== null && $description !== '') {
            $offer_summary .= "\nDescription:\n$description\n";
        }

        // Generate PDF file using reusable function
        generate_offer_letter_pdf($offer_id);

        // Update offer status to 'Pending' on creation
        $update_offer_status_sql = "UPDATE offer_letters SET status='Pending' WHERE offer_id = $offer_id";
        mysqli_query($con, $update_offer_status_sql);

        // Check which button was clicked
        if (isset($_POST['save_and_share'])) {
            // WhatsApp share URL
            $encoded_message = urlencode($offer_summary);
            $whatsapp_url = "https://wa.me/?text=$encoded_message";

            // Redirect to WhatsApp
            header("Location: $whatsapp_url");
            exit();
        } else {
            // Redirect to offer letter listing page
            header("Location: ../offer_letter/view_offer_letters.php");
            exit();
        }
    } else {
        echo "Error saving offer: " . mysqli_error($con);
    }
} else {
    echo "Invalid request method.";
}
?>
