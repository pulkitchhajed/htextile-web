<?php include("../includes/check_session.php");
include("../includes/config.php");
$time=time()+19800; // Timestamp is in GMT now converted to IST
$date=date('d_m_Y_H_i_s',$time);

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


//application/vnd.openxmlformats-officedocument.spreadsheetml.sheet header('Content-Type: application/pdf');
header ( "Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
header ( "Content-Disposition: attachment; filename=Buyer_outstanding_report_".$date.".xls" );
$con=get_connection();

$download='XLS';




?>

<?php  if($_REQUEST['report_disp']=='OK'){ $rep_print="" ;  $rep_xls="OK"; ?>    
<table border='1' ><tr><td>
<?php include("../includes/header_xls.php"); ?>    
</td></tr>
<tr><td>             
<?php include("interest_buyer_outstanding_report_display.php"); ?>   
</td>
</tr>
</table>
<?php } ?>  
<?php 
release_connection($con);
?>