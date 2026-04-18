<?php include("../includes/check_session.php");
include("../includes/config.php");
$con=get_connection();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$time=time()+19800; // Timestamp is in GMT now converted to IST
$date=date('d_m_Y',$time);
?>

<?php
//$con=get_connection();



$sql="select * from txt_company where delete_tag='FALSE' order by company_id ASC";
$result=mysqli_query($con,$sql);


// Creating Company Array with reverse Key Value Pair 
// because array_search function searched value and returns key
// first value Dummy to show the position of details
$company_array=array("Value"=>"Key"); 
$company_array=array("All"=>""); 
while($rs=mysqli_fetch_array($result))
{

  $com_array=array($rs['firm_name']=>$rs['company_id']);
  $company_array=array_merge($company_array,$com_array);




}
?>


<?php 
$header_buyer_code="Buyer";
if(isset($_REQUEST['buyer_account_code'])){
  $header_buyer_code=$_REQUEST['buyer_account_code'];
}


$site_days="";
if(isset($_REQUEST['site_days'])){
  $site_days=$_REQUEST['site_days'];
}


/*
$roi_month="";
if(isset($_REQUEST['roi_month'])){
  $roi_month=$_REQUEST['roi_month'];
}  
*/
$roi_month=0;
$roi_day=0;
if(isset($_REQUEST['roi_month'])){

  $roi_month=$_REQUEST['roi_month'];
  if($roi_month==""){
    $roi_month=0;
  }
  $roi_day=($roi_month*12)/36500;
} 

?>

<title>
<?php echo array_search($_REQUEST['buyer_account_code'],$company_array); ?>
 Outstanding Report Interest <?php echo $date?></title>
<link rel="icon" type="image/x-icon" href="<?php echo $web_path; ?>images/dt-favicon.ico" />
<link href="<?php echo $web_path; ?>css/style.css" rel="stylesheet" type="text/css" />
<meta charset="UTF-8" />


<style type="text/css" media="print">
  @page { size:A4 portrait; }
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

<body style="background-color:#FFFFFF">
<!-- border:1px solid #e5f1f8; -->
<table width="100%" border="0" align="center" style="background-color:#FFFFFF">
<tr> <td>
<?php

$time=time()+19800; // Timestamp is in GMT now converted to IST
$date=date('d_m_Y_H_i_s',$time);

//application/vnd.openxmlformats-officedocument.spreadsheetml.sheet 
//application/octet-stream


//header('Content-Type: application/pdf');
//header('Content-Type: application/octet-stream');
//header('Accept-Ranges: bytes');
//header('Accept-Charset: iso-8859-1,*,utf-8');

//header('Content-Transfer-Encoding: Binary');
//header ( "Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
//header ( "Content-Disposition: attachment; filename=Buyer_outstanding_report_".$date.".pdf" );
?>

<?php  if($_REQUEST['report_disp']=='OK'){  $rep_xls="" ; $download='';  $rep_print="OK"; ?>    
<table border='0'  style="background-color:#FFFFFF" ><tr><td>
<?php include("../includes/header_xls.php"); ?>    
</td></tr>
<tr><td>             
<?php include("interest_buyer_outstanding_report_display.php"); ?>   
</td>
</tr>
</table>
<?php } ?>  
</td>
</tr>
</table>
<script>
    window.print();
</script>
</body>
</html>
<?php 
release_connection($con);
?>