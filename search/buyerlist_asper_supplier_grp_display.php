<table   border='0' >

<tr><td align='right'>
<?php if($rep_print!="OK") {?>
<input  type="button" class="form-button" onclick="excelDownLoad()" name="ls_dnload" value="Download Excel" />
<input  type="button" class="form-button" onclick="pdfDownLoad()" name="ls_dnload" value="Print" /> 
<?php }?>
<br>
</td></tr>
<?php

//if($rep_xls=="OK" && $role_id=="admin"){


?>
<tr><td>
<table class="tbl_border_0" border='1' width='100%'>

  <?php 
    $supplier_code=$_REQUEST['supplier_group_id'];
    
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

$con=get_connection();



$sql="SELECT comp.group_id AS GROUP_ID,grp.group_name AS GROUP_NAME,comp.firm_name AS FIRM_NAME,comp.company_id AS comp_id ,comp.city AS CITY FROM txt_group_master AS grp, txt_company AS comp WHERE comp.delete_tag='FALSE' AND grp.delete_tag='FALSE' AND comp.group_id=grp.group_id 

ORDER BY comp.company_id ASC";
$result=mysqli_query($con,$sql);

$rowcount=0;
// Creating Group Array with reverse Key Value Pair 
// because array_search function searched value and returns key
// first value Dummy to show the position of details
$group_array=array("Value"=>"Key"); 
while($rs=mysqli_fetch_array($result))
{
  //$groupRow[$rowcount][0]=$rs['comp_id'];
  //$groupRow[$rowcount][1]=$rs['GROUP_NAME'];
  $grp_com_name=$rs['GROUP_NAME'].",".$rs['CITY'].",".$rs['FIRM_NAME'];
  $grp_array=array($grp_com_name=>trim($rs['comp_id']));
  $group_array=array_merge($group_array,$grp_array);
  //xls_report_log($rs['GROUP_NAME']);
  //xls_report_log($rs['comp_id']);

//  echo $companyRow[$rowcount][0];
//  echo $companyRow[$rowcount][1];

  $rowcount++;
}

?>
    
<?php

//echo array_search(8,$company_array);

$key='name';
$val='Pritesh Shah';

$a1=array('id'=>5678);
$a2=array('first'=>'Pritesh');
$a3=array($key=>$val);
$a10=array_merge($a1,$a2,$a3);

//echo "--";
//print_r( $a10);
//echo "--";
//echo array_search($val,$a10);
//echo "--";

$rep_supplier_code=$_REQUEST['supplier_group_id'];

$rep_bill_start_date=convert_date($_REQUEST['bill_start_date']);
$rep_bill_end_date=convert_date($_REQUEST['bill_end_date']);



$sql_pay="SELECT *
FROM (SELECT
        supplier_account_code,
        Supplier.comp_firm_name    AS supp_firm_name,
        buyer_account_code,
        Buyer.comp_firm_name       AS buy_firm_name,
        Supplier.comp_group_id     AS Supp_grp_id,
        Buyer.comp_group_id        AS Buy_grp_id,
        Supplier.master_group_name     AS Supp_grp_name,
        Buyer.master_group_name        AS Buy_grp_name        
        
      FROM txt_bill_entry,
        view_supplier_with_grp_name AS Supplier,
        view_buyer_with_grp_name AS Buyer
      WHERE txt_bill_entry.delete_tag = 'FALSE' ";

if($rep_supplier_code!=''){
  $sql_pay.=" AND Supplier.comp_group_id='$rep_supplier_code' ";
  
}

$sql_pay.="AND txt_bill_entry.supplier_account_code = Supplier.comp_company_id ";
$sql_pay.="AND txt_bill_entry.buyer_account_code = Buyer.comp_company_id ";

//           AND Supplier.comp_group_id = '3'

if($rep_bill_start_date!=''){
  $sql_pay.=" AND txt_bill_entry.bill_date>='$rep_bill_start_date'";
}
if($rep_bill_end_date!=''){
  $sql_pay.=" AND txt_bill_entry.bill_date<='$rep_bill_end_date'";
}



//ORDER by bill_date,bill_number 
 $sql_pay.= "       GROUP BY supp_grp_name,buy_grp_name
 ) T1 ";



/*   *******************   New Query  - 4-2-24  *******************

SELECT *
FROM (SELECT
        supplier_account_code,
        Supplier.comp_firm_name    AS supp_firm_name,
        buyer_account_code,
        Buyer.comp_firm_name       AS buy_firm_name,
        Supplier.comp_group_id     AS Supp_grp_id,
        Buyer.comp_group_id        AS Buy_grp_id,
        Supplier.master_group_name     AS Supp_grp_name,
        Buyer.master_group_name        AS Buy_grp_name        
        
      FROM txt_bill_entry,
        view_supplier_with_grp_name AS Supplier,
        view_buyer_with_grp_name AS Buyer
      WHERE txt_bill_entry.delete_tag = 'FALSE'
          AND Supplier.comp_group_id = '3'
          AND txt_bill_entry.supplier_account_code = Supplier.comp_company_id
          AND txt_bill_entry.buyer_account_code = Buyer.comp_company_id

          AND txt_bill_entry.bill_date <= '2024-03-31'
          AND txt_bill_entry.bill_date >= '2022-03-31'
      GROUP BY supp_firm_name,buy_firm_name
     ) T1 
      



*/


//$disp_buyer_name=array_search($rs['buyer_account_code'],$Buyer_array);
//echo $sql_pay;
// $Supplier_array
$result=mysqli_query($con,$sql_pay);
//echo $sql_pay;
$count=0;

//$disp_supplier_name=array_search($_REQUEST['supplier_group_id'],);



    $header=true;
    while($rs= mysqli_fetch_array($result)){
      if($header){

              
      echo "<tr>";
      echo "<th align='Left'>";  
      echo "Buyer List   for " .$rs['Supp_grp_name'];
      echo "</th>";
      echo "</tr>";

        $header=false;
      }
      
      echo "<tr>";
          echo "<td align='Left'>";  
          echo $rs['Buy_grp_name'];
          echo "</td>";
          echo "</tr>";
        }






?>

</table>

</td></tr></table>