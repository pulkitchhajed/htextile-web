<?php
include("../includes/check_session.php");
include("../includes/config.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Commission Edit Receipt Entry</title>
<link rel="icon" type="image/x-icon" href="<?php echo $web_path; ?>images/dt-favicon.ico" />
<link href="<?php echo $web_path; ?>css/style.css" rel="stylesheet" type="text/css" />


<style>

*
{
	margin:0;
	padding:0;
}


table {
  text-align: left;

  border-collapse: collapse;  
}

.th-sticky {
  background: white;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.tbl_border  {
  border:1px solid #cccccc; 
  border-collapse:collapse; 
	padding: 1px 4px;
  margin:0px;
}

.tbl_border td {
  border:1px solid #cccccc; 
  border-collapse:collapse; 
	padding: 10px 10px;
	padding: 0.25rem;
}

.tbl_border th {
  border:1px solid #cccccc;
  background-color:#F5F5F5;   
  border-collapse:collapse; 
  padding: 1px 4px;
  margin:0px;
  padding: 0.25rem;
}	

.table_scroll
{	
	overflow:auto;
	display:block;
	position:relative;
	overflow-x:hidden;
}

.table_scroll_h
{	
	overflow:auto;
	display:block;
}
	 
</style>	

<script type="text/javascript" src="../js/commisssion_receipt_entry.js"></script>
<script type="text/javascript" src="../js/dateCheck.js"></script>

<script type="text/javascript">
        window.onbeforeunload = function () {
            var inputs = document.getElementsByTagName("INPUT");
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].type == "button" || inputs[i].type == "submit") {
                    inputs[i].disabled = true;
                }
            }
        };
    </script>

<script type="text/javascript">

function finalCheck(){
	// this is just a place holder it will be implemented later
	//l_header_values=check();
	//l_chq_gr_values=checkMinOneChqGRDetail();
	//l_bill_details=checkMinOneBillHasValue();
	calculateDifference();
	l_receipt_type_last_value=document.getElementById('receipt_type_last_value').value;
	l_receipt_type_selected=document.getElementById('receipt_type').value;
	//alert( 'Final Check');
	
	if((l_receipt_type_selected=="Advance Receipt" )  ){
		if(l_receipt_type_last_value==l_receipt_type_selected){
			if(check()){
				return true;
			}else{
				return false;
			}

		
		}else{
			alert ("Changing to Advance Receipt");			
			return true;			
			/*
			alert('Please Click Next and populate Receipt Again')
			document.getElementById('narration').focus();
			return false;
		*/			
		}
	}else {
		if(check()){
				if(checkMinOneBillHasValue()){ // Current work
					//alert("Test one");
					if(balanceLessThenZero()){
						//alert("Test Two");
						if(checkBillPaymentTypeSelected()){
							//alert("Test Three");
							if(checkAllDifference()){
								//alert("Test Four");
								return true;
							}else{
								return false;
							}
						
						}else{
							return false;
						}
					}else{
						return false;
					}

				}else{
					return false;
				}
		}else{
			return false;
		}
	}
	
	//alert(l_header+l_chq_gr_values+l_bill_details);
	
	/*
	if(l_header && l_chq_gr_values && l_bill_details ){
		return true;
	}else{
		return false;
	}
	*/	
}

function check()
{

	var receipt_date=document.getElementById("receipt_date").value;
		if(receipt_date=="") {
			alert("Please Enter Receipt Date");
			document.getElementById("receipt_date").focus();
			return false;

		}

	var receipt_type=document.getElementById("receipt_type").value;
		if(receipt_type=="") {
			alert("Please Enter Receipt Type");
			document.getElementById("receipt_type").focus();
			return false;

		}

	var receipt_mode=document.getElementById("receipt_mode").value;
		if(receipt_mode=="") {
			alert("Please Select Receipt Mode");
			document.getElementById("receipt_mode").focus();
			return false;

		}



	var agent_account_code=document.getElementById("agent_account_code").value;
		if(agent_account_code=="") {
			alert("Please Select Agent ");
			document.getElementById("agent_account_code").focus();
			return false;

		}



	var supplier_account_code=document.getElementById("supplier_account_code").value;
		if(supplier_account_code=="") {
			alert("Please Select Supplier ");
			document.getElementById("supplier_account_code").focus();
			return false;

		}

		if(receipt_type=="Receipt" || receipt_type=="Advance Receipt"){
			var receipt_amount=document.getElementById("receipt_amount").value;
				if(receipt_amount=="" || receipt_amount==0){
					alert ("Please enter Receipt Amount");
					document.getElementById("receipt_amount").focus();
					return false;
				}
		}else{
			var receipt_amount=document.getElementById("receipt_amount").value;
				if(receipt_amount!="" || receipt_amount!=0){
					alert ("Receipt Amount Should be 0 ");
					document.getElementById("receipt_amount").focus();
					return false;
				}
			var deduction_amount=document.getElementById("deduction_amount").value;				
				if(deduction_amount=="" || deduction_amount==0){
					alert ("Please enter Deduction Amount");
					document.getElementById("deduction_amount").focus();					
					return false;
				}
		}		

	return true;
}

function final_submit() {
	//alert("in final submit mode");
	//document.getElementById('final_btn').disabled=true;
	if(finalCheck()) {
		//alert('Final Check 0');
		//alert('Final Check 0 1');
		l_vou_type_selected=document.getElementById('receipt_type').value;
		//alert('Final Check 0 2'+l_vou_type_selected );
		if((l_vou_type_selected!="Advance Receipt" )  ){
			//alert('Final Check 1');
			document.getElementById('total_amount_received').disabled=false;
			document.getElementById('total_deduction').disabled=false;
			document.getElementById('amount_received_difference').disabled=false;
			document.getElementById('discount_difference').disabled=false;			

			//alert('Hello');
			//alert('Final Check 2');
			var l_commission_bill_entry_id_array=document.getElementsByName('commission_bill_entry_id[]');
			//var l_bill_voucher_date_array=document.getElementsByName('bill_voucher_date[]');
			var l_commission_year_array=document.getElementsByName('commission_year[]');
			l_size=l_commission_year_array.length;
			var l_commission_mode_array=document.getElementsByName('commission_mode[]');
			var l_commission_amt_bill_array=document.getElementsByName('commission_amt_bill[]');
			var l_commission_amt_pay_array=document.getElementsByName('commission_amt_pay[]');
			var l_adj_amt_array=document.getElementsByName('adj_amt[]');
			var l_adj_dis_array=document.getElementsByName('adj_dis[]');
			var l_bill_bal_amt_array=document.getElementsByName('bill_bal_amt[]');
			//alert('Final Check 3');
			//alert(l_size);
			for(bn=0;bn<l_size;bn++){
				//alert(bn);
				l_commission_bill_entry_id_array[bn].disabled=false;
				//l_bill_voucher_date_array[bn].disabled=false;
				l_commission_year_array[bn].disabled=false;
				l_commission_mode_array[bn].disabled=false;
				l_commission_amt_bill_array[bn].disabled=false;
				l_commission_amt_pay_array[bn].disabled=false;
				l_adj_amt_array[bn].disabled=false;
				l_adj_dis_array[bn].disabled=false;
				l_bill_bal_amt_array[bn].disabled=false;
			}
			//alert(l_size+"Hello");
			//alert('Final Check 4');
		}
		//alert('Final Check 4 1');
		if(document.getElementById('receipt_type_tag').value=="FALSE"){
			alert ("Receipt Type Changed ");
		}else if(document.getElementById('agent_tag').value=="FALSE"){
			alert ("Agent Changed ");
		}else if(document.getElementById('supplier_tag').value=="FALSE"){
			alert ("Supplier Changed ");
		}else{
			// commented for testing Java Script Validation
			//alert("Submit");
			
			document.getElementById('payment_vou').action='process_commission_receipt_entry.php?action=modify';
			document.getElementById('payment_vou').submit();
			document.getElementById('final_btn').disabled=true;
			
			

		}
		//alert('Final Check 5 1');
		document.getElementById('final_btn').disabled=false;	
	}
}



function pay_delete(){
	if(confirm("Do you want to delete")){
		l_narration=document.getElementById('narration').value;
		l_hidden_narration=document.getElementById('hidden_narration').value;

		if(l_narration==l_hidden_narration){
			alert ('Please Mention the Reason of Delete in Narration');
			document.getElementById("narration").focus();

		}else{
			document.getElementById('payment_vou').action='process_commission_receipt_entry.php?action=delete';
			document.getElementById('payment_vou').submit();
		}	
	}

}

</script>
<?php include("../includes/jQDate.php"); ?>	
</head>	

<?php

$con=get_connection();

$recpt_ent_id=$_REQUEST['receipt_entry_id'];
//echo $pay_ent_id;

$sql_main="SELECT * FROM txt_commission_receipt_entry_main where receipt_entry_id='$recpt_ent_id'";
//echo $sql_main;

$main_result=mysqli_query($con,$sql_main);
$main_rs=mysqli_fetch_array($main_result);



?>

<?php

	$con=get_connection();

	$receipt_entry_id=$main_rs['receipt_entry_id'];
	$receipt_type=$main_rs['receipt_type'];
	$receipt_date=$main_rs['receipt_date'];
	$receipt_mode=$main_rs['receipt_mode'];
	$supplier=$main_rs['supplier_account_code'];
	$agent=$main_rs['agent_account_code'];
	$receipt_amount=$main_rs['receipt_amount'];
	$bank_name=$main_rs['bank_name'];
	$cheque_number=$main_rs['cheque_number'];
	$deduction_amount=$main_rs['deduction_amount'];

	$narration=$main_rs['narration'];


	$req_disp="";
	if(isset($_REQUEST['disp'])){
		$req_disp=$_REQUEST['disp'];
	}
	$req_src="";
	if(isset($_REQUEST['src'])){
		$req_src=$_REQUEST['src'];
	}
?>


<?php
	$con=get_connection();
/*
	$receipt_date="";
	if(isset($_REQUEST['receipt_date'])){
		$receipt_date=$_REQUEST['receipt_date'];
	}

	if($receipt_date==""){
		$receipt_date=	date("d-m-Y");
	}
	
	$receipt_type="";
	if(isset($_REQUEST['receipt_type'])){
		$receipt_type=$_REQUEST['receipt_type'];
	}

	$receipt_mode="";
	if(isset($_REQUEST['receipt_mode'])){
		$receipt_mode=$_REQUEST['receipt_mode'];
	}	

	$supplier="";
	if(isset($_REQUEST['supplier_account_code'])){
		$supplier=$_REQUEST['supplier_account_code'];
	}

	$agent="";
	if(isset($_REQUEST['agent_account_code'])){
		$agent=$_REQUEST['agent_account_code'];
	}

	$receipt_amount="";
	if(isset($_REQUEST['receipt_amount'])){
		$receipt_amount=$_REQUEST['receipt_amount'];
	}

	$bank_name="";
	if(isset($_REQUEST['bank_name'])){
		$bank_name=$_REQUEST['bank_name'];
	}

	$cheque_number="";
	if(isset($_REQUEST['cheque_number'])){
		$cheque_number=$_REQUEST['cheque_number'];
	}

	$deduction_amount="";
	if(isset($_REQUEST['deduction_amount'])){
		$deduction_amount=$_REQUEST['deduction_amount'];
	}

	$narration="";
	if(isset($_REQUEST['narration'])){
		$narration=$_REQUEST['narration'];
	}

	*/
	
?>
<table width="100%" border="0" align="center" style="border:0px solid #e5f1f8;background-color:#FFFFFF">
	<tr>
    	<td><?php include("../includes/header.php"); ?></td>
  	</tr>
  	<tr>
    	<td><?php include("../includes/menu.php"); ?></td>
  	</tr>
  	<tr>
    	<td  valign="top">
			<table width="100%" style="margin-top:0px" align="center" cellpadding="0" cellspacing="0" border="0">
            	<tr>
                    <td valign="top">
                        <div class="content_padding">
                        <div class="content-header">
						<?php 	if($req_disp!='child'){ 
										if($req_src=='search') { 
											$search_supplier_code=$_REQUEST['search_supplier_account_code'];
											$search_agent_code=$_REQUEST['search_agent_account_code'];
											$vou_start_date=$_REQUEST['vou_start_date'];
											$vou_end_date=$_REQUEST['vou_end_date'];
											//src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&buyer_account_code=$buyer_code&supplier_account_code=$supplier_code&bill_entry_id=$bill_entry_id
											echo "<a href='s_commission_receipt_search.php?src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code'>Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
											
											} else {								
												echo ' <a href="index.php">Back</a>'; 
											}			
												
									}?>
                         
            				<table width="100%">
								<tr>								
									<td width="25%" align='left'>
										<h3>Commission Edit Receipt Entry :</h3>
									</td>
									<td align="center" width="30%">
                    					<?php
											if(isset($_SESSION['msg'])) {
                    							echo $_SESSION['msg'];
                    							$_SESSION['msg']='';
											}
										?>
									</td>								
									<td width="45%" align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</td>
            					</tr>
            				</table>
            			</div>
    
    				<table cellpadding="0" cellspacing="0" border="0">
            			<tr>
             				<td width="100%"  valign="top">
    							<form method="post" id="payment_vou" enctype="multipart/form-data">
    
							    <table width="850" class="tbl_border">	

	<!-- Formatting Stopped  -->

	<tr>

		<th  align="center" >Receipt Id </th> 

		<td colspan='2'>
		<input type="hidden" name="receipt_entry_id" id="receipt_entry_id"  value="<?php echo $receipt_entry_id; ?>" >
			<?php echo $receipt_entry_id; ?>
		</td>
	</tr>
	<tr>
		<td colspan='3' > </td>
	</tr>
	<tr>
		
		<th  align="center" >Receipt Date <span class="astrik">*</span></th>
		<th align="center" >Receipt Type<span class="astrik">*</span></th>
		<th align="center" >Receipt Mode<span class="astrik">*</span></th>
	</tr>
	<tr>
        
			
		<td align="center" ><input type="text" onChange="validatedate_format(this)"  name="receipt_date" class="datepick" id="receipt_date" value="<?php echo rev_convert_date($receipt_date);?>" size="8"></td>
		
		<td align="center" >

		<input type="hidden" name="receipt_type_tag" id="receipt_type_tag"  value="" >
		<input type="hidden" name="receipt_type_last_value" id="receipt_type_last_value"  value="<?php echo $receipt_type;?>" >
		<select  name="receipt_type" id="receipt_type">
        		<option value="">--Select--</option>
				<?php
				

				$arr=array('Receipt','Advance Receipt','TDS','Misc Less');

				foreach($arr as $v)
				{
					$selected="";
					if($v==$receipt_type){
						$selected="selected";
					}
					echo "<option $selected >".$v."</option>";
				}
				?>
        	</select>

		</td>

		<td align="center" >

		<input type="hidden" name="receipt_mode_tag" id="receipt_mode_tag"  value="" >
		<input type="hidden" name="receipt_mode_last_value" id="receipt_mode_last_value"  value="<?php echo $receipt_mode;?>" >
		<select  name="receipt_mode" id="receipt_mode">
        		<option value="">--Select--</option>
				<?php
				

				$arr=array('Tentative','Final');

				foreach($arr as $v)
				{
					$selected="";
					if($v==$receipt_mode){
						$selected="selected";
					}
					echo "<option $selected >".$v."</option>";
				}
				?>
        	</select>

		</td>		

		


    </tr>
   
    <tr>
		<th align="center" >Agent  <span class="astrik">*</span></th>
		<th colspan='2' align="center">Supplier <span class="astrik">*</span></th>
		
	</tr>

		<td align="center" >
		<input type="hidden" name="agent_tag" id="agent_tag"  value="" >
		<input type="hidden" name="agent_last_value" id="agent_last_value"  value="<?php echo $agent;?>" >
        	<select disabled name="agent_account_code" id="agent_account_code" >
            	<option value="">--Select--</option>
                <?php
					$s_sql="SELECT * FROM txt_company WHERE Firm_type='Agent' and delete_tag='FALSE' order by firm_name ASC";
					$s_result=mysqli_query($con,$s_sql);
					while($s_rs=mysqli_fetch_array($s_result))
					{
						$selected="";
						if($agent==$s_rs['company_id'])
						{
							$selected="selected";
						}						
						echo "<option $selected value='".$s_rs['company_id']."'>".$s_rs['firm_name']."</option>";
					}
				?>
            </select>
		</td>			

		
		<td colspan='2' align="center" >
		<input type="hidden" name="supplier_tag" id="supplier_tag"  value="" >
		<input type="hidden" name="supplier_last_value" id="supplier_last_value"  value="<?php echo $supplier;?>" >
		<select disabled name="supplier_account_code" id="supplier_account_code" >
            	<option value="">--Select--</option>
                <?php
					$s_sql="SELECT * FROM txt_company WHERE Firm_type='Supplier' and delete_tag='FALSE' order by firm_name ASC";
					$s_result=mysqli_query($con,$s_sql);
					while($s_rs=mysqli_fetch_array($s_result))
					{
						$selected="";
						if($supplier==$s_rs['company_id'])
						{
							$selected="selected";
						}						

						echo "<option $selected value='".$s_rs['company_id']."'>".$s_rs['firm_name']."</option>";
					}
				?>
            </select>
		</td>
		
    </tr>

	<tr>
		<th align="center">Receipt Amount </th>
		<th align="center">Bank Name </th>
		<th align="center">Cheque Number </th>
	</tr>
	


	<tr>

	<TD align="center">
		<input type="hidden" name="receipt_amount_tag" id="receipt_amount_tag"  value="" >
		<input type="hidden" name="receipt_amount_last_value" id="receipt_amount_last_value"  value="<?php echo $receipt_amount;?>" >
		
		<input  type="text" size='6'  name="receipt_amount" id="receipt_amount"  value="<?php echo $receipt_amount;?>" >
	</TD>
	<TD align="center"><input  type="text" sixe='6' name="bank_name" id="bank_name"  value="<?php echo $bank_name; ?>"  ></TD>
	<TD align="center"><input  type="text" sixe='6' name="cheque_number" id="cheque_number"  value="<?php echo $cheque_number; ?>"  ></TD>

	</tr>

	<tr>
	<th align="center">Deduction Amount </th>
	<th colspan='2' align="center">Narration</th>
	</tr>	

	<tr>
	<TD align="center">
		<input type="hidden" name="deduction_amount_tag" id="deduction_amount_tag"  value="" >
		<input type="hidden" name="deduction_amount_last_value" id="deduction_amount_last_value"  value="<?php echo $deduction_amount;?>" >
		<input   type="text" size='6'  name="deduction_amount" id="deduction_amount"  value="<?php echo $deduction_amount; ?>" ></TD>
	<td colspan='2' align="center" width="150"><textarea name="narration" id="narration"  cols="60" rows="1"><?php echo $narration; ?></textarea></td>
	<input type='hidden' name='hidden_narration' id='hidden_narration' value='<?php echo $narration; ?>'></td>
	</tr>
  
</table>
		<br>	
					



<br>
<table cellpadding="0" cellspacing="0" border="0">
<TR>
	<TH class="th-sticky"> <b> Commisssion Bill Details : </> <br /></TH>
</TR>
<tr>
	<td valign="top">
<div class="table_scroll_h" style="height:200px; width: 1400px; border:1px solid;">		
    <table width='100%' class="tbl_border">	
		<tr>
    	<th class="th-sticky" align="left">S.No.</th>
		<th class="th-sticky" align="left">Id</th>		

		<th class="th-sticky" align="left">Comm Year</th>
		<th class="th-sticky" align="left">Comm Wise</th>
		<th class="th-sticky" align="left">Comm <BR> Bill Wise</th>
		<th class="th-sticky" align="left">Comm <BR> Pay Wise</th>
		<th class="th-sticky" align="left">Adjusted <BR> Payment</th>
		<th class="th-sticky" align="left">Adjusted <BR> Discount</th>

		<th class="th-sticky" align="left">Type</th>
		<th class="th-sticky" align="left">Receipt <br>Wise</th>
		
		<th class="th-sticky" align="left">Deduction <br>/ TDS <br> Amount</th>
		<th class="th-sticky" align="left">Received <br> Amount</th>

		<th class="th-sticky" align="left">Bal. Amt. <br> (Calculated)</th>
		<th class="th-sticky" align="left">Remarks</th>
		</tr>



		<?php 

			//txt_commission_receipt_bill_entry 

			$recpt_bill_sql="SELECT * FROM  
				txt_commission_receipt_bill_entry 
				WHERE receipt_entry_id=$receipt_entry_id AND delete_tag='FALSE'";

			edit_payment_log(" - txt payment entry -".$recpt_bill_sql);
			//echo $recpt_bill_sql;
			$recpt_bill_result=mysqli_query($con,$recpt_bill_sql);

			$i=0;
			$where="";
			$disp_bal_amt="";

			while($rs=mysqli_fetch_array($recpt_bill_result)){
				$i++;
	
				// Making Where Clause for Next Query
				$where_comm_bill_ent_id=$rs['commission_bill_entry_id'];
				$where.=" AND commission_bill_entry_id !='$where_comm_bill_ent_id' ";



		?>


<tr style='background-color:#f0f5f4f3'>	
				<td align="left"><?php echo $i; ?></td>
			
				<td> 
				<input type="text" size="4" disabled  name="commission_bill_entry_id[]" id='commission_bill_entry_id' value="<?php echo $rs['commission_bill_entry_id']; ?>">
				<input type="hidden"  name="commission_bill_number[]" id="commission_bill_number" value="<?php echo $rs['commission_bill_number']; ?>">
				<input type="hidden"  name="commission_bill_date[]" id="commission_bill_date" value="<?php echo $rs['commission_bill_date']; ?>">


				</td>

				<td>
				<input type="text" size="5" disabled name="commission_year[]" id="commission_year" value="<?php echo $rs['commission_year']; ?>">
				</td>

				<td>
				<input type="text" size="10" disabled name="commission_mode[]" id="commission_mode" value="<?php echo ($rs['commission_mode']); ?>">		
				</td>


				<td><input disabled type="text" size="6" name="commission_amt_bill[]" id="commission_amt_bill" value="<?php echo $rs['commission_amt_bill'] ?>"  size="10"></td>		
				<td><input disabled type="text" size="6" name="commission_amt_pay[]" id="commission_amt_pay" value="<?php echo $rs['commission_amt_pay'] ?>"  size="10"></td>		

				<td><input disabled type="text" size="6" name="adj_amt[]"  id="adj_amt" size="10" value="<?php echo $rs['amount_adjusted'] ?>" ></td>
				<td><input disabled type="text" size="6" name="adj_dis[]"  id="adj_dis" size="10" value="<?php echo $rs['deduction_adjusted'] ?>" ></td>		
				
				<td><select onChange="receiptPartFullOnChange()" name="receipt_part_full[]" id="receipt_part_full" >
					<option value="">Select</option>
					<?php 
					if  ($rs['receipt_part_full'] == "Full"){
						echo '<option selected value="Full">Full</option>';
					}else {
						echo '<option value="Full">Full</option>';
					}

					if  ($rs['receipt_part_full'] == "Part"){
						echo '<option selected value="Part">Part</option>';
					}else {
						echo '<option value="Part">Part</option>';
					}					

					?>

				</Select></td>		
				
				<td>
				<select disabled onChange="receiptBillPayOnChange()" name="receipt_bill_pay[]" id="receipt_bill_pay" >
					<option>Select</option>

				<?php 
						$disp_comm_mode_arr=array('BillWise','PaymentWise');
						$comm_mode=$rs['receipt_bill_pay'];
						foreach($disp_comm_mode_arr as $v)
						{
						$selected="";
						
							if($comm_mode==$v)
							{
							$selected="selected";
							}
						
						echo "<option $selected value='$v'>".$v."</option>";
						}
				?>


				</td>		

				<td><input type="text" size="6"  onblur="calculateDeductionAmt()" name="deduction_amt[]" id="deduction_amt" value="<?php echo $rs['deduction_amount'] ?>"  size="10" onkeypress="return isNumberKey(event)" ></td>

				<td><input type="text" size="6"  onblur="calculateRecdAmt()" name="bill_received_amt[]" id="bill_received_amt" value="<?php echo $rs['received_amount'] ?>" size="10" onkeypress="return isNumberKey(event)" ></td>

				<td><input type="text" disabled size="6" name="bill_bal_amt[]" id="bill_bal_amt" size="10" value="<?php echo $rs['balance_amount']; ?>"></td>
				<td><textarea name="bill_remarks[]"  id="bill_remarks" cols="10" rows="2"></textarea></td>
				</tr>

<?php } ?>







		<?php


			$bill_result_rows=0;

			$save_btn_disp=true;

			if($receipt_type!=""){

				$bill_sql="SELECT * 
							FROM txt_commission_bill_entry 
							WHERE agent_account_code='$agent' 
							AND supplier_account_code='$supplier' 
							AND delete_tag='FALSE' ";
				$bill_sql.=$where;
				$bill_sql.=" ORDER BY commission_bill_date ASC, commission_bill_number ASC ";
				//echo $bill_sql;
				$bill_result=mysqli_query($con,$bill_sql);
				$bill_result_rows=mysqli_num_rows($bill_result);
				//echo $bill_result_rows;
				$save_btn_disp=false;
				if($bill_result_rows>0){
					$save_btn_disp=true;


		?>




<?php  
		$i=0;
		while($rs=mysqli_fetch_array($bill_result)){
			$i++;

			$commission_bill_entry_id=$rs['commission_bill_entry_id'];
			// to check if bill is paid it should not be displayed
			$paybill_sql="SELECT * FROM 
						txt_commission_receipt_bill_entry 
						WHERE commission_bill_entry_id='$commission_bill_entry_id' 
						AND delete_tag='FALSE' AND receipt_part_full='FULL' ";

			$pay_bill_result=mysqli_query($con,$paybill_sql);
			add_receipt_log("*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*");
			add_receipt_log($paybill_sql);
			add_receipt_log("*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*");

			$log_file = "my-errors.log";
			if(mysqli_errno($con)==0) 
			{ 
				//$bill_entry_id=mysqli_insert_id($con);
				//$_SESSION['msg']="<div class='success-message'>Bill Entry Id : $bill_entry_id Successfully Added</div>";
				//echo "<BR> Prit - 1" .mysqli_error($con);
				//echo "Bill Entry Id - ".$bill_entry_id;
				//echo "<br>";
				//error_log("Success \n ",3,$log_file);
				//error_log($sql,3,$log_file);

				//error_log($sql,1,"pritsneh@gmail.com");
			} 
			else 
			{
				$msg=mysqli_errno($con);
				$_SESSION['msg']="<div class='error-message'>Bill Entry Not Addedd Error Code($msg) </div>";
				echo " <BR>Prit - 2" .mysqli_error($con);
				echo "<br>";
				$time=time()+19800; // Timestamp is in GMT now converted to IST
				$date=date('d_m_Y_H_i_s',$time);
				error_log("\n *******Fail **************** \n " .$date,3,$log_file);
				error_log("\n ".mysqli_error($con),3,$log_file);
				error_log("\n".$paybill_sql."\n ",3,$log_file);
				//error_log($sql,1,"pritsneh@gmail.com");

				//$_SESSION['msg']=$msg;
			}



			add_receipt_log(" -Checking Full Payment Info - ".$paybill_sql);
			$len_rs=mysqli_num_rows($pay_bill_result);
			//echo "Pritesh --".$len_rs;
			// if result set is Zero then check for same bill_entry id and sum the values
			if($len_rs==0){
				//echo "Pritesh in --";
				add_receipt_log("Bill Not Fully Paid");
				$bill_ent_id_child="SELECT
									 SUM(received_amount)AS sum_rec_amt,
									 SUM(deduction_amount)AS sum_ded_amt
									 FROM txt_commission_receipt_bill_entry 
									 WHERE commission_bill_entry_id='$commission_bill_entry_id' AND delete_tag='FALSE' ";
			//	echo $bill_ent_id_child;echo "<br>";
				
				$pay_bill_child_result=mysqli_query($con,$bill_ent_id_child);

				add_receipt_log("- Sum Query - ".$bill_ent_id_child);

				$sum_adj_amt=0;
				$sum_adj_dis=0;
				$sum_adj_ded=0;
				$sum_adj_gr=0;
				while ($rs_c=mysqli_fetch_array($pay_bill_child_result)){
					//echo "Pritesh inside While--";echo "<br>";
					add_receipt_log("inside While");
					$sum_adj_amt=number_format($rs_c['sum_rec_amt'],2,'.','');
					$sum_adj_ded=number_format($rs_c['sum_ded_amt'],2,'.','');
					$sum_adj_dis+=$sum_adj_ded;
					$sum_adj_dis=number_format($sum_adj_dis,2,'.','');
					//echo $sum_adj_amt.$sum_adj_dis.$sum_adj_gr;
					//echo $sum_adj_amt;echo "<br>";
					//echo $rs_c['sum_pay_rec'];echo "<br>";
					add_receipt_log(implode($rs_c));
				}
				//echo "Pritesh out While--";
				//echo $sum_adj_amt.$sum_adj_dis.$sum_adj_gr.$sum_adj_ded;
				$disp_commission_amt_bill=number_format($rs['commission_amt_bill'],2,'.','');
				$disp_commission_amt_payment=number_format($rs['commission_amt_pay'],2,'.','');
				//$disp_bal_amt=number_format($disp_bill_amt-$sum_adj_amt-$sum_adj_dis-$sum_adj_gr,2,'.','');

					

?>
				<tr>	
				<td align="left"><?php echo $i; ?></td>
			
				<td> 
				<input type="text" size="4" disabled  name="commission_bill_entry_id[]" id='commission_bill_entry_id' value="<?php echo $rs['commission_bill_entry_id']; ?>">
				<input type="hidden"  name="commission_bill_number[]" id="commission_bill_number" value="<?php echo $rs['commission_bill_number']; ?>">
				<input type="hidden"  name="commission_bill_date[]" id="commission_bill_date" value="<?php echo $rs['commission_bill_date']; ?>">

				</td>

				<td>
				<input type="text" size="5" disabled name="commission_year[]" id="commission_year" value="<?php echo $rs['commission_year']; ?>">
				</td>

				<td>
				<input type="text" size="10" disabled name="commission_mode[]" id="commission_mode" value="<?php echo ($rs['commission_mode']); ?>">		
				</td>


				<td><input disabled type="text" size="6" name="commission_amt_bill[]" id="commission_amt_bill" value="<?php echo $rs['commission_amt_bill'] ?>"  size="10"></td>		
				<td><input disabled type="text" size="6" name="commission_amt_pay[]" id="commission_amt_pay" value="<?php echo $rs['commission_amt_pay'] ?>"  size="10"></td>		

				<td><input disabled type="text" size="6" name="adj_amt[]"  id="adj_amt" size="10" value="<?php echo $sum_adj_amt ?>" ></td>
				<td><input disabled type="text" size="6" name="adj_dis[]"  id="adj_dis" size="10" value="<?php echo $sum_adj_dis ?>" ></td>		
				
				<td><select onChange="receiptPartFullOnChange()" name="receipt_part_full[]" id="receipt_part_full" ><option value="">Select</option><option value="Full">Full</option><option value="Part">Part</option></Select></td>		
				
				<td>
				<select disabled onChange="receiptBillPayOnChange()" name="receipt_bill_pay[]" id="receipt_bill_pay" >
					<option>Select</option>

				<?php 
						$disp_comm_mode_arr=array('BillWise','PaymentWise');
						$comm_mode=$rs['commission_mode'];
						foreach($disp_comm_mode_arr as $v)
						{
						$selected="";
						
							if($comm_mode==$v)
							{
							$selected="selected";
							}
						
						echo "<option $selected value='$v'>".$v."</option>";
						}
				?>


				</td>		

				<td><input type="text" size="6" value="" onblur="calculateDeductionAmt()" name="deduction_amt[]" id="deduction_amt" size="10" onkeypress="return isNumberKey(event)" ></td>

				<td><input type="text" size="6" value="" onblur="calculateRecdAmt()" name="bill_received_amt[]" id="bill_received_amt" size="10" onkeypress="return isNumberKey(event)" ></td>

				<td><input type="text" disabled size="6" name="bill_bal_amt[]" id="bill_bal_amt" size="10" value="<?php //echo $disp_bal_amt; ?>"></td>
				<td><textarea name="bill_remarks[]"  id="bill_remarks" cols="10" rows="2"></textarea></td>
				</tr>
<?php
			}

	} 
}
?>
    </table>
</div>
	</td>
	</tr>

</table>


<BR>
<table  class="tbl_border">
			<tr>

				<th width="54">Total Received</th>
				<td width="220"><input disabled value="" type="text" name="total_amount_received" id="total_amount_received" /></td>
				<th width="100">Deduction Total</th>
				<td width="222"><input disabled value="" type="text" name="total_deduction" id="total_deduction" /></td>

				<th width="100">Log Message One</th>
				<td width="222"><input disabled size="20" value="" type="text" name="log_message_one" id="log_message_one" /></td>


			</tr>
			<tr>
				<td colspan='6'></td>
			</tr>
			<tr>

				<th width="100">Amount Difference</th>
				<td width="222"><input disabled value="" type="text" name="amount_received_difference" id="amount_received_difference" /></td>

				<th width="100">Deduction Difference</th>
				<td width="222"><input disabled value="" type="text" name="discount_difference" id="discount_difference" /></td>

				<th width="100">Log Message Two</th>
				<td width="222"><input disabled size="20" value="" type="text" name="log_message_two" id="log_message_two" /></td>				

			</tr>


		</table>
		<?php 
	if($req_disp=='child'){
	?>
	<input type="hidden" name="disp" value="child">
	<?php	
	}
	?>

		<?php
		if($req_src=='search') {
			
			
			$search_supplier_code=$_REQUEST['search_supplier_account_code'];
			$search_agent_code=$_REQUEST['search_agent_account_code'];
			$vou_start_date=$_REQUEST['vou_start_date'];
			$vou_end_date=$_REQUEST['vou_end_date'];
			$src=$_REQUEST['src'];
			
	?>



	<input type="hidden" name="search_supplier_account_code" value="<?php echo $search_supplier_code; ?>">
	<input type="hidden" name="search_agent_account_code" value="<?php echo $search_agent_code; ?>">

	<input type="hidden" name="vou_start_date" value="<?php echo $vou_start_date; ?>">
	<input type="hidden" name="vou_end_date" value="<?php echo $vou_end_date; ?>">


	<input type="hidden" name="src" value="<?php echo $src; ?>">

	


	<?php
			
		}
		?>	
<?php
	}

//echo "<BR> Hello Save Button <BR>";
//if($receipt_type!="" && $save_btn_disp){

if($receipt_type!="" ){	

?>
 <br>

 
<table width="324">
	<tr>
		<td width="116">

		<?php if($req_disp!='child') { ?>

			<?php if($req_src=='search') { 
				$search_supplier_code=$_REQUEST['search_supplier_account_code'];
				$search_agent_code=$_REQUEST['search_agent_account_code'];
				$vou_start_date=$_REQUEST['vou_start_date'];
				$vou_end_date=$_REQUEST['vou_end_date'];
				
				echo "<a href='s_commission_receipt_search.php?src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code'>Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				
			} else {?>

				<a href="index.php">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<?php } 
			}?>
			<input  type="button" class="form-button" onclick="final_submit()" name="final_btn" value="Update" />
			<?php if($req_disp!='child') { ?>	
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	
			<input  type="button" class="form-button" onclick="pay_delete()" name="del_btn" value="Delete" />
		<?php } ?>


		<!-- <a href="index.php">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input  type="button" class="form-button" onclick="final_submit()" name="final_btn" value="Save" />
		-->
		</td>
	</tr>
</table>
<br>
<?php
}

?>

</form>
	</td></tr></table><?php $_SESSION['uid']=77; ?>
	</div>
	</td></tr></table>
	</td></tr>
	<tr>
	<td> <?php include("../includes/footer.php"); ?></td>
	</tr>
	</table>
	<br>
	<script>
		document.getElementById('receipt_date').focus();
		
	</script>	
</body>
</html>
<?php 
release_connection($con);
?>