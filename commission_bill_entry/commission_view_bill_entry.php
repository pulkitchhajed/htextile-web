<?php include("../includes/check_session.php");
include("../includes/config.php"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View Commission Bill Entry</title>
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
<script type="text/javascript">



</script>
</head>
<body>
<?php include("../includes/jQDate.php"); ?>

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

<table width="100%" border="0" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">
<tr>
    <td>
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
											echo "<a href='s_commission_bill_search.php?src=search&&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code'>Back</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
											
											} else {								
											 	echo ' <a href="index.php">Back</a>'; 
											}
												
									}?>
            <table width="100%"><tr><td><h3>View Commission Bill Entry :</h3></td>
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
		<th width="223" align="left">Commission Year </th>
        <td width="174" align="left"><input disabled type="text" name="commission_year" value="<?php echo $rs['commission_year']; ?>" id="commission_year" size="10"></td>

    	<th> Bill Wise  / Payment Wise </th>
    	<td align="left"><input disabled type="text" name="commission_mode"  value="<?php echo ($rs['commission_mode']); ?>" id="commission_mode" size="8" /></td>
	</tr>
	<tr>

	</tr>	
    <tr>
    	<th align="left">Bill Number </th>
    	<td><input disabled type="text" name="commission_bill_number" value="<?php echo $rs['commission_bill_number']; ?>" size="10" id="commission_bill_number" /></td>

    	<th align="left">Bill Date <span class="astrik">*</span></th>
    	<td><input disabled type="text" name="commission_bill_date" class="datepick" value="<?php echo rev_convert_date($rs['commission_bill_date']); ?>" id="commission_bill_date" size="8" /></td>
	</tr>
	<tr>

	</tr>	

	<tr>

	</tr>	

    <tr>
		<th align="left">Supplier Name <span class="astrik">*</span></th>
		<td>
        	<select disabled name="supplier_account_code" id="supplier_account_code">
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
        	<select disabled name="agent_account_code" id="agent_account_code">
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
    	<th align="left">GST %  <span class="astrik">*</span></th>
		<td>
			<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">	
						<input type="text" disabled name="gst_percent" size="6"  value="<?php echo $rs['gst_percent']; ?>"  id="gst_percent" onblur="grossAmountOnChange()" />
					</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="grs_msg" id="grs_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>
		</td>
		<th align="left">Commission % </th>
    	<td>
		<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">			
						<input type="text" disabled name="commission_percent" size="6" value="<?php echo $rs['commission_percent']; ?>" onfocusout="discountCalculate()" id="commission_percent" />

						</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="dis_msg" id="dis_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>

					
		</td>
	</tr>
    <tr>


    	<th align="left">Total Bill Amount</th>
		<td><input disabled  type="text" name="total_bill_amount" size="6" value="<?php echo $rs['total_bill_amount']; ?>" id="total_bill_amount" /> </td>

    	<th align="left">Total GR Amount</th>
		<td><input disabled  type="text" name="total_gr_amount" size="6" value="<?php echo $rs['total_gr_amount']; ?>" id="total_gr_amount" /> </td>


	</tr>


    <tr>
		
		<th align="left">Total Discount Amount </th>
    	<td>
			<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">	
						<input type="text" disabled name="total_discount_amount" size="6" value="<?php echo $rs['total_discount_amount']; ?>" id="total_discount_amount" />
					</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="ded_msg" id="ded_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>						
		
		</td>
		<td colspan=2 ></td>

    </tr>
    <tr>

    	<th align="left">Net Bill Amount</th>
    	<td>
		<table  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse">
				<tr cellpadding="0" cellspacing="0" border="0">
					<td cellpadding="0" cellspacing="0" border="0">	
						<input type="text" disabled name="net_bill_amt" size="6" value="<?php echo $rs['net_bill_amt']; ?>" id="net_bill_amt"   />
						</td>
					<td cellpadding="0" cellspacing="0" border="0">
						<p name="adl_msg" id="adl_msg" style="color: red" ></p>
					</td>
				</tr>
			</table>						
		</td>


    	<th align="left">Total Payment Amount  <span class="astrik">*</span> </th>
		<td><input disabled type="text" name="total_payment_amount" size="6"  value="<?php echo $rs['total_payment_amount']; ?>" id="total_payment_amount" /> (Calculated) </td>

	</tr>
	
	<tr>
		<th align="left">GST Amount (Net Bill Amount)</th>
		<td>


						<input type="text" disabled name="gst_amount_bill" size="6" id="gst_amount_bill" value="<?php echo $rs['gst_amount_bill']; ?>"  onblur="gstCalculate()" />

		</td>


    	<th align="left">GST Amount (Total Payment Amount)</th>
		<td><input disabled type="text" name="gst_amount_payment" size="6"  value="<?php echo $rs['gst_amount_payment']; ?>" id="gst_amount_payment" /> (Calculated) </td>

	</tr>
  	

    <tr>
	<th> Commission Amount (Bill Wise)</th>
		<td><input disabled  value="<?php echo $rs['commission_amt_bill']; ?>" type="text" name="commission_amt_bill" size="8" id="commission_amt_bill" /> (Calculated)</td>
		<th> Commission Amount (Payment Wise)</th>
		<td><input disabled   value="<?php echo $rs['commission_amt_pay']; ?>" type="text" name="commission_amt_pay" size="8" id="commission_amt_pay" /> (Calculated)</td>
	</tr>
    <tr>
    	<th align="left">Remarks <span class="astrik">*</span></th>
		<td><input type="text" disabled name="remarks" value="<?php echo $rs['remarks']; ?>" id="remarks" /></td>
		<td colspan=2 >
		
		</td>
	</tr>
	
	
    
    <input type="hidden" name="commission_bill_entry_id" value="<?php echo $commission_bill_entry_id; ?>">
    
</table>
 <br /><br />
				    <table width="324">
                	    <tr>
                    	    <td width="116">
							<?php if($req_disp!='child') { ?>
								
								
								<?php if($req_src=='search') { 
									$search_supplier_code=$_REQUEST['search_supplier_account_code'];
									$search_agent_code=$_REQUEST['search_agent_account_code'];
									$bill_start_date=$_REQUEST['bill_start_date'];
									$bill_end_date=$_REQUEST['bill_end_date'];
									//src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&buyer_account_code=$buyer_code&supplier_account_code=$supplier_code&bill_entry_id=$bill_entry_id
									echo "<a href='s_commission_bill_search.php?src=search&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&search_agent_account_code=$search_agent_code&search_supplier_account_code=$search_supplier_code&commmission_bill_entry_id=$commission_bill_entry_id'>Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									
								 } else { ?>	
									<a href="index.php">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
							<?php } 
								} ?>				
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
</html>
<?php 
release_connection($con);
?>