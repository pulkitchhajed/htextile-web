<?php
session_start();

include_once '../includes/config.php';
include_once '../includes/header.php';
include_once '../includes/menu.php';

$con = get_connection();

$rep_print = isset($_REQUEST['rep_print']) ? $_REQUEST['rep_print'] : '';
$rep_xls = isset($_REQUEST['rep_xls']) ? $_REQUEST['rep_xls'] : '';

$buyer_code = isset($_POST['buyer_account_code']) ? mysqli_real_escape_string($con, $_POST['buyer_account_code']) : '';
$supplier_code = isset($_POST['supplier_account_code']) ? mysqli_real_escape_string($con, $_POST['supplier_account_code']) : '';
$status_filter = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';

// Fetch buyer firm_name for selected buyer_code
$buyer_firm_name = '';
if ($buyer_code !== '') {
    $buyer_name_result = mysqli_query($con, "SELECT firm_name FROM txt_company WHERE company_id = '$buyer_code'");
    if ($buyer_name_result && $row = mysqli_fetch_assoc($buyer_name_result)) {
        $buyer_firm_name = $row['firm_name'];
    }
}

error_log("Buyer firm_name for filter: " . $buyer_firm_name);
error_log("Supplier code selected: " . $supplier_code);

$where_clauses = [];
if ($status_filter === 'Pending' || $status_filter === 'Done') {
    $where_clauses[] = "d.status = '" . mysqli_real_escape_string($con, $status_filter) . "'";
}
if ($buyer_firm_name !== '') {
    // Remove GST number or appended text from buyer_firm_name before filtering
    $buyer_name_parts = explode(' ', $buyer_firm_name);
    $last_part = end($buyer_name_parts);
    if (preg_match('/^[0-9A-Z]{15}$/', $last_part)) {
        array_pop($buyer_name_parts);
    }
    $buyer_firm_name_clean = implode(' ', $buyer_name_parts);
    $buyer_firm_name_esc = mysqli_real_escape_string($con, $buyer_firm_name_clean);
    $where_clauses[] = "LOWER(o.buyer) LIKE LOWER('%" . $buyer_firm_name_esc . "%')";
}
if ($supplier_code !== '') {
    $supplier_name_result = mysqli_query($con, "SELECT firm_name FROM txt_company WHERE company_id = '$supplier_code'");
    $supplier_firm_name = '';
    if ($supplier_name_result && $row = mysqli_fetch_assoc($supplier_name_result)) {
        $supplier_firm_name = $row['firm_name'];
    }
if ($supplier_firm_name !== '') {
    // Remove GST number or appended text from supplier_firm_name before filtering
    $supplier_name_parts = explode(' ', $supplier_firm_name);
    $last_part = end($supplier_name_parts);
    if (preg_match('/^[0-9A-Z]{15}$/', $last_part)) {
        array_pop($supplier_name_parts);
    }
    $supplier_firm_name_clean = implode(' ', $supplier_name_parts);
    $supplier_firm_name_esc = mysqli_real_escape_string($con, $supplier_firm_name_clean);
    $where_clauses[] = "LOWER(o.supplier) LIKE LOWER('%" . $supplier_firm_name_esc . "%')";
}
}

$where_sql = '';
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(' AND ', $where_clauses);
}

$sql = "SELECT d.detail_id, d.offer_id, d.quality, d.meter, d.price, d.status,
               o.offer_number, o.supplier, o.buyer, o.created_at as offer_date
        FROM offer_letter_details d
        JOIN offer_letters o ON d.offer_id = o.offer_id
        $where_sql
        ORDER BY o.created_at DESC, d.detail_id ASC";

error_log("Order Confirmation Report SQL: " . $sql);
error_log("Where clause: " . $where_sql);

$result = mysqli_query($con, $sql);

if (isset($_REQUEST['rep_xls']) && $_REQUEST['rep_xls'] != '') {
    // Generate Excel download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=order_confirmation_report.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1'>";
    echo "<tr>
            <th>S.No.</th>
            <th>Offer Number</th>
            <th>Supplier</th>
            <th>Buyer</th>
            <th>Offer Date</th>
            <th>Quality</th>
            <th>Meter</th>
            <th>Price</th>
            <th>Status</th>
          </tr>";

    $serial_no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $serial_no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['offer_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['supplier']) . "</td>";
        echo "<td>" . htmlspecialchars($row['buyer']) . "</td>";
        echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['offer_date']))) . "</td>";
        echo "<td>" . htmlspecialchars($row['quality']) . "</td>";
        echo "<td>" . htmlspecialchars($row['meter']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    exit();
}

if (isset($_REQUEST['rep_print']) && $_REQUEST['rep_print'] != '') {
    // Generate PDF download
    require_once('../tcpdf/tcpdf.php');

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Heera Textile');
    $pdf->SetTitle('Order Confirmation Report');
    $pdf->SetHeaderData('', 0, 'Order Confirmation Report', '');
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage();

    $html = '<h2>Order Confirmation Report</h2>';
    $html .= '<table border="1" cellpadding="4">';
    $html .= '<tr style="background-color:#f2f2f2;">
                <th>S.No.</th>
                <th>Offer Number</th>
                <th>Supplier</th>
                <th>Buyer</th>
                <th>Offer Date</th>
                <th>Quality</th>
                <th>Meter</th>
                <th>Price</th>
                <th>Status</th>
              </tr>';

    $serial_no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
                    <td>' . $serial_no++ . '</td>
                    <td>' . htmlspecialchars($row['offer_number']) . '</td>
                    <td>' . htmlspecialchars($row['supplier']) . '</td>
                    <td>' . htmlspecialchars($row['buyer']) . '</td>
                    <td>' . htmlspecialchars(date('d-m-Y', strtotime($row['offer_date']))) . '</td>
                    <td>' . htmlspecialchars($row['quality']) . '</td>
                    <td>' . htmlspecialchars($row['meter']) . '</td>
                    <td>' . htmlspecialchars($row['price']) . '</td>
                    <td>' . htmlspecialchars($row['status']) . '</td>
                  </tr>';
    }
    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('order_confirmation_report.pdf', 'D');
    exit();
}

$company_array = array("All" => "");
$company_result = mysqli_query($con, "SELECT * FROM txt_company WHERE delete_tag='FALSE' ORDER BY company_id ASC");
while ($row = mysqli_fetch_assoc($company_result)) {
    $company_array[$row['firm_name']] = $row['company_id'];
}

$col_span = 8;

?>

<div class="content_padding" style="background-color: white; padding: 15px;">
    <div class="content-header">
        <table width="100%" border='0'><tr><td><h3>Order Confirmation Report (Quality-wise)</h3></td></tr></table>
    </div>
    <form method="post" id="report" onsubmit="" enctype="multipart/form-data" >              
        <table class="tbl_border" style="width: 100%; max-width: 900px; margin-bottom: 10px;">
          <tr>
             <th style="width: 20%; text-align: left;">Supplier Name</th>
            <td style="width: 30%;">
                <select name="supplier_account_code" id="supplier_account_code" onchange="downloadLock()" style="width: 100%;">
                    <option value="">-- Select Supplier --</option>
                    <?php
                    $suppliers_result = mysqli_query($con, "SELECT company_id, firm_name FROM txt_company WHERE firm_type='Supplier' AND delete_tag='FALSE' ORDER BY firm_name ASC");
                    while ($supplier_row = mysqli_fetch_assoc($suppliers_result)) {
                        $supplier_val = $supplier_row['company_id'];
                        $supplier_name = htmlspecialchars($supplier_row['firm_name']);
                        $selected = ($supplier_code === $supplier_val) ? 'selected' : '';
                        echo "<option value='$supplier_val' $selected>$supplier_name</option>";
                    }
                    ?>
                </select>
            </td>
                        <th style="width: 20%; text-align: left;">Buyer Name</th>
            <td style="width: 30%;">
                <select name="buyer_account_code" id="buyer_account_code" onchange="downloadLock()" style="width: 100%;">
                    <option value="">-- Select Buyer --</option>
                    <?php
                    $buyers_result = mysqli_query($con, "SELECT company_id, firm_name FROM txt_company WHERE firm_type='Buyer' AND delete_tag='FALSE' ORDER BY firm_name ASC");
                    while ($buyer_row = mysqli_fetch_assoc($buyers_result)) {
                        $buyer_val = $buyer_row['company_id'];
                        // Remove GST number or any appended text after firm_name if present
                        $buyer_name_raw = $buyer_row['firm_name'];
                        $buyer_name_parts = explode(' ', $buyer_name_raw);
                        // Assuming GST number is last part if it matches GST pattern, remove it
                        $last_part = end($buyer_name_parts);
                        if (preg_match('/^[0-9A-Z]{15}$/', $last_part)) {
                            array_pop($buyer_name_parts);
                        }
                        $buyer_name = htmlspecialchars(implode(' ', $buyer_name_parts));
                        $selected = ($buyer_code === $buyer_val) ? 'selected' : '';
                        echo "<option value='$buyer_val' $selected>$buyer_name</option>";
                    }
                    ?>
                </select>
            </td>
          </tr>

                       <th style="width: 15%; text-align: left;">Filter by Status</th>
            <td style="width: 20%;">
                <select name="status" id="status" onchange="this.form.submit()" style="width: 100%;">
                    <option value="">-- All --</option>
                    <option value="Pending" <?php if ($status_filter === 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Done" <?php if ($status_filter === 'Done') echo 'selected'; ?>>Done</option>
                </select>
            </td>
           
        </table>
        <br>
        <table style="max-width: 900px; margin-bottom: 20px;">
          <tr>
            <td>
              <input type="button" class="form-button" onclick="report_submit()" value="Submit" style="padding: 6px 12px; font-size: 14px;" />
            </td>
          </tr>
        </table>
        <input type='hidden' name='report_disp' id='report_disp' value='OK' >
        <input type='hidden' name='download_lock' id='download_lock' value='OFF' >
        <br>
        <br>
    </form>
    <script>
        function report_submit(){
            document.getElementById('report').submit();
        }
    </script>

<?php if (isset($_POST['report_disp']) && $_POST['report_disp'] == 'OK'): ?>

    <table border="1" style="border-collapse: collapse; width: 100%; text-align: left; font-family: Arial, sans-serif; font-size: 12px;">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Offer Number</th>
                <th>Supplier</th>
                <th>Buyer</th>
                <th>Offer Date</th>
                <th>Quality</th>
                <th>Meter</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serial_no = 1;
            $prev_buyer = '';
            $prev_supplier = '';
            $buyer_total_meter = 0;
            $buyer_total_price = 0;
            $supplier_total_meter = 0;
            $supplier_total_price = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                /*
                if ($prev_buyer !== '' && $prev_buyer !== $row['buyer']) {
                    // Buyer subtotal row
                    echo "<tr style='font-weight:bold; background-color:#f0f0f0;'>";
                    echo "<td colspan='5'>Subtotal for Buyer: " . htmlspecialchars($prev_buyer) . "</td>";
                    echo "<td></td>";
                    echo "<td>" . number_format($buyer_total_meter, 2) . "</td>";
                    echo "<td>" . number_format($buyer_total_price, 2) . "</td>";
                    echo "<td></td>";
                    echo "</tr>";
                    $buyer_total_meter = 0;
                    $buyer_total_price = 0;
                }
                */


                echo "<tr>";
                echo "<td>" . $serial_no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['offer_number']) . "</td>";
                echo "<td>" . htmlspecialchars($row['supplier']) . "</td>";
                echo "<td>" . htmlspecialchars($row['buyer']) . "</td>";
                echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['offer_date']))) . "</td>";
                echo "<td>" . htmlspecialchars($row['quality']) . "</td>";
                echo "<td>";
                if (is_numeric($row['meter'])) {
                    echo number_format($row['meter'], 2);
                } else {  
                    echo htmlspecialchars($row['meter']);
                }
                echo "</td>";
                echo "<td>";
                if (is_numeric($row['price'])) {
                    echo number_format($row['price'], 2);
                } else {
                    echo htmlspecialchars($row['price']);
                }
                echo "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "</tr>";

                $buyer_meter = is_numeric($row['meter']) ? floatval($row['meter']) : 0;
                $buyer_price = is_numeric($row['price']) ? floatval($row['price']) : 0;
                $supplier_meter = is_numeric($row['meter']) ? floatval($row['meter']) : 0;
                $supplier_price = is_numeric($row['price']) ? floatval($row['price']) : 0;

                $buyer_total_meter += $buyer_meter;
                $buyer_total_price += $buyer_price;
                $supplier_total_meter += $supplier_meter;
                $supplier_total_price += $supplier_price;

                $prev_buyer = $row['buyer'];
                $prev_supplier = $row['supplier'];
            }

            // Final subtotal rows


            ?>
        </tbody>
    </table>
    <br>
    <form method="post" style="margin-top: 10px;">
        <input type="hidden" name="buyer_account_code" value="<?php echo htmlspecialchars($buyer_code); ?>">
        <input type="hidden" name="supplier_account_code" value="<?php echo htmlspecialchars($supplier_code); ?>">
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
        <input type="hidden" name="report_disp" value="OK">
        <input type="submit" name="rep_xls" value="Download Excel" class="form-button" style="padding: 6px 12px; font-size: 14px; margin-right: 10px;">
        <input type="submit" name="rep_print" value="Download PDF" class="form-button" style="padding: 6px 12px; font-size: 14px;">
    </form>
<?php endif; ?>
