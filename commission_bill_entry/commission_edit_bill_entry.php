<?php include("../includes/check_session.php");
include("../includes/config.php"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Edit Bill Entry</title>
<link rel="icon" type="image/x-icon" href="<?php echo $web_path; ?>images/dt-favicon.ico" />
<link href="<?php echo $web_path; ?>css/style.css" rel="stylesheet" type="text/css" />

<style>
*
{
	margin:0;
	padding:0;
}
</style>
<script type="text/javascript" src="../js/commission_bill_entry.js"></script>
<script type="text/javascript" src="../js/dateCheck.js"></script>


<script type="text/javascript">
function check()
{
	var bill_date=document.getElementById("bill_date").value;
		if(bill_date=="") {
			alert("Please Enter Bill Date");
			document.getElementById("bill_date").focus();
			return false;
		}
	var supplier_account_code=document.getElementById("supplier_account_code").value;
		if(supplier_account_code=="") {
			alert("Please Enter Supplier Account Code");
			document.getElementById("supplier_account_code").focus();
			return false;
		}
	var agent_account_code=document.getElementById("agent_account_code").value;
		if(agent_account_code=="") {
			alert("Please Enter Agent Account Code");
			document.getElementById("agent_account_code").focus();
			return false;
		}
	var gst_percent=document.getElementById("gst_percent").value;
		if(gst_percent=="") {
			alert("Please Enter GST Percent");
			document.getElementById("gst_percent").focus();
			return false;
		}
	var commission_percent=document.getElementById("commission_percent").value;
		if(commission_percent=="") {
			alert("Please Enter Commission Percent");
			document.getElementById("commission_percent").focus();
			return false;
		}		
	var total_bill_amount=document.getElementById("total_bill_amount").value;
		if(total_bill_amount=="") {
			alert("Please Enter Total Bill Amount");
			document.getElementById("total_bill_amount").focus();
			return false;
		}
	var total_payment_amount=document.getElementById("total_payment_amount").value;
		if(total_payment_amount=="") {
			alert("Please Enter Total Payment Amount");
			document.getElementById("total_payment_amount").focus();
			return false;
		}		

	return true;
}


function final_submit() {
	
	if(check()) {
		
		document.getElementById("net_bill_amt").disabled=false;

		document.getElementById("gst_amount_bill").disabled=false;
		document.getElementById("bill_amount_less_gst").disabled=false;
		
		document.getElementById("gst_amount_payment").disabled=false;
		document.getElementById("total_payment_amount_less_gst").disabled=false;
		
		document.getElementById("commission_amt_bill").disabled=false;
		document.getElementById("commission_amt_pay").disabled=false;

		document.getElementById('form-id').action='process_commission_bill_entry.php?action=modify';
		document.getElementById('form-id').submit();
	}
}

function bill_delete(){
	//alert("inDelete");
	l_remark=document.getElementById("remarks").value;
	//alert (l_remark);
	l_hidden_remark=document.getElementById("hidden_remarks").value;
	//alert (l_remark);
	//alert (l_hidden_remark);

	if(l_remark==l_hidden_remark){
		alert ('Please Mention the Reason of Delete in Remark');
		document.getElementById("remarks").focus();

	}else{
		document.getElementById('form-id').action='process_commission_bill_entry.php?action=delete';
		document.getElementById('form-id').submit();
	}
	//alert ("Done");

}
</script>
</head>
<body>
<?php include("../includes/jQDate.php"); ?>

<table width="100%" border="0" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">
<tr>
    <td>
		<?php 
		  $req_disp="";
		  if(isset($_REQUEST['disp'])){
			  $req_disp=$_REQUEST['disp'];
		  }
		  $req_src="";
		  if(isset($_REQUEST['src'])){
			  $req_src=$_REQUEST['src'];
		  }

		?>
			<?php if($req_disp!='child'){include("../includes/header.php"); }?>
	</td>
  </tr>
  <tr>
    <td><?php if($req_disp!='child'){include("../includes/menu.php"); } ?></td>
  </tr>
  <tr>
    <td height="326" valign="top"><table width="100%" style="margin-top:0px" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td valign="top">
                            <div class="content_padding">
                            <div class="content-header">
							<?php 	if($req_disp!='child'){ 
										if($req_src=='search') { 
											$search_supplier_code=$_REQUEST['search_supplier_account_code'];
											$search_agent_code=$_REQUEST['search_agent_account_code'];
											$bill_start_date=$_REQUEST['bill_start_date'];
											$bill_end_date=$_REQUEST['bill_end_date'];

											//src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&buyer_account_code=$buyer_code&supplier_account_code=$supplier_code&bill_entry_id=$bill_entry_id
											echo "<a href='s_commission_bill_search.php?src=search&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code'>Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
											
											} else {
												echo ' <a href="index.php">Back</a>'; 
											}
									
									
									
									}?>
                           
			<table width="100%"><tr><td><h3>Commission Edit Bill Entry :</h3></td>
			<td align="center" >
                    <?php
									if(isset($_SESSION['msg'])) {
										echo $_SESSION['msg'];
										$_SESSION['msg']='';
									}
								?></td>
            	<td align="right"></td>
            </tr>
            </table>
            </div>
    
    <table cellpadding="0" cellspacing="0" border="0">
            <tr>
             <td width="818" height="138" valign="top">
    <form method="post" id="form-id" enctype="multipart/form-data" >
    
    <?php
$con=get_connection();
$commission_bill_entry_id=$_REQUEST['commission_bill_entry_id'];
$sql="select * from txt_commission_bill_entry where commission_bill_entry_id='$commission_bill_entry_id'";
$result=mysqli_query($con,$sql);
$rs=mysqli_fetch_array($result);
?>
    
    <table width="776" height="370" class="tbl_border">	
    <tr>
	
	
	<th width="226" align="left">Commission Bill Entry Id </th>
		<td width="174" align="left" > <?php echo $rs['commission_bill_entry_id']; ?> </td>
		<td colspan=2 ></td>
		
	</tr>
	<tr>

	</tr>
	<tr>

	</tr>
	<tr>
    	<th width="223" align="left">Commission Year </th>
        <td align="left" width="182">

		<select name="commission_year" id="commission_year"> 
			<?php
			if ($rs["commission_year"]=="Old"){ ?>
				<option selected value="Old">Old</option>
			<?php 
			}else{ ?>
				<option value="Old">Old</option>
			<?php 

			}
			?>
		
		<?php
		$year=Date("Y");
		// Base Year for this Software
		$finYear=2020;
		while ($finYear <= $year){
			$displayFinYear=$finYear."-".($finYear+1);
			$selected="";
			if($rs["commission_year"]==$displayFinYear)
			{
				$selected="selected";

			}

			echo "<option $selected value='".$displayFinYear."'>".$displayFinYear."</option>";
			$finYear++;
		}
		?>
		<?php
			if ($rs["commission_year"]=="New"){ ?>
				<option selected value="New">New</option>
			<?php 
			}else{ ?>
				<option value="New">New</option>
			<?php 

			}


		?>

				</select>
		</td>

		<th> Bill Wise  / Payment Wise </th>
		<td>  
		<select name="commission_mode" id="commission_mode" onblur="commissionModeOnChange()"> 

		<?php 
		 $disp_comm_mode_arr=array('Bill Wise','Payment Wise');
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

				</select>

		</td>
    </tr>

    <tr>
    	<th align="left">Bill Number </th>
    	<td align="left"><input value="<?php echo $rs['commission_bill_number']?>" type="text" name="bill_number" size="10" id="bill_number" /></td>
        <th align="left">Bill Date <span class="astrik">*</span></th>
    	<td><input type="text" value="<?php echo rev_convert_date ($rs['commission_bill_date']) ;?>" name="bill_date" onChange="validatedate_format(this)" class="datepick" size="8" id="bill_date" /></td>
    </tr>
	
	

    <tr>
		<th align="left">Supplier Name <span class="astrik">*</span></th>
		<td>
        	<select name="supplier_account_code" id="supplier_account_code">
            	<option value="">--Select--</option>
                <?php
					$s_sql="SELECT * FROM txt_company WHERE Firm_type='Supplier' and delete_tag='FALSE' order by firm_name ASC";
					$s_result=mysqli_query($con,$s_sql);
					while($s_rs=mysqli_fetch_array($s_result))
					{
						$selected="";
						if($rs['supplier_account_code']==$s_rs['company_id'])
						{
							$selected="selected";
						}						

						echo "<option $selected value='".$s_rs['company_id']."'>".$s_rs['firm_name']."</option>";
					}
				?>
            </select>
		</td>		

		<th align="left">Agent Name <span class="astrik">*</span></th>
		
		<td>
        	<select name="agent_account_code" id="agent_account_code">
            	<option value="">--Select--</option>
                <?php
					$s_sql="SELECT * FROM txt_company WHERE Firm_type='Agent' and delete_tag='FALSE' order by firm_name ASC";
					$s_result=mysqli_query($con,$s_sql);
					while($s_rs=mysqli_fetch_array($s_result))
					{
						$selected="";
						if($rs['agent_account_code']==$s_rs['company_id'])
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

	</tr>


	<tr>

	</tr>
	<tr>

<th align="left">GST % <span class="astrik">*</span></th>
<td>
	<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
		<tr cellpadding="0" cellspacing="0" border="0">
			<td cellpadding="0" cellspacing="0" border="0">	

				<input type="text"  value="<?php echo $rs['gst_percent']?>" name="gst_percent" size="6" id="gst_percent" onblur="gstPercentOnChange()" />
			</td>
			<td cellpadding="0" cellspacing="0" border="0">
				<p name="gst_msg" id="gst_msg" style="color: red" ></p>
			</td>
		</tr>
	</table>
</td>		
<th align="left">Commission % <span class="astrik">*</span></th>
<td>
<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
		<tr cellpadding="0" cellspacing="0" border="0">
			<td cellpadding="0" cellspacing="0" border="0">	

			<input type="text" value="<?php echo $rs['commission_percent']?>"  size="6" name="commission_percent" id="commission_percent" onblur="commissionPercentOnChange()"/>
			</td>
			<td cellpadding="0" cellspacing="0" border="0">
				<p name="comm_msg" id="comm_msg" style="color: red" ></p>
			</td>
		</tr>
	</table>
		</td>
</tr>	



<tr>
    	<th align="left">Total Bill Amount <span class="astrik">*</span></th>
		<td>
			<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">	
						<input type="text" value="<?php echo $rs['total_bill_amount']?>"  name="total_bill_amount" size="8" id="total_bill_amount" onblur="totalBillAmountOnChange()" />
					</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="total_bill_amt_msg" id="total_bill_amt_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>
		</td>
		<th align="left">Total GR Amount </th>
		<td >
		<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">	
						<input type="text" value="<?php echo $rs['total_gr_amount']?>" name="total_gr_amount" size="8" id="total_gr_amount" onblur="totalGRAmountOnChange()" />
					</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="total_gr_msg" id="total_gr_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>


	<tr>
    	<th align="left">Total Discount Amount </th>
    	<td>
		<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">			
						<input type="text"  value="<?php echo $rs['total_discount_amount']?>" name="total_discount_amount" size="8" onfocusout="totalDiscountAmountOnChange()" id="total_discount_amount" />

						</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="dis_msg" id="dis_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>

					
		</td>
		<td colspan=2> &nbsp;				</td>
  
	</tr>


	<tr>
		<th align="left">Net Bill Amount </th>
		<td><input disabled  value="<?php echo $rs['net_bill_amt']?>" type="text" name="net_bill_amt" size="8" id="net_bill_amt" /> (Calculated)</td>
    	<th align="left">Total Payment Amount <span class="astrik">*</span></th>
    	<td>
			<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">	
						<input type="text" value="<?php echo $rs['total_payment_amount']?>" name="total_payment_amount" size="8" id="total_payment_amount" onblur="totalPaymentAmountOnChange()" />
					</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="tot_pay_msg" id="tot_pay_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>						
		</td>
	<tr>				
    	<th align="left">GST Amount (Net Bill Amount) <span class="astrik">*</span></th>
		<td><input disabled type="text" value="<?php echo $rs['gst_amount_bill']?>" name="gst_amount_bill" size="8" id="gst_amount_bill" /> (Calculated) </td>
		<th align="left">GST Amount (Total Payment Amount) <span class="astrik">*</span></th>
		<td><input disabled type="text" value="<?php echo $rs['gst_amount_payment']?>" name="gst_amount_payment" size="8" id="gst_amount_payment" /> (Calculated) </td>
	</tr>
	<tr>
	<th align="left"> Net Bill Amount (Less GST) </th>
		<td><input disabled type="text" value="<?php echo $rs['bill_amount_less_gst']?>" name="bill_amount_less_gst" size="8" id="bill_amount_less_gst" /> (Calculated)</td>		
		<th align="left">Total Payment Amount (Less GST)</th>
    	<td>
			<input  disabled type="text" value="<?php echo $rs['total_payment_amount_less_gst']?>" name="total_payment_amount_less_gst" size="8" id="total_payment_amount_less_gst"  />
						(Calculated)
		</td>
    </tr>	
    <tr>
	<th> Commission Amount (Bill Wise)</th>
		<td><input disabled type="text" value="<?php echo $rs['commission_amt_bill']?>" name="commission_amt_bill" size="8" id="commission_amt_bill" /> (Calculated)</td>
		<th> Commission Amount (Payment Wise)</th>
		<td><input disabled type="text" value="<?php echo $rs['commission_amt_pay']?>" name="commission_amt_pay" size="8" id="commission_amt_pay" /> (Calculated)</td>
	</tr>
    <tr>
    	<th align="left">Remarks </th>
		<td><input type="text" value="<?php echo $rs['remarks']?>" name="remarks" id="remarks" size="50"/></td>
		<input type="hidden" value="<?php echo $rs['remarks']?>" name="hidden_remarks" id="hidden_remarks" />
		<th> </th>
		<td cellpadding="0" cellspacing="0" border="0">	<p name="log_msg" id="log_msg" style="color: red" ></p> </td>
	</tr>


 	
    
	<input type="hidden" name="commission_bill_entry_id" value="<?php echo $commission_bill_entry_id; ?>">
	
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
			$bill_start_date=$_REQUEST['bill_start_date'];
			$bill_end_date=$_REQUEST['bill_end_date'];
			$src=$_REQUEST['src'];
			
	?>



	<input type="hidden" name="search_supplier_account_code" value="<?php echo $search_supplier_code; ?>">
	<input type="hidden" name="search_agent_account_code" value="<?php echo $search_agent_code; ?>">

	<input type="hidden" name="bill_start_date" value="<?php echo $bill_start_date; ?>">
	<input type="hidden" name="bill_end_date" value="<?php echo $bill_end_date; ?>">


	<input type="hidden" name="src" value="<?php echo $src; ?>">

	


	<?php
			
		}
		?>	
    
</table>
 <br /><br />
				    <table border='0' >
                	    <tr>
                    	    <td >
								<?php if($req_disp!='child') { ?>

									<?php if($req_src=='search') { 
										
										$search_supplier_code=$_REQUEST['search_supplier_account_code'];
										$search_agent_code=$_REQUEST['search_agent_account_code'];
										$bill_start_date=$_REQUEST['bill_start_date'];
										$bill_end_date=$_REQUEST['bill_end_date'];
										//src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&buyer_account_code=$buyer_code&supplier_account_code=$supplier_code&bill_entry_id=$bill_entry_id
										echo "<a href='s_commission_bill_search.php?src=search&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code'>Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										
									} else {?>										
									<a href="index.php">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<?php } 
									}?>

									<?php 
											//$bill_entry_id=$_REQUEST['bill_entry_id'];

											$check_pay_sql=" select * from txt_commission_receipt_bill_entry where delete_tag='FALSE' and commission_bill_entry_id='$commission_bill_entry_id' ";
									
											$pay_attached=0;
											$result=mysqli_query($con,$check_pay_sql);
											while($rs=mysqli_fetch_array($result))
											{
												$pay_attached++;
											}

											if($pay_attached==0)
											{	
									?>
							   <input  type="button" class="form-button" onclick="final_submit()" name="my_btn" value="Update" />
							   <?php if($req_disp!='child') { ?>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input  type="button" class="form-button" onclick="bill_delete()" name="del_btn" value="Delete" />
								<?php } ?>	

								<?php } else {
									echo " <br><br>
									<h3> Commission Bill Edit and delete not Allowed because one or more Payment is attached to it. </h3>";
								}
								
								 ?>	
								<?php
								/*
								<input  type="button" class="form-button" onclick="final_submit()" name="my_btn" value="Save" />
								*/
								?>

                 			</td>
						</tr>
					</table>
                    </form>
                  </td></tr></table><?php $_SESSION['uid']=77; ?>
                  </div>
                  </td></tr></table>
                  </td></tr>
                  <tr>
                  	<td> <?php include("../includes/footer.php"); ?></td>
                  </tr>
                  </table>
</body>

<script>
document.getElementById("voucher_number").focus();
</script>
</html>
<?php 
release_connection($con);
?>