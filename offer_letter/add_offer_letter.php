<?php
session_start();
include_once '../includes/config.php';

$con = get_connection();

// Fetch suppliers, buyers, transports from company master table
$suppliers = [];
$buyers = [];
$transports = [];

$result = mysqli_query($con, "SELECT firm_name, gstin FROM txt_company WHERE firm_type = 'Supplier' ORDER BY firm_name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $suppliers[] = $row;
}

$result = mysqli_query($con, "SELECT firm_name, gstin FROM txt_company WHERE firm_type = 'Buyer' ORDER BY firm_name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $buyers[] = $row;
}

$result = mysqli_query($con, "SELECT firm_name FROM txt_company WHERE firm_type = 'Transport' ORDER BY firm_name ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $transports[] = $row['firm_name'];
}

/**
 * Get next offer number (auto increment) ensuring uniqueness
 * This logic assumes offer_number is unique in DB
 */
$offer_number_result = mysqli_query($con, "SELECT MAX(offer_number) AS max_offer_number FROM offer_letters");
$max_offer_number_row = mysqli_fetch_assoc($offer_number_result);
$next_offer_number = $max_offer_number_row ? intval($max_offer_number_row['max_offer_number']) + 1 : 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Offer Letter</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="background-color: white;">
    <?php include_once '../includes/header.php'; ?>
    <?php include_once '../includes/menu.php'; ?>
    <div class="content" style="background-color: white; margin: 10px;">
        <h2>Add Offer Letter</h2>
        <form id="addOfferForm" method="post" action="save_offer_letter.php">
            <!-- Offer Number hidden as per user request -->
            <input type="hidden" id="offer_number" name="offer_number" value="<?php echo $next_offer_number; ?>">

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label for="supplier" style="display: block; margin-bottom: 5px;">Supplier Name:</label>
                    <select id="supplier" name="supplier" required style="width: 100%;">
                        <option value="">Select Supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?php echo htmlspecialchars($supplier['firm_name']); ?>"><?php echo htmlspecialchars($supplier['firm_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1;">
            <label for="buyer" style="display: block; margin-bottom: 5px;">Buyer Name:</label>
            <select id="buyer" name="buyer" required onchange="populateGST()" style="width: 100%;">
                <option value="">Select Buyer</option>
                <?php foreach ($buyers as $buyer): ?>
                    <?php
                        // Trim GST number from buyer name if appended
                        $trimmed_buyer_name = preg_replace('/\s+[A-Z0-9]{13,15}$/i', '', $buyer['firm_name']);
                    ?>
                    <option value="<?php echo htmlspecialchars($trimmed_buyer_name); ?>" data-gst="<?php echo htmlspecialchars($buyer['gstin']); ?>"><?php echo htmlspecialchars($trimmed_buyer_name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label for="gst_number" style="display: block; margin-bottom: 5px;">GST Number:</label>
                    <input type="text" id="gst_number" name="gst_number" readonly style="width: 100%;">
                </div>

                <div style="flex: 1;">
                    <label for="transport" style="display: block; margin-bottom: 5px;">Transport Name:</label>
                    <select id="transport" name="transport" required style="width: 100%;">
                        <option value="">Select Transport</option>
                        <?php foreach ($transports as $transport): ?>
                            <option value="<?php echo htmlspecialchars($transport); ?>"><?php echo htmlspecialchars($transport); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h3>Offer Details</h3>
            <table id="offerDetailsTable" border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Quality</th>
                        <th>Meter</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="quality[]" required></td>
                        <td><input type="text" name="meter[]" required></td>
                        <td><input type="text" name="price[]" required></td>
                        <td><button type="button" onclick="removeRow(this)">Remove</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" onclick="addRow()">Add Row</button><br><br>
            <div style="margin-top: 15px;">
                <label for="description" style="display: block; margin-bottom: 5px;">Description (Optional):</label>
                <textarea id="description" name="description" rows="4" style="width: 100%;"></textarea>
            </div>
            <button type="submit" name="save" style="margin-right: 10px;">Save</button>
            <button type="submit" name="save_and_share">Save and Share</button>

        </form>
    </div>
    <?php include_once '../includes/footer.php'; ?>
    <script>
        /**
         * Populate GST number and trim GST number from buyer name if appended
         */
        function populateGST() {
            var buyerSelect = document.getElementById('buyer');
            var gstInput = document.getElementById('gst_number');
            var selectedOption = buyerSelect.options[buyerSelect.selectedIndex];
            var gst = selectedOption.getAttribute('data-gst') || '';
            gstInput.value = gst;

            // Trim GST number from buyer name if appended
            var buyerName = selectedOption.text;
            var trimmedBuyerName = buyerName.replace(/\s+[A-Z0-9]{15}$/i, '');
            selectedOption.text = trimmedBuyerName;
        }

        function addRow() {
            var table = document.getElementById('offerDetailsTable').getElementsByTagName('tbody')[0];
            var newRow = table.rows[0].cloneNode(true);
            var inputs = newRow.getElementsByTagName('input');
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].value = '';
            }
            table.appendChild(newRow);
        }

        function removeRow(button) {
            var row = button.parentNode.parentNode;
            var table = document.getElementById('offerDetailsTable').getElementsByTagName('tbody')[0];
            if (table.rows.length > 1) {
                table.removeChild(row);
            } else {
                alert('At least one row is required.');
            }
        }
    </script>
</body>
</html>
