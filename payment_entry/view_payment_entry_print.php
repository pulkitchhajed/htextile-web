<?php include("../includes/check_session.php");
include("../includes/config.php");
$con=get_connection();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Payment Voucher</title>
<link rel="icon" type="image/x-icon" href="<?php echo $web_path; ?>images/dt-favicon.ico" />
<link href="<?php echo $web_path; ?>css/style.css" rel="stylesheet" type="text/css" />
<meta charset="UTF-8" />


<style type="text/css" media="print">
  @page { size:A4 landscape; }
</style>

<style>
*
{
	margin:0;
	padding:0;
}

table,th,td
{
	border-collapse:collapse;
	font-size:12px;
}
</style>
</head>

<body>

<table width="100%" border="0" align="center" style="background-color:#FFFFFF">
<tr> <td>
<?php

$time=time()+19800; // Timestamp is in GMT now converted to IST
$date=date('d_m_Y_H_i_s',$time);


?>

<?php  if($_REQUEST['report_disp']=='OK'){  $rep_xls="" ; $download=''; $rep_print="OK"; ?>    
<table ><tr><td>
<?php include("../includes/voucher_print_header.php"); ?>    
</td></tr>
<tr><td>             
<?php include("view_payment_entry_disp.php"); ?>   
</td>
</tr>
</table>
<?php } ?>  
</td>
</tr>
</table>

<?php include("../includes/voucher_print_footer.php"); ?>
<script>
    window.print();
</script>
</body>
</html>
