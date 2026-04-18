<table width="100%" border="0" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">

  <tr>
    <td height="326" valign="top"><table width="100%" style="margin-top:0px" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td valign="top">
                            <div class="content_padding">
       
    
    <table cellpadding="0" cellspacing="0" border="0">
            <tr>
             <td  valign="top">
              

<table class="tbl_border">
             	
	<tr>
    	<th valign="top" >S.No.</th>
      <th valign="top" >Edit</th>
      <th valign="top" >View</th>      
        <th valign="top" >Payment <BR>Entry Id</th>
        <th valign="top" >Voucher Date</th>
        <th valign="top" >Agent Name</th>
        <th valign="top" >Supplier Name</th>
        <th valign="top" >Payment Amount</th>
        <th valign="top" >Deduction Amount</th>
        


<!--        <th>Delete</th> -->

    </tr>
    <?php 
          $search_supplier_code=$_REQUEST['search_supplier_account_code'];
          $search_agent_code=$_REQUEST['search_agent_account_code'];
          $vou_start_date=$_REQUEST['vou_start_date'];
          $vou_end_date=$_REQUEST['vou_end_date'];

?>    
<?php
$con=get_connection();



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

//$result -> free_result();

    

$sql_pay="select * from txt_commission_receipt_entry_main 
        where delete_tag='FALSE' ";
    if($search_supplier_code!=''){
        $sql_pay.=" AND supplier_account_code='$search_supplier_code' ";
    }
    
    if($search_agent_code!=''){
        $sql_pay.=" AND agent_account_code='$search_agent_code' ";
    }
    
    $sql_vou_start_date=convert_date($vou_start_date);
    if($vou_start_date!=''){
        $sql_pay.=" AND receipt_date>='$sql_vou_start_date' ";
    }
    
    $sql_vou_end_date=convert_date($vou_end_date);
    if($vou_end_date!=''){
        $sql_pay.=" AND receipt_date<='$sql_vou_end_date' ";
    }




$sql_pay.="order by receipt_date DESC, receipt_entry_id desc ";

//echo $sql_pay;     
$result=mysqli_query($con,$sql_pay);
$count=0;
while($rs=mysqli_fetch_array($result))
{
	$payment_entry_id=$rs[0];
	echo "<tr>";
  echo "<td>".++$count."</td>";
  echo "<td><a href='commission_edit_receipt_entry.php?src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code&receipt_entry_id=$payment_entry_id'><img src='../images/noun_project_edit_1.png' height='16' width='16' border='0' title='Edit' /></a></td>";
  echo "<td><a href='commission_view_receipt_entry.php?src=search&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&receipt_entry_id=$payment_entry_id'><img src='../images/noun_project_preview_1.png' height='16' width='16' border='0' title='View' /></a></td>";
  echo "<td>".$rs['receipt_entry_id']."</td>";

  echo "<td>".rev_convert_date($rs['receipt_date'])."</td>";
  
   
  $disp_supp_name=$disp_agent_name="Not Found";
  
 /*
  for($a=0;$a<sizeof($companyRow);$a++){

    if($rs['supplier_account_code']==$companyRow[$a][0]){
      $disp_supp_name=$companyRow[$a][1];
    }
    if($rs['agent_account_code']==$companyRow[$a][0]){
      $disp_agent_name=$companyRow[$a][1];
    }

    


  }
*/
//$disp_transport_name=array_search($rs['transport_name'],$company_array);
$disp_supp_name=array_search($rs['supplier_account_code'],$company_array);
$disp_agent_name=array_search($rs['agent_account_code'],$company_array);


	echo "<td>".$disp_agent_name."</td>";
  echo "<td>".$disp_supp_name."</td>";
  echo "<td>".zeroToBlank($rs['receipt_amount'])."</td>";
  echo "<td>".zeroToBlank($rs['deduction_amount'])."</td>";
  

	//echo "<td><a href='process_payment_entry.php?action=delete&payment_entry_id=$payment_entry_id' onclick='return confirm_delete();'>Delete</a></td>";
	
	echo"</tr>";
}
?>
</table>
