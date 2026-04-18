<?php include("../includes/check_session.php");
include("../includes/config.php"); ?>


<?php
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
	case 'add':
		save();
		break;
	case 'modify':
		update();
		break;
	case 'delete' :
		delete(); 
		break;
	case 'remfile' :
		removeFile();
		break;				
}
$req_disp="";
if(isset($_REQUEST['disp'])){
	$req_disp=$_REQUEST['disp'];
}

$req_src="";
if(isset($_REQUEST['src'])){
	$req_src=$_REQUEST['src'];
}


if($req_disp!='child'){
	if($action=='add'){
		
		echo "<script language='javascript'>";
		echo "location.href='commission_add_bill_entry.php'";
		echo "</script>";
		
	}else{
		
		if($req_src=='search') { 
			$supplier_code=$_REQUEST['search_supplier_account_code'];
			$agent_code=$_REQUEST['search_agent_account_code'];
			$bill_start_date=$_REQUEST['bill_start_date'];
			$bill_end_date=$_REQUEST['bill_end_date'];

			//src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&buyer_account_code=$buyer_code&supplier_account_code=$supplier_code&bill_entry_id=$bill_entry_id
			
			echo "<script language='javascript'>";
			echo "location.href='s_commission_bill_search.php?src=search&&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&search_agent_account_code=$agent_code&search_supplier_account_code=$supplier_code'";
			echo "</script>";			
			
			
		 } else {
			
			echo "<script language='javascript'>";
			echo "location.href='index.php'";
			echo "</script>";
			
		 }
	}
}else {
	echo "<script language='javascript'>";
	echo "location.href='../ledger/child_update_submit.php'";
	echo "</script>";

}


function save() 
{
	$con=get_connection();
		// get data 
	if(($_REQUEST['commission_year']) && ($_SESSION['uid']==77)) 
	{

			$commission_year=$_REQUEST['commission_year'];
			$commission_mode=$_REQUEST['commission_mode'];
				
			$bill_number=$_REQUEST['bill_number'];
			$bill_date=$_REQUEST['bill_date'];
				$bill_date=convert_date($bill_date);
			$supplier_account_code=$_REQUEST['supplier_account_code'];
			$agent_account_code=$_REQUEST['agent_account_code'];

			$gst_percent=blankToZero($_REQUEST['gst_percent']);
			$commission_percent=blankToZero($_REQUEST['commission_percent']);

			$total_bill_amount=blankToZero($_REQUEST['total_bill_amount']);
			$total_gr_amount=blankToZero($_REQUEST['total_gr_amount']);
			$total_discount_amount=blankToZero($_REQUEST['total_discount_amount']);

			$net_bill_amt=blankToZero($_REQUEST['net_bill_amt']);
			$total_payment_amount=blankToZero($_REQUEST['total_payment_amount']);

			$gst_amount_bill=blankToZero($_REQUEST['gst_amount_bill']);
			$gst_amount_payment=blankToZero($_REQUEST['gst_amount_payment']);

			$bill_amount_less_gst=blankToZero($_REQUEST['bill_amount_less_gst']);
			$total_payment_amount_less_gst=blankToZero($_REQUEST['total_payment_amount_less_gst']);

			$commission_amt_bill=blankToZero($_REQUEST['commission_amt_bill']);
			$commission_amt_pay=blankToZero($_REQUEST['commission_amt_pay']);



			$remarks=$_REQUEST['remarks'];
	
			
			$last_update_user=$_SESSION['LOGID'];
			//echo "Pritesh - ".$lr_date."--";
			$log_file = "my-errors.log";
			
			$sql="insert into 
			txt_commission_bill_entry
				(commission_year,
				commission_mode,
				commission_bill_number,
				commission_bill_date,
				supplier_account_code,
				agent_account_code,
				gst_percent,
				commission_percent,
				total_bill_amount,
				total_gr_amount,
				total_discount_amount,
				net_bill_amt,
				total_payment_amount,
				gst_amount_bill,
				gst_amount_payment,
				bill_amount_less_gst,
				total_payment_amount_less_gst,
				commission_amt_bill,
				commission_amt_pay,			
				remarks,
				last_update_user,
				last_update_date,
				create_user,
				create_date)
				values(
					'$commission_year',
					'$commission_mode',
					'$bill_number',
					'$bill_date',
					'$supplier_account_code',
					'$agent_account_code',

					'".blankToZero($gst_percent)."',
					'".blankToZero($commission_percent)."',
					'".blankToZero($total_bill_amount)."',
					'".blankToZero($total_gr_amount)."',
					'".blankToZero($total_discount_amount)."',
					'".blankToZero($net_bill_amt)."',
					'".blankToZero($total_payment_amount)."',
					'".blankToZero($gst_amount_bill)."',
					'".blankToZero($gst_amount_payment)."',
					'".blankToZero($bill_amount_less_gst)."',
					'".blankToZero($total_payment_amount_less_gst)."',
					'".blankToZero($commission_amt_bill)."',
					'".blankToZero($commission_amt_pay)."',
					'$remarks',
					'$last_update_user',
					NOW(),
					'$last_update_user',
					NOW())";

			//error_log($sql,3,$log_file);

			$result=mysqli_query($con,$sql); 
			//echo $sql;
			//echo $result;
				if(mysqli_errno($con)==0) 
					{ 
						$bill_entry_id=mysqli_insert_id($con);
						$_SESSION['msg']="<div class='success-message'>Bill Entry Id : $bill_entry_id Successfully Added</div>";
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
						error_log("\n".$sql."\n ",3,$log_file);
						//error_log($sql,1,"pritsneh@gmail.com");

						//$_SESSION['msg']=$msg;
					}

					$_SESSION['uid']=11;
		
	} //if(($_REQUEST['commission_year']) && ($_SESSION['uid']==77)) 


	release_connection($con);

}



function update()
{
	if(($_REQUEST['commission_bill_entry_id']) && ($_SESSION['uid']==77))
	{
		$_SESSION['uid']=11; // this is to prevent the call of function if Save is clicked twice .
		
		$con=get_connection();

		$commission_bill_entry_id=$_REQUEST['commission_bill_entry_id'];

		// $check_pay_sql=" select * from txt_payment_bill_entry where delete_tag='FALSE' and bill_entry_id='$commission_bill_entry_id' ";

		$pay_attached=0;
/*		
		$result=mysqli_query($con,$check_pay_sql);
		while($rs=mysqli_fetch_array($result))
		{
			$pay_attached++;
		}
*/


		if($pay_attached==0)
		{

		
		$commission_bill_entry_id=$_REQUEST['commission_bill_entry_id'];

		$commission_year=$_REQUEST['commission_year'];
		$commission_mode=$_REQUEST['commission_mode'];

		$commission_bill_number=$_REQUEST['bill_number'];
		$commission_bill_date=$_REQUEST['bill_date'];
			$commission_bill_date=convert_date($commission_bill_date);

		$supplier_account_code=$_REQUEST['supplier_account_code'];
		$agent_account_code=$_REQUEST['agent_account_code'];

		$total_bill_amount=blankToZero($_REQUEST['total_bill_amount']);
		$total_gr_amount=blankToZero($_REQUEST['total_gr_amount']);
		$total_discount_amount=blankToZero($_REQUEST['total_discount_amount']);
		$net_bill_amt=blankToZero($_REQUEST['net_bill_amt']);
		$bill_amount_less_gst=blankToZero($_REQUEST['bill_amount_less_gst']);
		
		$total_payment_amount=blankToZero($_REQUEST['total_payment_amount']);
		$total_payment_amount_less_gst=blankToZero($_REQUEST['total_payment_amount_less_gst']);

		$gst_percent=blankToZero($_REQUEST['gst_percent']);
		$gst_amount_payment=blankToZero($_REQUEST['gst_amount_payment']);
		$gst_amount_bill=blankToZero($_REQUEST['gst_amount_bill']);

		$commission_percent=blankToZero($_REQUEST['commission_percent']);
		$commission_amt_bill=blankToZero($_REQUEST['commission_amt_bill']);
		$commission_amt_pay=blankToZero($_REQUEST['commission_amt_pay']);

		$remarks=$_REQUEST['remarks'];

		$last_update_user=$_SESSION['LOGID'];

		$bill_upload="";
	
		$sql="update txt_commission_bill_entry set 
			commission_year='$commission_year',
			commission_mode='$commission_mode',
			commission_bill_number='$commission_bill_number',
			commission_bill_date='$commission_bill_date',
			supplier_account_code='$supplier_account_code',
			agent_account_code='$agent_account_code',
			total_bill_amount='$total_bill_amount',
			total_gr_amount='$total_gr_amount',
			total_discount_amount='$total_discount_amount',
			net_bill_amt='$net_bill_amt',
			bill_amount_less_gst='$bill_amount_less_gst',
			total_payment_amount='$total_payment_amount',
			total_payment_amount_less_gst='$total_payment_amount_less_gst',
			gst_percent='$gst_percent',
			gst_amount_payment='$gst_amount_payment',
			gst_amount_bill='$gst_amount_bill',
			commission_percent='$commission_percent',
			commission_amt_bill='$commission_amt_bill',
			commission_amt_pay='$commission_amt_pay',
			remarks='$remarks',
			last_update_user='$last_update_user',
			last_update_date=NOW()
			where commission_bill_entry_id='$commission_bill_entry_id'";

				//		gst_percent,gst_amount,round_off,last_update_user,last_update_date)
				//'$gst_per','$gst_amount','$round_off','$last_update_user',NOW())"			
			
			$result=mysqli_query($con,$sql);	

			$log_file = "my-errors.log";

			error_log($sql,3,$log_file);


			if(mysqli_errno($con)==0) 
				{ 
					$_SESSION['msg']="<div class='success-message'>Bill Entry  $commission_bill_entry_id Successfully Updated</div>";

					//echo " <BR>Prit - 1 -" .mysqli_error($con);
					//echo "<br>";
				} 
				else 
				{
					$msg= "<div class='error-message'> Error Message - ".mysqli_error($con)."  Bill Entry Not updated </div>";
//					$_SESSION['msg']="<div class='error-message'>Book Not Addedd $msg</div>";
					$_SESSION['msg']=$msg;

					//echo " <BR>Prit - 2" .mysqli_error($con);
					//echo "<br>";
					$time=time()+19800; // Timestamp is in GMT now converted to IST
					$date=date('d_m_Y_H_i_s',$time);
					error_log("\n *******Fail **************** \n " .$date,3,$log_file);
					error_log("\n ".mysqli_error($con),3,$log_file);
					error_log("\n".$sql."\n ",3,$log_file);

				}

			}
			else
			{
				$msg="Bill Entry Not Updated  $pay_attached Payments already attached to the Bill  ";
				$_SESSION['msg']="<div class='error-message'>$msg</div>";	

			}				

				$_SESSION['uid']=11;
				release_connection($con);

	}
}


function delete()
{
	$con=get_connection();
	if(($_REQUEST['commission_bill_entry_id']) && ($_SESSION['uid']==77))
	{

		$bill_entry_id=$_REQUEST['commission_bill_entry_id'];

		$check_pay_sql=" select * from txt_commission_receipt_bill_entry where delete_tag='FALSE' and commission_bill_entry_id='$bill_entry_id' ";

		$pay_attached=0;
		echo $check_pay_sql;
		$result=mysqli_query($con,$check_pay_sql);
		while($rs=mysqli_fetch_array($result))
		{
			$pay_attached++;
		}


		if($pay_attached==0)
		{


				
				$delete_user=$_SESSION['LOGID'];
				//$sql="delete from txt_bill_entry where bill_entry_id='$bill_entry_id'";

				$sql=" update txt_commission_bill_entry set  delete_tag='TRUE',";
				$sql .=" delete_user='$delete_user',";
				$sql .=" delete_date=NOW()";
				$sql .=" where commission_bill_entry_id='$bill_entry_id'";

				$log_file = "my-errors.log";
				error_log($sql,3,$log_file);


				$result=mysqli_query($con,$sql);
				if(mysqli_errno($con)==0) 
				{ 
					$_SESSION['msg']="<div class='success-message'>Bill Entry Successfully Deleted</div>";
				} 
				else 
				{
					$msg=" Error Code (".mysqli_errno($con).") Bill Entry Not Deleted  ";
					$_SESSION['msg']="<div class='error-message'>$msg</div>";
					echo " <BR>Prit - 2 -" .mysqli_error($con);
					echo "<br>";
					$time=time()+19800; // Timestamp is in GMT now converted to IST
					$date=date('d_m_Y_H_i_s',$time);
					error_log("\n *******Fail **************** \n " .$date,3,$log_file);
					error_log("\n ".mysqli_error($con),3,$log_file);
					error_log("\n".$sql."\n ",3,$log_file);
				}
		}
		else
		{
			$msg="Bill Entry Not Deleted  $pay_attached Payments already attached to the Bill  ";
			$_SESSION['msg']="<div class='error-message'>$msg</div>";	
		}
		$_SESSION['uid']=11;

		release_connection($con);

	}
}

function removeFile()
{
	echo "Remove File";
	$log_file = "my-errors.log";
	echo ":--".$_REQUEST['bill_entry_id'];
	echo $_SESSION['uid'];
	if(($_REQUEST['bill_entry_id']) && ($_SESSION['uid']==77))
	{
		//echo "Remove File inside if";

		$con=get_connection();
		$bill_entry_id=$_REQUEST['bill_entry_id'];
		$ft=$_REQUEST['ft'];  // ft= file type 
		
		if($ft=="bill_upload") 
		{
			//$sql="select bill_entry_id from txt_bill_entry where bill_entry_id='$bill_entry_id'";
			$sql_update="update txt_bill_entry set bill_upload='' where bill_entry_id='$bill_entry_id'";
		}
		
		if($ft=="supporting_doc") 
		{
			//$sql="select supporting_doc from txt_bill_entry where bill_entry_id='$bill_entry_id'";
			$sql_update="update txt_bill_entry set supporting_doc='' where bill_entry_id='$bill_entry_id'";
		}
		

		
		//$result=mysqli_query($con,$sql);	
		//$row=mysql_fetch_row($result);
			
		mysqli_query($con,$sql_update);
		
		if(mysqli_errno($con)==0) { 
					$_SESSION['msg']="<div class='success-message'>File Successfully Deleted</div>";
				} else {
					$msg= "File Delete Error - ".mysqli_error($con)." -File Not Deleteted";
					$_SESSION['msg']="<div class='success-message'>$msg</div>";
					echo " <BR>Prit - 2" .mysqli_error($con);
					echo "<br>";
					$time=time()+19800; // Timestamp is in GMT now converted to IST
					$date=date('d_m_Y_H_i_s',$time);
					error_log("\n *******Fail **************** \n " .$date,3,$log_file);
					error_log("\n ".mysqli_error($con),3,$log_file);
					error_log("\n".$sql_update."\n ",3,$log_file);
				}
				
			

		$req_disp="";
		if(isset($_REQUEST['disp'])){
			$req_disp=$_REQUEST['disp'];
		}
		if($req_disp!='child'){
			echo "<script language='javascript'>";
			echo "location.href='edit_bill_entry.php?bill_entry_id=$bill_entry_id'";
			echo "</script>";
		}else {
			echo "<script language='javascript'>";
			echo "location.href='edit_bill_entry.php?disp=child&bill_entry_id=$bill_entry_id'";
			echo "</script>";
		
		}
		
		release_connection($con);
		

	}
}

?>