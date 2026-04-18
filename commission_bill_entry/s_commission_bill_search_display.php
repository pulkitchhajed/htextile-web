




<table  border="0" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">

  <tr>
    <td height="326" valign="top"><table  style="margin-top:0px" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td valign="top">
                            <div class="content_padding">
        
    
    <table cellpadding="0" cellspacing="0" border="0">
            <tr>
             <td width="100%" height="138" valign="top">
              

<table class="tbl_border" width="100%" >
	<tr>
      <th width="24">S.No.</th>
      <th width="27">Edit</th>      
      <th width="27">View</th>            
      <th width="50">Bill Id</th>      

      <th width="54">Bill Number</th>
      <th width="100">Bill Date</th>
      <th width="54"> Commission Year </th>
      <th width="54"> Commission Type </th>
      <th width="250">Supplier </th>
      <th width="250">Agent </th>

      <th width="80">Commission Amount Bill Wise</th>
      <th width="50">Commission Amount Payment Wise</th>

      
      


<!--        <th width="50">Delete</th> -->
    </tr>
<?php 
$search_supplier_code=$_REQUEST['search_supplier_account_code'];
$search_agent_code=$_REQUEST['search_agent_account_code'];
$bill_start_date=$_REQUEST['bill_start_date'];
$bill_end_date=$_REQUEST['bill_end_date'];
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
$Agent_array=array("Value"=>"Key"); 
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
    
    
    if($rs['firm_type']=='Agent'){
    
      //$agentRow[$rowcount][0]=$rs['company_id'];
      //$agentRow[$rowcount][1]=$rs['firm_name'];
      $buy_array=array($rs['firm_name']=>$rs['company_id']);
      $Agent_array=array_merge($Agent_array,$buy_array);  
      
    }  
  
/*
  echo $companyRow[$rowcount][0];
  echo $companyRow[$rowcount][1];
*/
  $rowcount++;

}

$result -> free_result();
/*
echo"<BR>";
echo"<BR>";
echo"<BR>";
echo"<BR>";
echo"<BR>";


echo"<BR>";
echo sizeof($companyRow);
echo " Saumya ";
for($a=0;$a<sizeof($companyRow);$a++){
  echo $companyRow[$a][0];
  echo $companyRow[$a][1];
}

echo"<BR>";
echo sizeof($companyRow);
echo " Vagmi ";
*/







$sql="select * from txt_commission_bill_entry 
where delete_tag='FALSE' ";
if($search_supplier_code!=''){
    $sql.=" AND supplier_account_code='$search_supplier_code' ";
}

if($search_agent_code!=''){
    $sql.=" AND agent_account_code='$search_agent_code' ";
}

$sql_bill_start_date=convert_date($bill_start_date);
if($bill_start_date!=''){
    $sql.=" AND commission_bill_date>='$sql_bill_start_date' ";
}

$sql_bill_end_date=convert_date($bill_end_date);
if($bill_end_date!=''){
    $sql.=" AND commission_bill_date<='$sql_bill_end_date' ";
}




// Entry Date
$sql_order_by=' commission_bill_date DESC ,commission_bill_entry_id DESC';


$sql.=" ORDER BY $sql_order_by ";

//echo $sql;
$result=mysqli_query($con,$sql);
$count=0;
while($rs=mysqli_fetch_array($result))
{
	$bill_entry_id=$rs[0];
  echo "<tr>";
  
  echo "<td>".++$count."</td>";
  echo "<td><a href='commission_edit_bill_entry.php?src=search&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code&commission_bill_entry_id=$bill_entry_id'><img src='../images/noun_project_edit_1.png' height='16' width='16' border='0' title='Edit' /></a></td>";
  echo "<td><a href='commission_view_bill_entry.php?src=search&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code&commission_bill_entry_id=$bill_entry_id'><img src='../images/noun_project_preview_1.png' height='16' width='16' border='0' title='View' /></a></td>";  
  echo "<td>".$rs['commission_bill_entry_id']."</td>";

  $bill_num=$rs['commission_bill_number'];
	echo "<td>".$rs['commission_bill_number']."</td>";
	echo "<td>".rev_convert_date($rs['commission_bill_date'])."</td>";

  
  $disp_transport_name=$disp_supp_name=$disp_agent_name=$disp_agent_name="Not Found";
  
  /*
  for($a=0;$a<sizeof($companyRow);$a++){
    if($rs['transport_name']==$companyRow[$a][0]){
      $disp_transport_name=$companyRow[$a][1];
    }
    if($rs['supplier_account_code']==$companyRow[$a][0]){
      $disp_supp_name=$companyRow[$a][1];
    }
    if($rs['agent_account_code']==$companyRow[$a][0]){
      $disp_agent_name=$companyRow[$a][1];
    }
    if($rs['agent']==$companyRow[$a][0]){
      $disp_agent_name=$companyRow[$a][1];
    }

  }
  */

  //$disp_transport_name=array_search($rs['transport_name'],$company_array);
  
  $disp_supp_name=array_search($rs['supplier_account_code'],$Supplier_array);
  $disp_agent_name=array_search($rs['agent_account_code'],$Agent_array);

	//echo "<td>".$disp_transport_name."</td>";
  echo "<td>".$rs['commission_year']."</td>";
	echo "<td>".$rs['commission_mode']."</td>";
  echo "<td>".$disp_supp_name."</td>";
  //echo "<td>".$rs['supplier_account_code']."</td>";
  echo "<td>".$disp_agent_name."</td>";
  //echo "<td>".$rs['agent_account_code']."</td>";


  echo "<td align='right'>".$rs['commission_amt_bill']."</td>";
  echo "<td align='right'>".$rs['commission_amt_pay']."</td>";
  
  
	
	//echo "<td><a href='process_bill_entry.php?action=delete&bill_entry_id=$bill_entry_id' onclick='return confirm_delete($bill_num,\"$disp_supp_name\");'>Delete</a></td>";
	echo"</tr>";
}

?>
</table>
