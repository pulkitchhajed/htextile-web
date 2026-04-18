<?php include("../includes/check_session.php");
include("../includes/config.php");
$con=get_connection();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Discount Calculator</title>
<link rel="icon" type="image/x-icon" href="<?php echo $web_path; ?>images/dt-favicon.ico" />
<link href="<?php echo $web_path; ?>css/style.css" rel="stylesheet" type="text/css" />
	<meta charset="UTF-8" />

  <script type="text/javascript" src="../js/discount_calculater.js"></script>
<script>






</script>

<script>


</script>

<style>
*
{
	margin:0;
	padding:0;
}

table,th,td
{
	border-collapse:collapse;
	font-size:12px;
}
</style>



<?php 






?>
</head>

<body>
<table width="100%" border="5" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">
  <tr>
    <td><?php include("../includes/header.php"); ?></td>
  </tr>
  <tr>
    <td><?php include("../includes/menu.php"); ?></td>
  </tr>
  <tr>
    <td height="326" valign="top">
      <table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td valign="top">
            <div class="content_padding">
              <div class="content-header">
                <table width="100%" border='0'><tr>
                  <td width="25%"><h3>Discount Calculator (With Full GST without Debit Note)</h3></td>
								<td align="center" width="30%">
                    				<?php
									if(isset($_SESSION['msg'])) {
                    					echo $_SESSION['msg'];
                    					$_SESSION['msg']='';
									}
									?>
								</td>								

            
            </tr></table>
              </div>
              <form method="post" id="discount_calc" enctype="multipart/form-data" >   
                <table class="tbl_border"> <tr> <td valign="top">
              <table class="tbl_border" >
                <tr >
                  <th  align="left"> Bill Amount <span class="astrik">*</span> </th>
                  <td>
                  <input type="text" name="bill_amount" size="6" id="bill_amount" onblur="billAmountOnChange()" />
		              </td>
                  <td cellpadding="0" cellspacing="0" border="0">
						        <p name="bill_msg" id="bill_msg" style="color: red" ></p>
					        </td>
                </tr>

                <tr>
                  <th align="left">GST </th>
			
                  <td>
                  <input type="text" name="gst" size="6" id="gst" value='5.00' onblur="gstAmountOnChange()" />  
                  </td>

                  <td cellpadding="0" cellspacing="0" border="0">
						        <p name="gst_msg" id="gst_msg" style="color: red" ></p>
					        </td>



                </tr>  

                <tr><td>&nbsp;</td></tr>
                <tr>

                  <th align="left">Rate Difference (if Any)</th>
			
                  <td>
                  <input type="text" name="rate_difference"  size="6" id="rate_difference" onblur="rdAmountOnChange()" />  
                  </td>


                  
                  <td cellpadding="0" cellspacing="0" border="0">
						        <p name="rd_msg" id="rd_msg" style="color: red" ></p>
					        </td>

                </tr>

                <tr>
                  <th align="left">Meter (For Rate Diff)</th>

                  <td>
                  <input type="text" name="meter" size="6" id="meter" onblur="meterAmountOnChange()" />  
                  </td>

                  <td cellpadding="0" cellspacing="0" border="0">
						        <p name="mtr_msg" id="mtr_msg" style="color: red" ></p>
					        </td>
       
                </tr>       
                

                <tr><td>&nbsp;</td></tr>


                <tr>
                  <th align="left">Discount %</th>
			
                  <td>
                  <input type="text" name="discount" size="6" id="discount" onblur="discountAmountOnChange()" />  
                  </td>

                  <td cellpadding="0" cellspacing="0" border="0">
						        <p name="dis_msg" id="dis_msg" style="color: red" ></p>
					        </td>


                </tr>                 
                <tr>
                  <td colspan=5>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan=5>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan=5>&nbsp;</td>
                </tr>
                
                
              </table>
                </td>
                <td valign="top">

              <table class="tbl_border">
              <tr>
                  <th align="right">Bill Amount (Before GST) </th>
		              <td><input disabled type="text" value='' name="bill_amt_before_gst_calc" size="6" id="bill_amt_before_gst_calc" /> (Calculated) </td>


           
              </tr>
              <tr>

                  <th align="right">Rate Difference Amount  (-) </th>
		              <td><input disabled type="text" value='' name="rate_diff_amount_calc" size="6" id="rate_diff_amount_calc" /> (Calculated) </td>
           

                  
              </tr>
              <tr><th></th><td>&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;</td></tr>
              <tr>
              <th align="right">Amount (=)  </th>
              <td><input disabled type="text" value='' name="bal_amt_1" size="6" id="bal_amt_1" /> (Calculated) </td>
              
                </tr>

              <tr>

              <th align="right">Discount Amount (-)  </th>
		              <td><input disabled type="text" value='' name="discount_amount_calc" size="6" id="discount_amount_calc" /> (Calculated) </td>

              </tr>
              </tr>
              <tr><th></th><td>&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;</td></tr>
              <tr>
              </tr>
              <th align="right">Amount  (=) </th>
              <td><input disabled type="text" value='' name="bal_amt_2" size="6" id="bal_amt_2" /> (Calculated) </td>

              <tr>
              <th align="right">GST Amount  (+)</th>
		              <td><input disabled type="text" value='' name="gst_amount_calc" size="6" id="gst_amount_calc" /> (Calculated) </td>

                  </tr>
              <tr><th></th><td>&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;&mdash;</td></tr>
              <tr>

                <th align="right">Payment Amount(=)</th>
		              <td><input disabled type="text" value='' name="payment_amount_calc" size="6" id="payment_amount_calc" /> (Calculated) </td>
                  </tr>
              
              </table>
                </td></tr>
                <tr><td colspan='4'> <span class="astrik">*</span> Meter and Rate Difference together has meaning  </td></tr>
                </table>
              
              <br>
              <br>
         
            </div>                                 
          </td>
        </tr>
        <tr>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>

    </td>
  </tr>
    <?php include("../includes/footer.php"); ?>

</table>
</form>
</body>
</html>
<script>
    <?php if($_REQUEST['src']=="search"){
        echo "bill_search_submit();";

    }
    ?>
</script>
<?php 
release_connection($con);
?>
