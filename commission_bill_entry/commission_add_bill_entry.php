<?php include("../includes/check_session.php");
include("../includes/config.php");
$con=get_connection();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Commission Add Bill Entry</title>
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

		document.getElementById('form-id').action='process_commission_bill_entry.php?action=add';
		document.getElementById('form-id').submit();
	}
}
</script>
<?php include("../includes/jQDate.php"); ?>
</head>
<body>

<table width="100%" border="4" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">
<tr>
    <td><?php include("../includes/header.php"); ?></td>
  </tr>
  <tr>
    <td><?php include("../includes/menu.php"); ?></td>
  </tr>
  <tr>
    <td height="326" valign="top"><table width="100%" style="margin-top:0px" align="center" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="top">
					<div class="content_padding">
					<div class="content-header">
					<a href="index.php">Back</a>
						<table width="100%" border='0'>
							<tr>
								<td width="25%" align='left'>
									<h3>Commission Add Bill Entry :</h3>
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
             <td width="818" height="138" valign="top">
    <form method="post" id="form-id" enctype="multipart/form-data" onsubmit="">
    
    <table width="776" height="370" class="tbl_border">	
	<tr>
    	<th width="223" align="left">Commission Year </th>
        <td align="left" width="182">
		<select name="commission_year" id="commission_year"> 
		<option value="Old">Old</option>
		<?php
		$year=Date("Y");
		// Base Year for this Software
		$finYear=2020;
		while ($finYear <= $year){
			$displayFinYear=$finYear."-".($finYear+1);
			echo "<option value='".$displayFinYear."'>".$displayFinYear."</option>";
			$finYear++;
		}
		?>
		<option value="New">New</option>
				</select>
		</td>

		<th> Bill Wise  / Payment Wise </th>
		<td>  
		<select name="commission_mode" id="commission_mode" onblur="commissionModeOnChange()"> 
		<option value="Bill Wise">Bill Wise</option>
		<option value="Payment Wise">Payment Wise</option>
				</select>

		</td>
    </tr>
    <tr>
    	<th align="left">Bill Number </th>
    	<td align="left"><input type="text" name="bill_number" size="10" id="bill_number" /></td>
        <th align="left">Bill Date <span class="astrik">*</span></th>
    	<td><input type="text" name="bill_date" onChange="validatedate_format(this)" class="datepick" size="8" id="bill_date" /></td>
    </tr>
   
    <tr>
		<th align="left">Supplier Name <span class="astrik">*</span></th>
		<td>
        	<select name="supplier_account_code" id="supplier_account_code">
            	<option value="">--Select--</option>
                <?php
					$s_sql="SELECT * FROM txt_company WHERE Firm_type='Supplier' AND delete_tag='FALSE' order by firm_name ASC";
					$s_result=mysqli_query($con,$s_sql);
					while($s_rs=mysqli_fetch_array($s_result))
					{
						echo "<option value='".$s_rs['company_id']."'>".$s_rs['firm_name']."</option>";
					}
				?>
            </select>
		</td>
		<th align="left">Agent Name <span class="astrik">*</span></th>
			
			<td>
        	<select name="agent_account_code" id="agent_account_code">
            	<option value="">--Select--</option>
                <?php
					$s_sql="SELECT * FROM txt_company WHERE Firm_type='Agent' AND delete_tag='FALSE' order by firm_name ASC";
					$s_result=mysqli_query($con,$s_sql);
					while($s_rs=mysqli_fetch_array($s_result))
					{
						echo "<option value='".$s_rs['company_id']."'>".$s_rs['firm_name']."</option>";
					}
				?>
            </select>
		</td>			
	</tr>

	
	<tr>

<th align="left">GST % <span class="astrik">*</span></th>
<td>
	<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
		<tr cellpadding="0" cellspacing="0" border="0">
			<td cellpadding="0" cellspacing="0" border="0">	

				<input type="text" value='5' name="gst_percent" size="6" id="gst_percent" onblur="gstPercentOnChange()" />
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

			<input type="text" value='2'  size="6" name="commission_percent" id="commission_percent" onblur="commissionPercentOnChange()"/>
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
						<input type="text" name="total_bill_amount" size="8" id="total_bill_amount" onblur="totalBillAmountOnChange()" />
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
						<input type="text" name="total_gr_amount" size="8" id="total_gr_amount" onblur="totalGRAmountOnChange()" />
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
						<input type="text" name="total_discount_amount" size="8" onfocusout="totalDiscountAmountOnChange()" id="total_discount_amount" />

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
		<td><input disabled  type="text" name="net_bill_amt" size="8" id="net_bill_amt" /> (Calculated)</td>
    	<th align="left">Total Payment Amount <span class="astrik">*</span></th>
    	<td>
			<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">	
						<input type="text" name="total_payment_amount" size="8" id="total_payment_amount" onblur="totalPaymentAmountOnChange()" />
					</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="tot_pay_msg" id="tot_pay_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>						
		</td>
	<tr>				
    	<th align="left">GST Amount (Net Bill Amount) <span class="astrik">*</span></th>
		<td><input disabled type="text" name="gst_amount_bill" size="8" id="gst_amount_bill" /> (Calculated) </td>
		<th align="left">GST Amount (Total Payment Amount) <span class="astrik">*</span></th>
		<td><input disabled type="text" name="gst_amount_payment" size="8" id="gst_amount_payment" /> (Calculated) </td>
	</tr>
	<tr>
	<th align="left"> Net Bill Amount (Less GST) </th>
		<td><input disabled type="text" name="bill_amount_less_gst" size="8" id="bill_amount_less_gst" /> (Calculated)</td>		
		<th align="left">Total Payment Amount (Less GST)</th>
    	<td>
			<input  disabled type="text" name="total_payment_amount_less_gst" size="8" id="total_payment_amount_less_gst"  />
						(Calculated)
		</td>
    </tr>	
    <tr>
	<th> Commission Amount (Bill Wise)</th>
		<td><input disabled type="text" name="commission_amt_bill" size="8" id="commission_amt_bill" /> (Calculated)</td>
		<th> Commission Amount (Payment Wise)</th>
		<td><input disabled type="text" name="commission_amt_pay" size="8" id="commission_amt_pay" /> (Calculated)</td>
	</tr>
    <tr>
    	<th align="left">Remarks </th>
		<td><input type="text" name="remarks" id="remarks" size="50"/></td>
		<th> </th>
		<td cellpadding="0" cellspacing="0" border="0">	<p name="log_msg" id="log_msg" style="color: red" ></p> </td>
	</tr>
	
    </table>
     <br/><br/>
		<table width="324">
			<tr>
				<td width="116">
				<a href="index.php">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input  type="button" class="form-button" onclick="final_submit()" name="my_btn" value="Save" />
				
				</td>
			</tr>
		</table>
      </form>
      </td>
	  </tr>
	  </table>
		<?php 
			$_SESSION['uid']=77; 
			?>
      </div>
      </td>
	  </tr>
	  </table>
      </td></tr>
	<tr>
	<td> <?php include("../includes/footer.php"); ?></td>
	</tr>
	</table>
</body>
<script>
	document.getElementById("commission_year").focus();
</script>
</html>
<?php 
release_connection($con);
?>