<?php
session_start();
include_once '../includes/config.php';
include_once '../includes/header.php';
include_once '../includes/menu.php';

$con = get_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    foreach ($_POST['status'] as $detail_id => $status) {
        $detail_id = intval($detail_id);
        $status = ($status === 'Done') ? 'Done' : 'Pending';
        $update_sql = "UPDATE offer_letter_details SET status = '$status' WHERE detail_id = $detail_id";
        if (!mysqli_query($con, $update_sql)) {
            echo "<p>Error updating status for detail ID $detail_id: " . mysqli_error($con) . "</p>";
        }
    }
    echo "<p>Status updated successfully.</p>";
}

// Fetch all quality line items with offer and company info, excluding those with status 'Done'
$query = "SELECT d.detail_id, d.offer_id, d.quality, d.meter, d.price, d.status,
                 o.offer_number, o.supplier, o.buyer
          FROM offer_letter_details d
          JOIN offer_letters o ON d.offer_id = o.offer_id
          WHERE d.status != 'Done'
          ORDER BY o.created_at DESC, d.detail_id ASC";
$details_result = mysqli_query($con, $query);
?>

<div class="content" style="background-color: white; margin: 5px; text-align: left;">
    <h2>Order Confirmation (Quality-wise)</h2>
    <form method="post" action="order_confirmation.php">
        <table border="1" style="border-collapse: collapse; width: 100%; text-align: left; font-family: Arial, sans-serif; font-size: 12px;">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Offer Number</th>
                    <th>Supplier</th>
                    <th>Buyer</th>
                    <th>Quality</th>
                    <th>Meter</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serial_no = 1;
                while ($detail = mysqli_fetch_assoc($details_result)) {
                    echo "<tr>";
                    echo "<td>" . $serial_no++ . "</td>";
                    echo "<td>" . htmlspecialchars($detail['offer_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($detail['supplier']) . "</td>";
                    echo "<td>" . htmlspecialchars($detail['buyer']) . "</td>";
                    echo "<td>" . htmlspecialchars($detail['quality']) . "</td>";
                    echo "<td>" . htmlspecialchars($detail['meter']) . "</td>";
                    echo "<td>" . htmlspecialchars($detail['price']) . "</td>";
                    echo "<td>";
                    echo "<select name='status[" . $detail['detail_id'] . "]'>";
                    $statuses = ['Pending', 'Done'];
                    foreach ($statuses as $status) {
                        $selected = ($detail['status'] === $status) ? "selected" : "";
                        echo "<option value='$status' $selected>$status</option>";
                    }
                    echo "</select>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <br>
        <button type="submit" name="update_status">Update Status</button>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>
