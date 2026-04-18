<table  class="tbl_border" >

<tr><td align='right'>
<?php if(isset($rep_print)){
		if($rep_print!="OK") {?>	
<input  type="button" class="form-button" onclick="excelDownLoad()" name="ls_dnload" value="Download Excel" />
<input  type="button" class="form-button" onclick="pdfDownLoad()" name="ls_dnload" value="Print" /> 
<?php } 
}?>
<br>
</td></tr>

<tr><td>
<table border='1'>
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

//$rep_buyer_code=$_REQUEST['buyer_group_code'];
//$disp_buyer_code=array_search($rep_buyer_code,$company_array);//

$buy_os_report_type=$_REQUEST['buy_os_report_type'];
$rep_start_date=convert_date($_REQUEST['start_date']);
$rep_end_date=convert_date($_REQUEST['end_date']);
$start_date=$_REQUEST['start_date'];
$end_date=$_REQUEST['end_date'];
//$GST_DISP=$_REQUEST['GST'];

//if($comm_report_type=="Bill Wise"){
if(true){

	$sql_comm_bill=" SELECT bill_buyer_name,
							bill_buy_group_name,
							bill_buy_company_id,
							total_bill_amount,
							total_discount,
							total_gr,
							total_payment AS total_bill_payment,
                            (total_bill_amount - IFNULL(total_discount,0) - IFNULL(total_gr,0) - IFNULL(total_payment,0)) as total_os,
							pay_buy_company_id,
							buy_comm,agent_id,city
					FROM 
					(SELECT buy_firm_name AS bill_buyer_name , 
							buy_group_name AS bill_buy_group_name,
							buy_company_id AS bill_buy_company_id,
							sum(bill_amount ) AS total_bill_amount,
							AVG(comm_per) AS buy_comm ,
							agent_id,city
					FROM txt_bill_entry ,( SELECT buy.company_id AS buy_company_id,
												  buy.firm_name AS buy_firm_name ,
												  buy.agent_id AS agent_id, city,
												  buy.commission_percentage AS comm_per,
												  grp.group_name AS buy_group_name
											FROM txt_company AS buy ,txt_group_master AS grp
											WHERE buy.delete_tag='FALSE' 
											AND firm_type='Buyer'
											AND grp.group_id=buy.group_id ";
											/*
											if($rep_buyer_code!='') {
												$sql_comm_bill.= " and buy.group_id='$rep_buyer_code' ";
											}
											*/

							$sql_comm_bill.= " ORDER BY firm_name) AS Buyer 
					WHERE delete_tag='FALSE' ";
/*					
		if($rep_buyer_code!='') {
					$sql_comm_bill.=" AND  buyer_account_code IN ( SELECT company_id 
												from txt_company 
												Where delete_tag='FALSE' 
												and firm_type='Buyer'
												and group_id='$rep_buyer_code') ";
		}  
*/		
	$sql_comm_bill.= " AND buy_company_id=buyer_account_code ";
		if($start_date!=''){
			$sql_comm_bill.=" AND bill_date>='$rep_start_date' ";
		}
		if($end_date!=''){
			$sql_comm_bill.=" AND bill_date<='$rep_end_date' ";
		}
	$sql_comm_bill.=" GROUP BY buy_firm_name ,buy_company_id,buy_group_name 
					ORDER BY buy_firm_name ,buy_company_id,buy_group_name  ) AS t1 

					LEFT JOIN 
					(SELECT SUM(PAY_BILL.dis_amount + PAY_BILL.deduction_amount ) AS total_discount, 
					SUM(PAY_BILL.bill_gr_amt) AS total_gr, 
					SUM(PAY_BILL.payment_received) AS total_payment, 
					buyer_account_code AS pay_buy_company_id
			FROM txt_payment_entry_main AS MAIN ,txt_payment_bill_entry AS PAY_BILL
			
			WHERE MAIN.delete_tag='FALSE'   AND PAY_BILL.delete_tag='FALSE'   
			AND MAIN.payment_entry_id=PAY_BILL.payment_entry_id ";

			if($start_date!=''){
				$sql_comm_bill.=" AND bill_date>='$rep_start_date' ";
			}
			if($end_date!=''){
				$sql_comm_bill.=" AND bill_date<='$rep_end_date' ";
			}
			
			
			  
	  
/*					
					if($rep_buyer_code!='') {
						$sql_comm_bill.=" AND  buyer_account_code IN ( SELECT company_id 
													from txt_company 
													Where delete_tag='FALSE' 
													and firm_type='Buyer'
													and group_id='$rep_buyer_code') ";
					}  				  
*/					
		$sql_comm_bill.=" GROUP BY MAIN.buyer_account_code  
		ORDER BY MAIN.buyer_account_code  ) AS t2 

					ON bill_buy_company_id=pay_buy_company_id ORDER BY ";
		if ($buy_os_report_type == "Buyer Group") { 
			$sql_comm_bill.="  bill_buy_group_name ";
		}
		if ($buy_os_report_type == "Buyer Name") { 
			$sql_comm_bill.="  bill_buyer_name ";
		}
		if ($buy_os_report_type == "Outstanding Amount") { 
			$sql_comm_bill.="  total_os DESC";
		}	
		if ($buy_os_report_type == "Sales Amount") { 
			$sql_comm_bill.="  total_bill_amount DESC ";
		}	
		if ($buy_os_report_type == "GR Amount") { 
			$sql_comm_bill.="  total_gr DESC ";
		}	
			


	//echo $sql_comm_bill;
	$result=mysqli_query($con,$sql_comm_bill);
	comm_log("SQL COMM BILL");
	comm_log($sql_comm_bill);
	$count=0;

	?>

	<tr><th colspan='9'><h3>Buyer Outstanding Summary Report  </h3></th></tr>
	<tr><th colspan='9'>(Order by <?php echo $buy_os_report_type ?>)  </th></tr>
	<tr><th colspan='9'><b><?php echo " From ".$start_date." To ".$end_date ?></b></th></tr>

	<tr>

	<td>S.No.
	</td>
	<td> City</td>
	<td>Group</td>
	<td>Buyer Name</td>

	<td align='right'>Total Bill Amount
	</td>

	<td align='right' >Total Discount
	</td>
	<td align='right' >Total GR 
	</td>
	<td align='right'>Total Payment
	</td>



	<td align='right'>Outstanding Amount
	</td>

	

	</tr>

	<?php
	$sno=0;
	$page_total_bill_amt=$page_total_discount=$page_total_bill_payment=$page_total_os_bill_amt=0;
	$page_total_comm_less_gst=$page_total_comm_amt=$page_total_gr=$page_total_net_bill_amt=0;
	$page_bill_total_payment=0;
	

	while($rs= mysqli_fetch_array($result)){

		$sno++;

		echo "<tr>";

		echo "<td>";
		echo $sno;
		echo "</td>";


		////echo array_search(8,$company_array); 


		echo "<td>";
		echo $rs['city'];

		echo "</td>";

		
		echo "<td>";
		echo $rs['bill_buy_group_name'];
		echo "</td>";

		echo "<td>";
		echo $rs['bill_buyer_name'];
		echo "</td>";

		echo "<td align='right' >";
		echo zeroToBlank(number_format($rs['total_bill_amount'],2,'.',''));
		echo "</td>";	
		$page_total_bill_amt +=$rs['total_bill_amount'];

		echo "<td align='right' >";
		echo zeroToBlank(number_format($rs['total_discount'],2,'.',''));
		echo "</td>";	
		$page_total_discount+=$rs['total_discount'];

		echo "<td align='right' >";
		echo zeroToBlank(number_format($rs['total_gr'],2,'.',''));
		echo "</td>";	
		$page_total_gr+=$rs['total_gr'];

		echo "<td align='right' >";
		echo zeroToBlank(number_format($rs['total_bill_payment'],2,'.',''));
		echo "</td>";	
		$page_bill_total_payment+=$rs['total_bill_payment'];


		// net Bill amount is Outstanding bill amount
		$os_amount=($rs['total_bill_amount']-$rs['total_discount']-$rs['total_gr'] -$rs['total_bill_payment']);
		echo "<td align='right' >";
		echo zeroToBlank(number_format($os_amount,2,'.',''));
		/*
		echo "--";
		echo $rs['total_os'];
		echo "--";
		*/
		echo "</td>";
		$page_total_os_bill_amt+=$os_amount;


		//$comm_amt=(($net_bill_amount*$rs['buy_comm'])/100);

		//$page_total_comm_amt+=$comm_amt;


		//$comm_amt_less_gst=(($comm_amt/(100+$GST_DISP))*100);
		//$comm_amt_less_gst=$comm_amt*((100-$GST_DISP)/100);
	
		//$page_total_comm_less_gst+=$comm_amt_less_gst;

		echo "</tr>";

	} //end While

	echo "<tr>";
	echo "<td align='right' colspan='4'> Total </td>";
	echo "<td align='right' >".zeroToBlank(number_format( $page_total_bill_amt,2,'.',''))." </td>";
	echo "<td align='right' > ".zeroToBlank(number_format($page_total_discount,2,'.',''))." </td>";
	echo "<td align='right' >".zeroToBlank(number_format($page_total_gr,2,'.',''))." </td>";
	echo "<td align='right' >".zeroToBlank(number_format( $page_bill_total_payment,2,'.','')) ."</td>";

	echo "<td align='right' > ".zeroToBlank(number_format($page_total_os_bill_amt,2,'.',''))." </td>";
	
	echo "</tr>";
	
} // End if($comm_report_type=="Bill Wise")



?>

</table>

</td></tr></table>