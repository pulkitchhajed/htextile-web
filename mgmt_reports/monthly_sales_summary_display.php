<table   border='0' >

<tr><td align='right'>
<?php if($rep_print!="OK") {?>
<input  type="button" class="form-button" onclick="excelDownLoad()" name="ls_dnload" value="Download Excel" />
<input  type="button" class="form-button" onclick="pdfDownLoad()" name="ls_dnload" value="Print" /> 
<?php }?>
<br>
</td></tr>
<?php
$head_col_span=3;


?>
<tr><td>
<table class="tbl_border_0" border='1' width='100%'>

  <?php 
  /*
    $supplier_code=$_REQUEST['supplier_account_code'];
    $buyer_code=$_REQUEST['buyer_account_code'];
    $bill_start_date=$_REQUEST['bill_start_date'];
    $bill_end_date=$_REQUEST['bill_end_date'];
    $vou_start_date=$_REQUEST['vou_start_date'];
    $vou_end_date=$_REQUEST['vou_end_date'];
    */
    $monthly_summ_report_type=$_REQUEST['monthly_summ_report_type'];

    $monthly_sales_month_type=$_REQUEST['monthly_sales_month_type'];
    $start_date=$_REQUEST['start_date'];
    $end_date=$_REQUEST['end_date'];
   

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
 // $rowcount++;

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
/*
$rep_supplier_code=$_REQUEST['supplier_account_code'];
$rep_buyer_code=$_REQUEST['buyer_account_code'];
$rep_bill_start_date=convert_date($_REQUEST['bill_start_date']);
$rep_bill_end_date=convert_date($_REQUEST['bill_end_date']);
$rep_vou_start_date=convert_date($_REQUEST['vou_start_date']);
$rep_vou_end_date=convert_date($_REQUEST['vou_end_date']);
$order=$_REQUEST['order'];
*/



$sql_pay="SELECT  ";
if ($monthly_sales_month_type=='Entry Month'){
    $sql_pay.=" vou_month ";    
    $sql_pay.=" , ";    
}

if($monthly_summ_report_type=='Supplier Name'){
  $sql_pay.=" supplier_account_code ";    
  $sql_pay.=" , ";    

}

if($monthly_summ_report_type=='Buyer Name'){
  $sql_pay.=" buyer_account_code ";    
  $sql_pay.=" , ";    

}

if ($monthly_sales_month_type=='Entry Date'){
  $sql_pay.=" voucher_date ";    
  $sql_pay.=" , ";    
}

if ($monthly_sales_month_type=='Bill Month'){
    $sql_pay.=" bill_month ";    
    $sql_pay.=" , ";    
}

if ($monthly_sales_month_type=='Bill Date'){
  $sql_pay.=" bill_date ";    
  $sql_pay.=" , ";    
}

$sql_pay.=" sum(bill_amount) as total_bill_amt  FROM 
(SELECT month(voucher_date) as vou_month, voucher_date, bill_entry_id,
  bill_number,
  month(bill_date) as bill_month,
  bill_date,
  supplier_account_code,
  buyer_account_code,
  bill_amount 
FROM txt_bill_entry 
WHERE delete_tag='FALSE' ";

$sql_end_date=convert_date($end_date);
$sql_start_date=convert_date($start_date);

   
        $sql_pay.=" AND bill_date>='$sql_start_date'";


        $sql_pay.=" AND bill_date<='$sql_end_date'";

    




//ORDER by bill_date,bill_number 
 $sql_pay.= "  ) AS t1
         ";

        if ($monthly_sales_month_type=='Entry Month'){
            $sql_pay.=" GROUP BY vou_month ";    
            
        }

        if ($monthly_sales_month_type=='Entry Date'){
          $sql_pay.=" GROUP BY voucher_date ";    
          
        }        

        if ($monthly_sales_month_type=='Bill Month'){
            $sql_pay.=" GROUP BY bill_month ";    
            
        }
        
        if ($monthly_sales_month_type=='Bill Date'){
          $sql_pay.=" GROUP BY bill_date ";    
          
        }
        
        if($monthly_summ_report_type=='Supplier Name'){
          $sql_pay.=" , supplier_account_code ";    
          
        
        }

        if($monthly_summ_report_type=='Buyer Name'){
          $sql_pay.=" , buyer_account_code ";    
          
        
        }

/*
$sql_pay="SELECT * FROM 
(SELECT bill_entry_id,
  bill_number,
  bill_date,
  supplier_account_code,
  buyer_account_code,
  bill_amount 
FROM txt_bill_entry 
WHERE delete_tag='FALSE' 
AND supplier_account_code='8' 
AND buyer_account_code='52' ) AS t1
LEFT JOIN 
(SELECT bill_entry_id AS t2_bill_entry_id ,
  payment_entry_id,
  payment_entry_vou_date,
  dis_amount,
  deduction_amount,
  bill_gr_amt,
  payment_received,
  balance_amount 
FROM txt_payment_bill_entry 
WHERE delete_tag='FALSE' ) AS t2 
ON t1.bill_entry_id=t2.t2_bill_entry_id";
*/
//$disp_buyer_name=array_search($rs['buyer_account_code'],$company_array);
//echo $sql_pay;

$result=mysqli_query($con,$sql_pay);
//echo $sql_pay;
$count=0;
//$rs=mysqli_fetch_field($result);
//while($rs=mysqli_fetch_fields($result))
//{

  /*
echo "<tr> <td colspan='11'>";
echo "--";
echo $rep_supplier_code;
echo "--";
echo $rep_buyer_code;
echo "--";
echo $rep_bill_start_date;
echo "--";
echo $rep_bill_end_date;
echo "--";
echo $rep_vou_start_date;
echo "--";
echo $rep_vou_end_date;
echo "--";
echo $sql_pay;


echo "</td></tr>";
*/

//if ($rep_xls=="OK" && $role_id=="admin"){

?>
 




<?php if ($monthly_sales_month_type=='Entry Month' || $monthly_sales_month_type=='Bill Month' ){ ?>
  <tr>
    <th colspan='<?php echo $head_col_span;?>' align='center' > Monthly Sales Summary Report 
    </th>
  </tr>
<tr>
<th valign='top' width='150' align='center' > <?php echo $monthly_sales_month_type ?> </th>

  <?php if ($monthly_summ_report_type !='None') { ?>
  <th valign='top' width='150' align='center' > <b><?php echo $monthly_summ_report_type ?> </b> </th>
  <?php } ?>  

<?php } ?>
<?php if ($monthly_sales_month_type=='Entry Date' || $monthly_sales_month_type=='Bill Date' ){ ?>
  <tr>
    <th colspan='<?php echo $head_col_span;?>' align='center' > Date Wise Sales Summary Report 
    </th>
  </tr>
  <tr>
<th valign='top' width='150' align='center' > <b><?php echo $monthly_sales_month_type ?> </b> </th>

  <?php if ($monthly_summ_report_type !='None') { ?>
  <th valign='top' width='150' align='center' > <b><?php echo $monthly_summ_report_type ?> </b> </th>
  <?php } ?>
<?php } ?>



<th valign='top' align='right' width='150'><b>Total Bill Amount</b>
</th>

</tr>



<?php


$bill_page_total=0;
//setlocale(LC_MONETARY, 'en_US');

    while($rs= mysqli_fetch_array($result)){

        echo "<tr>";

        if ($monthly_sales_month_type=='Entry Month'){
          echo "<td valign='top' align='center'  >";
         // echo rev_convert_date($rs['vou_month']);
         echo $rs['vou_month'];
          echo "</td>";       
        } 

        if ($monthly_sales_month_type=='Entry Date'){
          echo "<td valign='top' align='center' >";
         // echo rev_convert_date($rs['vou_month']);
         echo rev_convert_date($rs['voucher_date']);
          echo "</td>";       

          
        }         
        if ($monthly_sales_month_type=='Bill Month'){
            echo "<td valign='top'  align='center'>";
           // echo rev_convert_date($rs['vou_month']);
           echo $rs['bill_month'];
            echo "</td>";       
          } 

          if ($monthly_sales_month_type=='Bill Date'){
            echo "<td valign='top' align='center' >";
           // echo rev_convert_date($rs['vou_month']);
           echo rev_convert_date($rs['bill_date']);
            echo "</td>";       
          }           


          if ($monthly_summ_report_type !='None') {

                if ($monthly_summ_report_type=='Supplier Name'){
                  echo "<td valign='top' align='center' >";
                  // echo rev_convert_date($rs['vou_month']);
                  $disp_supplier_name=array_search($rs['supplier_account_code'],$Supplier_array);
                  //echo $rs['supplier_account_code'];
                  echo $disp_supplier_name;
                  echo "</td>";       
                }   

                if ($monthly_summ_report_type=='Buyer Name'){
                  echo "<td valign='top' align='center' >";
                  // echo rev_convert_date($rs['vou_month']);
                  $disp_buyer_name=array_search($rs['buyer_account_code'],$Buyer_array);
                  //echo $rs['buyer_account_code'];
                  echo $disp_buyer_name;
                  echo "</td>";       
                }  

          }

          //monthly_summ_report_type


          echo "<td valign='top' align='right' >";
          // echo rev_convert_date($rs['vou_month']);
          if( $rep_print=="xls" ){ 
           echo zeroToBlank(number_format($rs['total_bill_amt'],2,'.',''));
           
          } else {
            //echo zeroToBlank(number_format($rs['total_bill_amt'],2,'.',''));
            // echo number_format($rs['total_bill_amt'],2);
            echo preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,",number_format($rs['total_bill_amt'],2,'.',''));
            
          }
          $bill_page_total+=$rs['total_bill_amt'];
           echo "</td>";   
        
        }   




    


    if( $rep_print=="xls" ){ 

    echo "<tr> <td  align='Right' ><b>Gross Total</b></td> ";

    if ($monthly_summ_report_type !='None') {
      echo "<td></td>";
    }

    echo "<td align='Right' ><b>".zeroToBlank(number_format($bill_page_total,2,'.',''))."</b></td>
   
    </tr>";
    }else{
      echo "<tr> <td  align='Right' ><b>Gross Total</b></td> ";

      if ($monthly_summ_report_type !='None') {
        echo "<td></td>";
      }
      echo "<td align='Right' ><b>".preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,",number_format($bill_page_total,2,'.',''))."</b></td>
     
      </tr>";
    }





    


?>


</table>

</td></tr></table>