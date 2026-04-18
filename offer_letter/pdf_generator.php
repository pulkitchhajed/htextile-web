<?php
// Removed session_start() to avoid session already active notice
include_once '../includes/config.php';
require_once('../vendor/autoload.php'); // Assuming TCPDF is installed via composer

function generate_offer_letter_pdf($offer_id) {
    $con = get_connection();

    // Fetch offer letter data
    $offer_result = mysqli_query($con, "SELECT * FROM offer_letters WHERE offer_id = $offer_id");
    $offer = mysqli_fetch_assoc($offer_result);
    if (!$offer) {
        return false;
    }

    // Fetch offer letter details
    $details_result = mysqli_query($con, "SELECT * FROM offer_letter_details WHERE offer_id = $offer_id ORDER BY display_order ASC");
    $details = [];
    while ($row = mysqli_fetch_assoc($details_result)) {
        $details[] = $row;
    }

    $supplier = $offer['supplier'];
    $buyer = $offer['buyer'];
    $gst_number = $offer['buyer_gst'];
    $transport = $offer['transport'];
    $offer_number = $offer['offer_number'];
    $description = $offer['description'];

    $pdf = new \TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('HTextile');
    $pdf->SetTitle("Offer Letter - $supplier - $buyer - $offer_number");
    $pdf->SetMargins(15, 27, 15);
    $pdf->AddPage();

    // Add logo
    $logo_file = realpath('../images/dt_logo_name.png');
    if ($logo_file) {
        $page_width = $pdf->getPageWidth();
        $image_width = 180; // desired width in mm
        $x = ($page_width - $image_width) / 2;
        $pdf->Image($logo_file, $x, 10, $image_width, 0, '', '', '', false, 300, '', false, false, 0);
    }
    $pdf->Ln(45);

    $html = "<h1>Offer Letter</h1>";
     $html .= "<p><strong>Offer Number:</strong> " . htmlspecialchars($offer_number) . "</p>";
    $html .= "<p><strong>Supplier:</strong> " . htmlspecialchars($supplier) . "</p>";
    $html .= "<p><strong>Buyer:</strong> " . htmlspecialchars($buyer) . "</p>";
    $html .= "<p><strong>GST Number:</strong> " . htmlspecialchars($gst_number) . "</p>";
    $html .= "<p><strong>Transport:</strong> " . htmlspecialchars($transport) . "</p>";
   
    $html .= "<table border=\"1\" cellpadding=\"4\">
                <thead>
                    <tr>
                        <th>Quality</th>
                        <th>Meter</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($details as $detail) {
        $html .= "<tr>
                    <td>" . htmlspecialchars($detail['quality']) . "</td>
                    <td>" . htmlspecialchars($detail['meter']) . "</td>
                    <td>" . htmlspecialchars($detail['price']) . "</td>
                  </tr>";
    }
    $html .= "</tbody></table>";

    $pdf->writeHTML($html, true, false, true, false, '');

    if (!empty($description)) {
        $html_desc = "<h3>Description</h3><p>" . nl2br(htmlspecialchars($description)) . "</p>";
        $pdf->writeHTML($html_desc, true, false, true, false, '');
    }

    $pdf_dir = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'offer_letter_pdfs';
    if (!file_exists($pdf_dir)) {
        mkdir($pdf_dir, 0777, true);
    }
    $pdf_filename = $pdf_dir . DIRECTORY_SEPARATOR . preg_replace('/\s+/', '_', $supplier) . "_" . preg_replace('/\s+/', '_', $buyer) . "_$offer_number.pdf";
    $pdf->Output($pdf_filename, 'F');

    // Update offer_letters table with PDF path
    $pdf_relative_path = 'offer_letter/offer_letter_pdfs/' . basename($pdf_filename);
    $update_pdf_path_sql = "UPDATE offer_letters SET pdf_path = '$pdf_relative_path' WHERE offer_id = $offer_id";
    mysqli_query($con, $update_pdf_path_sql);

    return true;
}
?>
