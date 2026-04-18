<?php
include("../includes/check_session.php");
include("../includes/config.php");
include("../includes/header.php");
include("../includes/menu.php");

/*
include_once '../includes/config.php';
include_once '../includes/header.php';
include_once '../includes/menu.php';
*/
$con = get_connection();

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($con, "DELETE FROM offer_letters WHERE offer_id = $delete_id");
    mysqli_query($con, "DELETE FROM offer_letter_details WHERE offer_id = $delete_id");
    header("Location: view_offer_letters.php");
    exit();
}

// Fetch all offers
$offers_result = mysqli_query($con, "SELECT * FROM offer_letters ORDER BY created_at DESC");
?>

<div class="content" style="background-color: white; margin: 5px; text-align: left;">
    <h2 style="text-align: left;">View Offer Letters</h2>
    <a href="add_offer_letter.php"><button type="button" style="padding: 6px 12px; font-size: 14px; cursor: pointer; margin-right: 15px;">Add Offer Letter</button></a>
    <table border="1" style="border-collapse: collapse; width: 100%; text-align: left; font-family: Arial, sans-serif; font-size: 12px;">
        <thead>
            <tr style="font-size: 12px; font-family: Arial, sans-serif;">
                <th><b>S.No.</b></th>
                <th><b>Edit</b></th>
                <th><b>View</b></th>
                <th><b>Offer Number</b></th>
                <th><b>Supplier</b></th>
                <th><b>Buyer</b></th>
                <th><b>GST</b></th>
                <th><b>Transport</b></th>
                <th><b>Delete</b></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serial_no = 1;
            while ($offer = mysqli_fetch_assoc($offers_result)) {
                echo "<tr>";
                echo "<td>" . $serial_no++ . "</td>";
                echo "<td><a href='edit_offer_letter.php?offer_id=" . $offer['offer_id'] . "'><img src='../images/Edit.png' alt='Edit' style='width:25px; height:25px;'></a></td>";
                if (!empty($offer['pdf_path'])) {
                    echo "<td><a href='../" . htmlspecialchars($offer['pdf_path']) . "' target='_blank'><img src='../images/viewdetails_1.png' alt='View PDF' style='width:25px; height:25px;'></a></td>";
                } else {
                    echo "<td><a href='view_offer_letter_pdf.php?offer_id=" . $offer['offer_id'] . "' target='_blank'><img src='../images/viewdetails_1.png' alt='View' style='width:25px; height:25px;'></a></td>";
                }
                echo "<td>" . htmlspecialchars($offer['offer_number'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($offer['supplier'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($offer['buyer'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($offer['buyer_gst'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($offer['transport'] ?? '') . "</td>";
                // Removed timestamp column as requested
                // echo "<td>" . htmlspecialchars($offer['created_at'] ?? '') . "</td>";
                echo "<td><a href='view_offer_letters.php?delete_id=" . $offer['offer_id'] . "' onclick=\"return confirm('Are you sure you want to delete this offer?');\"><img src='../images/delete.png' alt='Delete' style='width:25px; height:25px;'></a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>
