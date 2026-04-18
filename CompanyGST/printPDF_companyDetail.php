

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>View Company</title>
<link rel="icon" type="image/x-icon" href="<?php echo $web_path; ?>images/dt-favicon.ico" />
<link href="<?php echo $web_path; ?>css/style.css" rel="stylesheet" type="text/css" />

<?php
$con=get_connection();



$sql="select * from txt_company where company_id='$company_id'";

$result=mysqli_query($con,$sql);
$rs=mysqli_fetch_array($result);
?>
<style>*
{
	margin:0;
	padding:0;
}
</style>

<script type="text/javascript">
function check()
{
	var firm_name=document.getElementById("firm_name").value;
	if(firm_name=="") {
		alert("Please Enter Firm Name");
		document.getElementById("firm_name").focus();
		return false;
	}
	
	var address=document.getElementById("address").value;
	if(address=="") {
		alert("Please Enter address");
		document.getElementById("address").focus();
		return false;
	}
	
	var city=document.getElementById("city").value;
	if(city=="") {
		alert("Please Enter city");
		document.getElementById("city").focus();
		return false;
	}
	
	var state=document.getElementById("state").value;
	if(state=="") {
		alert("Please Enter state");
		document.getElementById("state").focus();
		return false;
	}
	
	var pincode=document.getElementById("pincode").value;
	if(pincode=="") {
		alert("Please Enter pincode");
		document.getElementById("pincode").focus();
		return false;
	}
	
	var gstin=document.getElementById("gstin").value;
	if(gstin=="") {
		alert("Please Enter GSTIN");
		document.getElementById("gstin").focus();
		return false;
	}
	
/*	var contact_person=document.getElementById("contact_person").value;
	if(contact_person=="") {
		alert("Please Enter Contact Person");
		document.getElementById("contact_person").focus();
		return false;
	}
	
	var contact_number=document.getElementById("contact_number").value;
	if(contact_number=="") {
		alert("Please Enter Contact Number");
		document.getElementById("contact_number").focus();
		return false;
	}
	
	var sms_number=document.getElementById("sms_number").value;
	if(sms_number=="") {
		alert("Please Enter SMS Number");
		document.getElementById("sms_number").focus();
		return false;
	}
	
	var whatsapp_number=document.getElementById("whatsapp_number").value;
	if(whatsapp_number=="") {
		alert("Please Enter Whatsapp Number");
		document.getElementById("whatsapp_number").focus();
		return false;
	}
	
	var email=document.getElementById("email").value;
	if(email=="") {
		alert("Please Enter email");
		document.getElementById("email").focus();
		return false;
	}
	
	var email=document.getElementById('email');
	var filter = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

        if (filter.test(email.value) == false) 
        {
            alert('Invalid Email Address');
            return false;
        }

	var website=document.getElementById("website").value;
	if(website=="") {
		alert("Please Enter website");
		document.getElementById("website").focus();
		return false;
	}
	
	var group_type=document.getElementById("group_type").value;
	if(group_type=="") {
		alert("Please Enter Group Type");
		document.getElementById("group_type").focus();
		return false;
	}
	
	var commission_percentage=document.getElementById("commission_percentage").value;
	if(commission_percentage=="") {
		alert("Please Enter Commission Percentage");
		document.getElementById("commission_percentage").focus();
		return false;
	}
	
	var firm_type=document.getElementById("firm_type").value;
	if(firm_type=="") {
		alert("Please Enter Firm Type");
		document.getElementById("firm_type").focus();
		return false;
	}
	
	var reference=document.getElementById("reference").value;
	if(reference=="") {
		alert("Please Enter reference");
		document.getElementById("reference").focus();
		return false;
	}
	
	var remarks=document.getElementById("remarks").value;
	if(remarks=="") {
		alert("Please Enter remarks");
		document.getElementById("remarks").focus();
		return false;
	}
	
	var pan_number=document.getElementById("pan_number").value;
	if(pan_number=="") {
		alert("Please Enter PAN Number");
		document.getElementById("pan_number").focus();
		return false;
	}
	
	<?php if($rs['visiting_card']=="") { ?>
		var visiting_card=document.getElementById("visiting_card").value;
		if(visiting_card=="") {
			alert("Please Enter Visiting Card");
			document.getElementById("visiting_card").focus();
			return false;
		}
	<?php } ?>

	<?php if($rs['photo_1']=="") { ?>
		var photo_1=document.getElementById('photo_1').value;
		if(photo_1=="") {
			alert("Please Enter photo_1");
			document.getElementById("photo_1").focus();
			return false;
		}
	<?php } ?>			

	<?php if($rs['photo_2']=="") { ?>
		var photo_2=document.getElementById("photo_2").value;
		if(photo_2=="") {
			alert("Please Enter photo_2");
			document.getElementById("photo_2").focus();
			return false;
		}
	<?php } ?>
*/	return true;
}


function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    //if (charCode > 31 && (charCode < 48 || charCode > 57))
	if (charCode > 31 && (charCode != 46 &&(charCode < 48 || charCode > 57)))
        return false;
    return true;
}

function isSpaceKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    //if (charCode > 31 && (charCode < 48 || charCode > 57))
	if (charCode == 32)
        return false;
    return true;
}

function checkLength(len,ele){
  var fieldLength = ele.value.length;
  if(fieldLength <= len){
    return true;
  }
  else
  {
    var str = ele.value;
    str = str.substring(0, str.length - 1);
    ele.value = str;
  }
}


function isUpperCase(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    //if (charCode > 31 && (charCode < 48 || charCode > 57))
	if (charCode >= 65 && charCode <=90)
        return true;
    return false;
}

function ChangeCase(elem)  {
        elem.value = elem.value.toUpperCase();
}


function final_submit() {
			//alert("in final submit mode");
				if(check()) {
					document.getElementById('form-id').action='process_company.php?action=modify';
					document.getElementById('form-id').submit();
				}
} // end of function final_submit
</script>

<?php
	$company_id=$_REQUEST['company_id'];
$sql="select * from txt_company where company_id='$company_id'";

$result=mysqli_query($con,$sql);
$rs=mysqli_fetch_array($result);
?>
<table width="100%" border="0" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">

  <tr>
    <td height="326" valign="top"><table width="100%" style="margin-top:0px" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td valign="top">
                            <div class="content_padding">
                            <div class="content-header">
                          
            <table width="100%"><tr><td><h3> <?php echo $rs['firm_type']; ?> GST Details :</h3></td>
             <td align="center" width="135">
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

   
    <table width="718" class="tbl_border">	
	
    <tr>
    	<th width="100" align="left">Firm Name</th>
        <td width="600"><input disabled name="firm_name" type="hidden" id="firm_name" value="<?php echo $rs['firm_name']; ?>" size="60"><?php echo $rs['firm_name']; ?></td>
    </tr>
    <tr>
    	<th align="left">Address </th>
        <td><input disabled type="hidden" name="address" id="address" size="60" value="<?php echo $rs['address']; ?>"><?php echo $rs['address']; ?></td>
    </tr>
    <tr>
    	<th align="left">City </th>
        <td><input disabled type="hidden" name="city" id="city" value="<?php echo $rs['city']; ?>"><?php echo $rs['city']; ?></td>
    </tr>
    <tr>
    	<th align="left">State </th>
		<td> <input disabled type="hidden" name="state" id="state" value="<?php echo $rs['state']; ?>"><?php echo $rs['state']; ?></td>
    
    </tr>
    <tr>
    	<th align="left">Pincode </th>
        <td><input disabled type="hidden" name="pincode" id="pincode" size="8" value="<?php echo $rs['pincode']; ?>" ><?php echo $rs['pincode']; ?></td>
    </tr>
    <tr>
    	<th align="left">GSTIN </th>
        <td><input disabled type="hidden" name="gstin" id="gstin" size="20" value="<?php echo $rs['gstin']; ?>"><?php echo $rs['gstin']; ?></td>
	</tr>
	<tr>
    	<th align="left">Office Number</th>
        <td><input disabled type="hidden" name="office_phone" id="office_phone" size="10" value="<?php echo $rs['office_phone']; ?>"  ><?php echo $rs['office_phone']; ?></td>
    </tr>	
    <tr>
    	<th align="left">Contact Person</th>
        <td><input disabled name="contact_person" type="hidden" id="contact_person" value="<?php echo $rs['contact_person']; ?>" size="40"><?php echo $rs['contact_person']; ?></td>
    </tr>
    <tr>
    	<th align="left">Contact Number</th>
        <td><input disabled type="hidden" name="contact_number" id="contact_number" size="10" value="<?php echo $rs['contact_number']; ?>" ><?php echo $rs['contact_number']; ?></td>
	</tr>
    <tr>
    	<th align="left">Contact Person</th>
        <td><input disabled name="contact_person_2" type="hidden" id="contact_person_2" value="<?php echo $rs['contact_person_2']; ?>" size="40"><?php echo $rs['contact_person_2']; ?></td>
    </tr>
    <tr>
    	<th align="left">Contact Number</th>
        <td><input disabled type="hidden" name="contact_number_2" id="contact_number_2" size="10" value="<?php echo $rs['contact_number_2']; ?>" ><?php echo $rs['contact_number_2']; ?></td>
    </tr>
	

    <tr>
    	<th align="left">SMS Number</th>
        <td><input disabled type="hidden" name="sms_number" id="sms_number" size="10" value="<?php echo $rs['sms_number']; ?>" ><?php echo $rs['sms_number']; ?></td>
    </tr>
    <tr>
    	<th align="left">Whatsapp</th>
        <td><input disabled type="hidden" name="whatsapp_number" id="whatsapp_number" size="10" value="<?php echo $rs['whatsapp_number']; ?>"   ><?php echo $rs['whatsapp_number']; ?></td>
    </tr>
    <tr>
    	<th align="left">Email</th>
        <td><input disabled type="hidden" name="email" id="email" size="50" value="<?php echo $rs['email']; ?>"><?php echo $rs['email']; ?></td>
    </tr>
    <tr>
    	<th align="left">Website</th>
        <td><input  disabled type="hidden" name="website" id="website" size="50" value="<?php echo $rs['website']; ?>"><?php echo $rs['website']; ?></td>
    </tr>

    </table>
     <br /><br />
				  
                    </form>
                  </td></tr></table><?php $_SESSION['uid']=77; ?>
                  </div>
                  </td></tr></table>
                  </td></tr>
                  <tr>
           
                  </tr>
                  </table>
</body>
</html>
<?php 

?>