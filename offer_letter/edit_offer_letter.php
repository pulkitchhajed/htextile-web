<?php
session_start();
include_once '../includes/config.php';
include_once 'pdf_generator.php';

$con = get_connection();

$offer_id = isset($_GET['offer_id']) ? intval($_GET['offer_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process update form submission
    $offer_id = intval($_POST['offer_id']);
    $supplier = mysqli_real_escape_string($con, $_POST['supplier']);
    $buyer = mysqli_real_escape_string($con, $_POST['buyer']);
    $gst_number = mysqli_real_escape_string($con, $_POST['gst_number']);
    $transport = mysqli_real_escape_string($con, $_POST['transport']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $qualities = $_POST['quality'];
    $meters = $_POST['meter'];
    $prices = $_POST['price'];

    // Update offer_letters table including description
    $update_offer_sql = "UPDATE offer_letters SET supplier='$supplier', buyer='$buyer', buyer_gst='$gst_number', transport='$transport', description='$description' WHERE offer_id=$offer_id";
    if (mysqli_query($con, $update_offer_sql)) {
        // Delete existing offer details
        mysqli_query($con, "DELETE FROM offer_letter_details WHERE offer_id=$offer_id");

        // Insert updated offer details
        for ($i = 0; $i < count($qualities); $i++) {
            $quality = mysqli_real_escape_string($con, $qualities[$i]);
            $meter = mysqli_real_escape_string($con, $meters[$i]);
            $price = mysqli_real_escape_string($con, $prices[$i]);
            $insert_detail_sql = "INSERT INTO offer_letter_details (offer_id, quality, meter, price, display_order) VALUES ($offer_id, '$quality', '$meter', '$price', $i)";
            mysqli_query($con, $insert_detail_sql);
        }

        // Generate updated PDF
        generate_offer_letter_pdf($offer_id);

        if (isset($_POST['save_and_share'])) {
            // Redirect to share_offer_text.php to send WhatsApp message
            header("Location: share_offer_text.php?offer_id=$offer_id");
            exit();
        } else {
            header("Location: view_offer_letters.php");
            exit();
        }
    } else {
        echo "Error updating offer: " . mysqli_error($con);
    }
} else {
    // Display edit form
    $offer_result = mysqli_query($con, "SELECT * FROM offer_letters WHERE offer_id = $offer_id");
    $offer = mysqli_fetch_assoc($offer_result);

    if (!$offer) {
        echo "<p>Offer not found.</p>";
        exit();
    }

    $details_result = mysqli_query($con, "SELECT * FROM offer_letter_details WHERE offer_id = $offer_id");

    // Fetch suppliers, buyers, transports for dropdowns
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
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Offer Letter</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="background-color: white;">
    <?php include_once '../includes/header.php'; ?>
    <?php include_once '../includes/menu.php'; ?>
    <div class="content" style="background-color: white; margin: 20px;">
        <h2>Edit Offer Letter</h2>
    <form id="editOfferForm" method="post" action="edit_offer_letter.php?offer_id=<?php echo $offer_id; ?>">
        <input type="hidden" name="offer_id" value="<?php echo $offer_id; ?>">

        <!-- Offer Number hidden as per user request -->
        <input type="hidden" id="offer_number" name="offer_number" value="<?php echo htmlspecialchars($offer['offer_number']); ?>">

        <div style="display: flex; gap: 20px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label for="supplier" style="display: block; margin-bottom: 5px;">Supplier Name:</label>
                <select id="supplier" name="supplier" required style="width: 100%;">
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $supplier_item): ?>
                        <option value="<?php echo htmlspecialchars($supplier_item['firm_name']); ?>" <?php if ($supplier_item['firm_name'] == $offer['supplier']) echo 'selected'; ?>><?php echo htmlspecialchars($supplier_item['firm_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1;">
                <label for="buyer" style="display: block; margin-bottom: 5px;">Buyer Name:</label>
                <select id="buyer" name="buyer" required onchange="populateGST()" style="width: 100%;">
                    <option value="">Select Buyer</option>
                    <?php foreach ($buyers as $buyer_item): ?>
                        <?php
                            // Trim GST number from buyer name if appended
                            $trimmed_buyer_name = preg_replace('/\s+[A-Z0-9]{13,15}$/i', '', $buyer_item['firm_name']);
                        ?>
                        <option value="<?php echo htmlspecialchars($trimmed_buyer_name); ?>" data-gst="<?php echo htmlspecialchars($buyer_item['gstin']); ?>" <?php if ($trimmed_buyer_name == $offer['buyer']) echo 'selected'; ?>><?php echo htmlspecialchars($trimmed_buyer_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 20px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label for="gst_number" style="display: block; margin-bottom: 5px;">GST Number:</label>
                <input type="text" id="gst_number" name="gst_number" value="<?php echo htmlspecialchars($offer['buyer_gst']); ?>" readonly style="width: 100%;">
            </div>

            <div style="flex: 1;">
                <label for="transport" style="display: block; margin-bottom: 5px;">Transport Name:</label>
                <select id="transport" name="transport" required style="width: 100%;">
                    <option value="">Select Transport</option>
                    <?php foreach ($transports as $transport_item): ?>
                        <option value="<?php echo htmlspecialchars($transport_item); ?>" <?php if ($transport_item == $offer['transport']) echo 'selected'; ?>><?php echo htmlspecialchars($transport_item); ?></option>
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
                <?php while ($detail = mysqli_fetch_assoc($details_result)): ?>
                <tr>
                    <td><input type="text" name="quality[]" value="<?php echo htmlspecialchars($detail['quality']); ?>" required></td>
                    <td><input type="text" name="meter[]" value="<?php echo htmlspecialchars($detail['meter']); ?>" required></td>
                    <td><input type="text" name="price[]" value="<?php echo htmlspecialchars($detail['price']); ?>" required></td>
                    <td>
                        <button type="button" onclick="moveRowUp(this)">Up</button>
                        <button type="button" onclick="moveRowDown(this)">Down</button>
                        <button type="button" onclick="removeRow(this)">Remove</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="button" onclick="addRow()" style="margin: 10px 0 20px 0;">Add Row</button>

        <div style="margin-bottom: 15px;">
            <label for="description">Description (Optional):</label>
            <textarea id="description" name="description" rows="4" style="width: 100%;"><?php echo htmlspecialchars($offer['description'] ?? ''); ?></textarea>
        </div>

        <button type="submit" name="update">Update</button>
        <button type="submit" name="save_and_share" style="margin-left: 10px;">Save and Share</button>
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

        function moveRowUp(button) {
            var row = button.parentNode.parentNode;
            var prevRow = row.previousElementSibling;
            if (prevRow) {
                row.parentNode.insertBefore(row, prevRow);
            }
        }

        function moveRowDown(button) {
            var row = button.parentNode.parentNode;
            var nextRow = row.nextElementSibling;
            if (nextRow) {
                row.parentNode.insertBefore(nextRow, row);
            }
        }
        </script>
</body>
</html>

<?php
}
?>
