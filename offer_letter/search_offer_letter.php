<?php
if (session_status() == PHP_SESSION_NONE) 
include_once '../includes/config.php';

$con = get_connection();

$supplier = isset($_GET['supplier']) ? mysqli_real_escape_string($con, $_GET['supplier']) : '';
$buyer = isset($_GET['buyer']) ? mysqli_real_escape_string($con, $_GET['buyer']) : '';
$offer_number = isset($_GET['offer_number']) ? intval($_GET['offer_number']) : 0;

// Fetch suppliers and buyers for dropdowns
$suppliers = [];
$buyers = [];

$result = mysqli_query($con, "SELECT DISTINCT firm_name FROM txt_company WHERE firm_type = 'Supplier' ORDER BY firm_name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    // Remove GST number or appended text from firm_name
    $firm_name_raw = $row['firm_name'];
    $firm_name_parts = explode(' ', $firm_name_raw);
    $last_part = end($firm_name_parts);
    if (preg_match('/^[0-9A-Z]{15}$/', $last_part)) {
        array_pop($firm_name_parts);
    }
    $firm_name_clean = implode(' ', $firm_name_parts);
    $suppliers[] = $firm_name_clean;
}

$result = mysqli_query($con, "SELECT DISTINCT firm_name FROM txt_company WHERE firm_type = 'Buyer' ORDER BY firm_name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    // Remove GST number or appended text from firm_name
    $firm_name_raw = $row['firm_name'];
    $firm_name_parts = explode(' ', $firm_name_raw);
    $last_part = end($firm_name_parts);
    if (preg_match('/^[0-9A-Z]{15}$/', $last_part)) {
        array_pop($firm_name_parts);
    }
    $firm_name_clean = implode(' ', $firm_name_parts);
    $buyers[] = $firm_name_clean;
}

$where_sql = '';
if ($offer_number > 0) {
    // Search by offer number only
    $where_sql = "WHERE offer_number = $offer_number";
} else {
    $where_clauses = [];
if ($supplier !== '') {
    // Remove GST number or appended text from supplier before filtering
    $supplier_parts = explode(' ', $supplier);
    $last_part = end($supplier_parts);
    if (preg_match('/^[0-9A-Z]{15}$/', $last_part)) {
        array_pop($supplier_parts);
    }
    $supplier_clean = implode(' ', $supplier_parts);
    $supplier_esc = mysqli_real_escape_string($con, $supplier_clean);
    $where_clauses[] = "LOWER(supplier) LIKE LOWER('%" . $supplier_esc . "%')";
}
if ($buyer !== '') {
    // Remove GST number or appended text from buyer before filtering
    $buyer_parts = explode(' ', $buyer);
    $last_part = end($buyer_parts);
    if (preg_match('/^[0-9A-Z]{15}$/', $last_part)) {
        array_pop($buyer_parts);
    }
    $buyer_clean = implode(' ', $buyer_parts);
    $buyer_esc = mysqli_real_escape_string($con, $buyer_clean);
    $where_clauses[] = "LOWER(buyer) LIKE LOWER('%" . $buyer_esc . "%')";
}
    if (count($where_clauses) > 0) {
        // Combine supplier and buyer with OR
        $where_sql = "WHERE " . implode(' OR ', $where_clauses);
    }
}

$query = "SELECT * FROM offer_letters $where_sql ORDER BY created_at DESC";
$offers_result = mysqli_query($con, $query);
?>

<?php
session_start();
include_once '../includes/config.php';
include_once '../includes/header.php';
include_once '../includes/menu.php';
?>

<div class="content" style="background-color: white; margin: 20px; color: black;">
    <h2>Search Offer Letters</h2>
    <form method="get" action="search_offer_letter.php">
        <label for="supplier" style="font-weight: bold; font-size: 12px;">Supplier Name:</label>
        <select id="supplier" name="supplier" style="margin-bottom: 10px;">
            <option value="">Select Supplier</option>
            <?php foreach ($suppliers as $sup): ?>
                <option value="<?php echo htmlspecialchars($sup); ?>" <?php if ($sup == $supplier) echo 'selected'; ?>><?php echo htmlspecialchars($sup); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="buyer" style="font-weight: bold; font-size: 12px;">Buyer Name:</label>
        <select id="buyer" name="buyer" style="margin-bottom: 10px;">
            <option value="">Select Buyer</option>
            <?php foreach ($buyers as $buy): ?>
                <option value="<?php echo htmlspecialchars($buy); ?>" <?php if ($buy == $buyer) echo 'selected'; ?>><?php echo htmlspecialchars($buy); ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="offer_number" style="font-weight: bold; font-size: 12px;">Offer Number:</label>
        <input type="text" id="offer_number" name="offer_number" value="<?php echo htmlspecialchars($offer_number > 0 ? $offer_number : ''); ?>" style="margin-bottom: 10px;"><br>

        <button type="submit">Search</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && ( $supplier !== '' || $buyer !== '' || $offer_number > 0 )): ?>
        <?php if (mysqli_num_rows($offers_result) > 0): ?>
            <table class="tbl_border">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Edit</th>
                        <th>View</th>
                        <th>Offer Number</th>
                        <th>Supplier</th>
                        <th>Buyer</th>
                        <th>GST</th>
                        <th>Transport</th>
                        <th>Created At</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $serial_no = 1;
                    while ($offer = mysqli_fetch_assoc($offers_result)) {
                        echo "<tr>";
                        echo "<td>" . $serial_no++ . "</td>";
                        echo "<td><a href='edit_offer_letter.php?offer_id=" . $offer['offer_id'] . "'><img src='../images/Edit.png' alt='Edit' style='width:25px; height:25px;'></a></td>";
                        echo "<td><a href='view_offer_letter_pdf.php?offer_id=" . $offer['offer_id'] . "' target='_blank'><img src='../images/viewdetails_1.png' alt='View' style='width:25px; height:25px;'></a></td>";
                        echo "<td>" . htmlspecialchars($offer['offer_number'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($offer['supplier'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($offer['buyer'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($offer['buyer_gst'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($offer['transport'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($offer['created_at'] ?? '') . "</td>";
                        echo "<td><a href='view_offer_letters.php?delete_id=" . $offer['offer_id'] . "' onclick=\"return confirm('Are you sure you want to delete this offer?');\"><img src='../images/delete.png' alt='Delete' style='width:25px; height:25px;'></a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No offers found matching your criteria.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
</body>
</html>
