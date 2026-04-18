<?php include("../includes/check_session.php");
include("../includes/config.php");
$con=get_connection();

$download='XLS';

$time=time()+19800; // Timestamp is in GMT now converted to IST
$date=date('d_m_Y_H_i_s',$time);
?>


<?php
$con=get_connection();



$sql="select * from txt_company where delete_tag='FALSE' order by company_id ASC";
$result=mysqli_query($con,$sql);

$rowcount=0;
// Creating Company Array with reverse Key Value Pair 
// because array_search function searched value and returns key
// first value Dummy to show the position of details
//$company_array=array("Value"=>"Key"); 
$Supplier_array=array("Value"=>"Key"); 
$Buyer_array=array("Value"=>"Key"); 
while($rs=mysqli_fetch_array($result))
{
  /*
  $companyRow[$rowcount][0]=$rs['company_id'];
  $companyRow[$rowcount][1]=$rs['firm_name'];
  $com_array=array($rs['firm_name']=>$rs['company_id']);
  $company_array=array_merge($company_array,$com_array);
  */

  if($rs['firm_type']=='Supplier'){
    //echo "Hello";
     // $supplierRow[$rowcount][0]=$rs['company_id'];
      //$supplierRow[$rowcount][1]=$rs['firm_name'];
      $sup_array=array($rs['firm_name']=>$rs['company_id']);
      $Supplier_array=array_merge($Supplier_array,$sup_array);
    
    }
    
    
    if($rs['firm_type']=='Buyer'){
    
      //$buyerRow[$rowcount][0]=$rs['company_id'];
      //$buyerRow[$rowcount][1]=$rs['firm_name'];
      $buy_array=array($rs['firm_name']=>$rs['company_id']);
      $Buyer_array=array_merge($Buyer_array,$buy_array);  
      
    }   

/*
  echo $companyRow[$rowcount][0];
  echo $companyRow[$rowcount][1];
*/
  $rowcount++;

}
?>

<?php 
$header_buyer_code="Buyer";
if(isset($_REQUEST['buyer_account_code'])){
  $header_buyer_code=$_REQUEST['buyer_account_code'];
}
?>

<?php 
$header_supplier_code="Supplier";
if(isset($_REQUEST['supplier_account_code'])){
  $header_supplier_code=$_REQUEST['supplier_account_code'];
}
?>

<?php

$rep_bill_start_date=convert_date($_REQUEST['bill_start_date']);
$rep_bill_end_date=convert_date($_REQUEST['bill_end_date']);

?>

<?php

$file_name= array_search($header_supplier_code,$Supplier_array)." - ".array_search($header_buyer_code,$Buyer_array).
" - ". "Payment Report Interest -".$rep_bill_start_date." - ".$rep_bill_end_date." As On - ".$date;
?>

<?php 
//application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
header ( "Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );
header ( "Content-Disposition: attachment; filename=".$file_name.".xls" );
?>

<?php  if($_REQUEST['bill_report_disp']=='OK'){ $rep_print="" ;  $rep_xls="OK" ?>      
<table ><tr><td>
<?php include("../includes/header_xls.php"); ?>    
</td></tr>
<tr><td>         
<?php include("bill_report_interest__display_plus_minus.php"); ?>   
</td>
</tr>
</table>
<?php } ?>  
<?php 
release_connection($con);
?>