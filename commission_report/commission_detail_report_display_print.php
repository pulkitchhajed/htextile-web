<?php include("../includes/check_session.php");
include("../includes/config.php");
$con=get_connection();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php

$comm_report_type="";
if(isset($_REQUEST['comm_report_type'])){
  $comm_report_type=$_REQUEST['comm_report_type'];
 
}


$sql="select * from txt_company where delete_tag='FALSE' order by company_id ASC";
$result=mysqli_query($con,$sql);

$rowcount=0;
// Creating Company Array with reverse Key Value Pair 
// because array_search function searched value and returns key
// first value Dummy to show the position of details
$company_array=array("Value"=>"Key"); 
while($rs=mysqli_fetch_array($result))
{
  $companyRow[$rowcount][0]=$rs['company_id'];
  $companyRow[$rowcount][1]=$rs['firm_name'];
  $com_array=array($rs['firm_name']=>$rs['company_id']);
  $company_array=array_merge($company_array,$com_array);

/*
  echo $companyRow[$rowcount][0];
  echo $companyRow[$rowcount][1];
*/
  $rowcount++;

}




$comm_report_type=$_REQUEST['comm_report_type'];
$rep_supplier_code=$_REQUEST['supplier_account_code'];
$disp_suppllier_code=array_search($rep_supplier_code,$company_array);



$sql_agent="SELECT firm_name,pan_number,contact_person FROM txt_company WHERE delete_tag='FALSE' AND company_id IN (SELECT agent_id FROM txt_company WHERE delete_tag='FALSE' AND company_id='$rep_supplier_code')";

$res_agent=mysqli_query($con,$sql_agent);
$row_agt=mysqli_fetch_array($res_agent);
$agent_name=$row_agt['firm_name'];
$pan_num=$row_agt['pan_number'];
$contact_person=$row_agt['contact_person'];

/*
$rep_start_date=convert_date($_REQUEST['start_date']);
$rep_end_date=convert_date($_REQUEST['end_date']);
*/

$start_date=$_REQUEST['start_date'];
$end_date=$_REQUEST['end_date'];


echo "<title>";
echo $disp_suppllier_code;
echo "_"."$comm_report_type"."_";
echo "_"."$start_date"."_";
echo "_"."$end_date"."_";
echo "$agent_name";




echo "</title>";

?>


<!-- <title>Commission Report</title> -->


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

<body style="background-color:#FFFFFF">
<?php 
//style="border:0px solid #e5f1f8;background-color:#FFFFFF"
?>
<table width="100%" border="0" align="center" style="border:0px solid #e5f1f8;background-color:#FFFFFF">
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

<?php  if($_REQUEST['bill_report_disp']=='OK'){  $rep_print="OK"; ?>    
<table ><tr><td>
<?php // include("../includes/header_xls.php"); ?>    
</td></tr>
<tr><td>             
<?php include("commission_detail_report_display.php"); ?>   

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