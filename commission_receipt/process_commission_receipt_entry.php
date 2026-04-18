<?php include("../includes/check_session.php");
include("../includes/config.php");
ini_set("max_execution_time", "500");
?>


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

/*
echo "<BR> Start <BR>";

echo $req_disp ;
echo $req_src ;

echo "<BR> Start <BR>";
*/
if($req_disp!='child'){
	if($action=='add'){
		
		process_payment_entry_redirect( "<script language='javascript'>");
		process_payment_entry_redirect( "location.href='commission_add_receipt_entry.php'");
		process_payment_entry_redirect( "</script>");
		
	}else{	

		if($req_src=='search') { 
			$supplier_code=$_REQUEST['search_supplier_account_code'];
			$agent_code=$_REQUEST['search_agent_account_code'];
			$vou_start_date=$_REQUEST['vou_start_date'];
			$vou_end_date=$_REQUEST['vou_end_date'];
		
			//src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&bill_end_date=$bill_end_date&bill_start_date=$bill_start_date&buyer_account_code=$buyer_code&supplier_account_code=$supplier_code&bill_entry_id=$bill_entry_id
			process_payment_entry_redirect( "<script language='javascript'>");
			process_payment_entry_redirect( "location.href='s_commission_receipt_search.php?src=search&vou_end_date=$vou_end_date&vou_start_date=$vou_start_date&search_agent_account_code=$agent_code&search_supplier_account_code=$supplier_code'");
			process_payment_entry_redirect( "</script>");			
		 } else {
			
			process_payment_entry_redirect( "<script language='javascript'>");
			process_payment_entry_redirect( "location.href='index.php'");
			process_payment_entry_redirect( "</script>");
			
			
			//echo "<BR> Two <BR>";
		 }
	}
}else {
	 //echo "<BR> One <BR>";
	
	process_payment_entry_redirect( "<script language='javascript'>");
	process_payment_entry_redirect( "location.href='../ledger/child_update_submit.php'");
	process_payment_entry_redirect( "</script>");
	

}

function save() {

	$con=get_connection();

	if(($_REQUEST['receipt_type']) && ($_SESSION['uid']==77)) 	{
		$log_file = "my-errors.log";

		$_SESSION['uid']=11; // this is to prevent the call of function if Save is clicked twice .

		$last_update_user=$_SESSION['LOGID'];
		
		
		$receipt_date=$_REQUEST['receipt_date'];
		$receipt_type=$_REQUEST['receipt_type'];
		$receipt_mode=$_REQUEST['receipt_mode'];

		$supplier_account_code=$_REQUEST['supplier_account_code'];
		$agent_account_code=$_REQUEST['agent_account_code'];

		$receipt_amount=blankToZero($_REQUEST['receipt_amount']);

		$bank_name=blankToZero($_REQUEST['bank_name']);
		$cheque_number=blankToZero($_REQUEST['cheque_number']);

		$deduction_amount=blankToZero($_REQUEST['deduction_amount']);

		$narration=$_REQUEST['narration'];
		




		$receipt_date=convert_date($receipt_date);
		

		$receipt_entry_id=0; // will populated once insert query for Main is executed.

		$sql_main="insert into 
			txt_commission_receipt_entry_main
			(receipt_date,
			receipt_type, 
			receipt_mode,
			supplier_account_code,
			agent_account_code,
			receipt_amount,
			deduction_amount,
			bank_name,
			cheque_number,
			narration,
			last_update_user,
			last_update_date,
			create_user,
			create_date
			)
			values
			('$receipt_date',
			'$receipt_type',
			'$receipt_mode',
			'$supplier_account_code',
			'$agent_account_code',
			'$receipt_amount',
			'$deduction_amount',
			'$bank_name',
			'$cheque_number',
			'$narration',
			'$last_update_user',
			NOW(),			
			'$last_update_user',
			NOW()
			)";

			
			$sql_error_message="";
			$sql_success_code=0;

			$result=mysqli_query($con,$sql_main);
			process_payment_entry_logging_disp($sql_main);

			// Payment Entry ID
			$receipt_entry_id=mysqli_insert_id($con);
			if(mysqli_errno($con)==0) { 
				$sql_success_code+=0;
				//$_SESSION['msg']="<div class='success-message'>Payment Entry (".$receipt_entry_id.") Successfully Added</div>";
				process_payment_entry_logging_disp( "Payment Entry Main - Success" .mysqli_error($con));
				process_payment_entry_logging_disp( "Payment Entry Id - ".$receipt_entry_id);

			} else {
				$sql_success_code+=1;
				$sql_error_message = $sql_error_message."-MAIN-";
				process_payment_entry_logging_disp( " Payment Entry Main - Error" .mysqli_error($con));

	
				process_payment_entry_error_log("\n *******Fail **************** \n " .date_time());
				process_payment_entry_error_log("\n ".mysqli_error($con));
				process_payment_entry_error_log("\n".$sql_main."\n ");

				//$_SESSION['msg']="<div class='error-message'>Payment Entry Not Addedd</div>";

			} //if(mysqli_errno($con)==0)



	if((mysqli_errno($con)==0)){  // - 2

		if($receipt_type=="Advance Receipt" ){

			// will  be implemented later
			
			$_SESSION['msg']="<div class='success-message'>Receipt Entry (".$receipt_entry_id.")  Successfully Added</div>";
			

		}else {
			/*
			$total_amount_received=$_REQUEST['total_amount_received'];
			$total_discount_received=$_REQUEST['total_discount_received'];
			$total_goods_return_received=$_REQUEST['total_goods_return_received'];
			$amount_received_difference=$_REQUEST['amount_received_difference'];
			*/

			$commission_bill_entry_id_array=$_REQUEST['commission_bill_entry_id'];
			$commission_bill_number_array=$_REQUEST['commission_bill_number'];
			$commission_bill_date_array=$_REQUEST['commission_bill_date'];
			$commission_bill_entry_id_array_size=sizeof($commission_bill_entry_id_array);

			//$bill_voucher_number_array=$_REQUEST['bill_voucher_number'];
			//$bill_voucher_date_array=$_REQUEST['bill_voucher_date'];
			$commission_year_array=$_REQUEST['commission_year'];
			$commission_mode_array=$_REQUEST['commission_mode'];
			$commission_amt_bill_array=$_REQUEST['commission_amt_bill'];
			$commission_amt_pay_array=$_REQUEST['commission_amt_pay'];
			$adj_amt_array=$_REQUEST['adj_amt'];
			$adj_dis_array=$_REQUEST['adj_dis'];
			
			$receipt_part_full_array=$_REQUEST['receipt_part_full'];
			$receipt_bill_pay_array=$_REQUEST['receipt_bill_pay'];

			$deduction_amt_array=$_REQUEST['deduction_amt'];
			$bill_received_amt_array=$_REQUEST['bill_received_amt'];

			$bill_bal_amt_array=$_REQUEST['bill_bal_amt'];

			$bill_remarks_array=$_REQUEST['bill_remarks'];

			for($b=0;$b<$commission_bill_entry_id_array_size;$b++){

				//$bill_voucher_date_array[$b]=convert_date($bill_voucher_date_array[$b]);
				//$bill_date_array[$b]=convert_date($bill_date_array[$b]);

				$commission_amt_bill_array[$b]=blankToZero($commission_amt_bill_array[$b]);
				$commission_amt_pay_array[$b]=blankToZero($commission_amt_pay_array[$b]);

				$adj_amt_array[$b]=blankToZero($adj_amt_array[$b]);				
				$adj_dis_array[$b]=blankToZero($adj_dis_array[$b]);

				$bill_received_amt_array[$b]=blankToZero($bill_received_amt_array[$b]);
				$deduction_amt_array[$b]=blankToZero($deduction_amt_array[$b]);
				$bill_bal_amt_array[$b]=blankToZero($bill_bal_amt_array[$b]);



				if($receipt_part_full_array[$b]=="Full" || $receipt_part_full_array[$b]=="Part"){

					$sql_bill[$b]="insert into 
							txt_commission_receipt_bill_entry 
							(   
								receipt_entry_id,
								receipt_date,
								commission_bill_entry_id,
								commission_bill_number,
								commission_bill_date,
								commission_year,
								commission_mode,
								commission_amt_bill,
								commission_amt_pay,
								receipt_bill_pay,
								amount_adjusted,
								receipt_part_full,
								deduction_adjusted,
								received_amount,
								deduction_amount,
								balance_amount,
								remark,
								receipt_type,
								receipt_mode,
								create_user,
								create_date,
								last_update_user,
								last_update_date
							)
							values
							(
								'$receipt_entry_id',
								'$receipt_date',
								'$commission_bill_entry_id_array[$b]',
								'$commission_bill_number_array[$b]',
								'$commission_bill_date_array[$b]',
								'$commission_year_array[$b]',
								'$commission_mode_array[$b]',
								'$commission_amt_bill_array[$b]',
								'$commission_amt_pay_array[$b]',
								'$receipt_bill_pay_array[$b]',
								'$adj_amt_array[$b]',
								'$receipt_part_full_array[$b]',
								'$adj_dis_array[$b]',
								'$bill_received_amt_array[$b]',
								'$deduction_amt_array[$b]',
								'$bill_bal_amt_array[$b]',
								'$bill_remarks_array[$b]',
								'$receipt_type',
								'$receipt_mode',
								'$last_update_user',
								NOW(),
								'$last_update_user',
								NOW()
							)";
							$result=mysqli_query($con,$sql_bill[$b]);
						
							if(mysqli_errno($con)==0) 
							{ 
								$sql_success_code+=0;
								//$_SESSION['msg']="<div class='success-message'>Payment Entry Bill Successfully Added</div>";
								process_payment_entry_logging_disp(  " Payment Bill - Success" .mysqli_error($con));
								process_payment_entry_logging_disp(  "Payment Entry Id  - Bill - ".$commission_bill_entry_id_array[$b]);

							} 
							else 
							{
								$sql_success_code+=1;
								$sql_error_message=$sql_error_message."  BILL - ".$commission_bill_entry_id_array[$b] ." ";
								//$_SESSION['msg']="<div class='error-message'>Payment Entry Bill Details Not Addedd</div>";
								process_payment_entry_logging_disp(  " Payment Bill  - Fail" .mysqli_error($con));

	
								process_payment_entry_error_log("\n *******Fail **************** \n " .date_time());
								process_payment_entry_error_log("\n ".mysqli_error($con));
								process_payment_entry_error_log("\n".$sql_bill[$b]."\n ");								
							} //if(mysqli_errno($con)==0) 

				} //if($bill_payment_type_array[$b]=="Full" || $bill_payment_type_array[$b]=="Part")

			} //for($b=0;$b<$bill_entry_id_array_size;$b++)
							
							if($sql_success_code>0){
								$_SESSION['msg']="<div class='error-message'>Payment Entry (".$receipt_entry_id.") Partially Added ,  Bill Details Not Addedd ( ".$sql_error_message." )</div>";

							}else{
								$_SESSION['msg']="<div class='success-message'>Payment Entry (".$receipt_entry_id.")  Successfully Added</div>";
							}
							
		} //if($receipt_type=="Advance Receipt" )
		
	} else{ //if((mysqli_errno($con)==0)) - 2
		if($sql_success_code>0){
			$_SESSION['msg']="<div class='error-message'>Payment Entry (".$receipt_entry_id.") Partially Added , Chque/GR &  Bill Details Not Addedd ".$sql_error_message."</div>";

		}else{
			$_SESSION['msg']="<div class='success-message'>Payment Entry (".$receipt_entry_id.")  Successfully Added</div>";
		}

	} //if((mysqli_errno($con)==0)) - 2 - else



	
	} // if(($_REQUEST['manual_voucher_number']) && ($_SESSION['uid']==77))
	$_SESSION['uid']=11;

	release_connection($con);

} // function save()

/*
Update Function 
This function will update main table and will delete(update with delete tag) the child records 
and insert new records
*/

function update() {
	$con=get_connection();
	$log_file = "my-errors.log";
	if(($_REQUEST['receipt_type']) && ($_SESSION['uid']==77)) 
	{

		$_SESSION['uid']=11; // this is to prevent the call of function if Save is clicked twice .
		
		$last_update_user=$_SESSION['LOGID'];

		$receipt_entry_id=$_REQUEST['receipt_entry_id'];

		$receipt_date=$_REQUEST['receipt_date'];
		$receipt_type=$_REQUEST['receipt_type'];
		$receipt_mode=$_REQUEST['receipt_mode'];
/*
		$supplier_account_code=$_REQUEST['supplier_account_code'];
		$agent_account_code=$_REQUEST['agent_account_code'];
*/
		$receipt_amount=blankToZero($_REQUEST['receipt_amount']);

		$bank_name=blankToZero($_REQUEST['bank_name']);
		$cheque_number=blankToZero($_REQUEST['cheque_number']);

		$deduction_amount=blankToZero($_REQUEST['deduction_amount']);

		$narration=$_REQUEST['narration'];
		




		$receipt_date=convert_date($receipt_date);
		

		//$receipt_entry_id=0; // will populated once insert query for Main is executed.
		

		$update_sql_main= "UPDATE txt_commission_receipt_entry_main  set
			receipt_date='$receipt_date',
			receipt_type='$receipt_type', 
			receipt_mode='$receipt_mode',
			receipt_amount='$receipt_amount', 
			deduction_amount='$deduction_amount', 
			bank_name='$bank_name',
			cheque_number='$cheque_number',
			narration='$narration',
			last_update_user='$last_update_user',
			last_update_date=NOW()
			WHERE receipt_entry_id='$receipt_entry_id' ";
		
		//process_payment_entry_logging_disp(  $update_sql_main);



			
			$sql_error_message="";
			$sql_success_code=0;

			$result=mysqli_query($con,$update_sql_main);

			// process_payment_entry_logging_disp(  $result );

			if(mysqli_errno($con)==0) 
			{ 
				$sql_success_code+=0;
				//$_SESSION['msg']="<div class='success-message'>Payment Entry (".$payment_entry_id.") Successfully Added</div>";
				process_payment_entry_logging_disp("Receipt Entry Main - 1" .mysqli_error($con));
				process_payment_entry_logging_disp( "Receipt Entry Id - ".$receipt_entry_id);
			
			} 
			else 
			{
				$sql_success_code+=1;
				$sql_error_message= $sql_error_message. " -MAIN- ";

				process_payment_entry_logging_disp( " Payment Entry Main - 2" .mysqli_error($con));


				process_payment_entry_error_log("\n *******Fail **************** \n " .date_time());
				process_payment_entry_error_log("\n ".mysqli_error($con));
				process_payment_entry_error_log("\n".$update_sql_main."\n ");				

				//$_SESSION['msg']="<div class='error-message'>Payment Entry Not Addedd</div>";

			} // if(mysqli_errno($con)==0)

		if((mysqli_errno($con)==0)){			

			if($receipt_type=="Advance Receipt" ){

				// will  be implemented later

				// $receipt_entry_id=$REQUEST['receipt_entry_id'];



				$sql_bill_del="UPDATE txt_commission_receipt_bill_entry SET
				delete_tag='TRUE',
				last_update_user='$last_update_user',
				last_update_date=NOW(),
				delete_date=NOW()
				where receipt_entry_id='$receipt_entry_id' ";

				//process_payment_entry_logging_disp(  $sql_bill_del);		


				$result=mysqli_query($con,$sql_bill_del);

				// process_payment_entry_logging_disp(  $result);

				if(mysqli_errno($con)==0) 
				{ 
					$sql_success_code+=0;
					//$_SESSION['msg']="<div class='success-message'>Payment Entry (".$payment_entry_id.") Successfully Added</div>";
					process_payment_entry_logging_disp(  " Bill Entry Del 1" .mysqli_error($con));
					process_payment_entry_logging_disp(  "Receipt Entry Id - ".$receipt_entry_id);
			
				} 
				else 
				{
					$sql_success_code+=1;
					$sql_error_message=$sql_error_message."- BILL Entry Del -";

					process_payment_entry_logging_disp(  " BILL Entry Del" .mysqli_error($con));


					process_payment_entry_error_log("\n *******Fail **************** \n " .date_time());
					process_payment_entry_error_log("\n ".mysqli_error($con));
					process_payment_entry_error_log("\n".$sql_bill_del."\n ");				

					//$_SESSION['msg']="<div class='error-message'>Payment Entry Not Addedd</div>";

				}	//if(mysqli_errno($con)==0)			

			}else {
				// If any of the above Sql has failed we need to stop this execution
				if((mysqli_errno($con)==0)){




					$sql_bill_del="UPDATE txt_commission_receipt_bill_entry SET
					delete_tag='TRUE',
					last_update_user='$last_update_user',
					last_update_date=NOW(),
					delete_date=NOW()
					where receipt_entry_id='$receipt_entry_id' ";
	
					//process_payment_entry_logging_disp(  $sql_bill_del);		
	
	
					$result=mysqli_query($con,$sql_bill_del);
	
					// process_payment_entry_logging_disp(  $result);
	
					if(mysqli_errno($con)==0) 
					{ 
						$sql_success_code+=0;
						//$_SESSION['msg']="<div class='success-message'>Payment Entry (".$payment_entry_id.") Successfully Added</div>";
						process_payment_entry_logging_disp(  " Bill Entry Del 1" .mysqli_error($con));
						process_payment_entry_logging_disp(  "Receipt Entry Id - ".$receipt_entry_id);
				
					} 
					else 
					{
						$sql_success_code+=1;
						$sql_error_message=$sql_error_message."- BILL Entry Del -";
	
						process_payment_entry_logging_disp(  " BILL Entry Del" .mysqli_error($con));
	
	
						process_payment_entry_error_log("\n *******Fail **************** \n " .date_time());
						process_payment_entry_error_log("\n ".mysqli_error($con));
						process_payment_entry_error_log("\n".$sql_bill_del."\n ");				
	
						//$_SESSION['msg']="<div class='error-message'>Payment Entry Not Addedd</div>";
	
					}	//if(mysqli_errno($con)==0)							




					$commission_bill_entry_id_array=$_REQUEST['commission_bill_entry_id'];
					$commission_bill_number_array=$_REQUEST['commission_bill_number'];
					$commission_bill_date_array=$_REQUEST['commission_bill_date'];
					$commission_bill_entry_id_array_size=sizeof($commission_bill_entry_id_array);
		
					//$bill_voucher_number_array=$_REQUEST['bill_voucher_number'];
					//$bill_voucher_date_array=$_REQUEST['bill_voucher_date'];
					$commission_year_array=$_REQUEST['commission_year'];
					$commission_mode_array=$_REQUEST['commission_mode'];
					$commission_amt_bill_array=$_REQUEST['commission_amt_bill'];
					$commission_amt_pay_array=$_REQUEST['commission_amt_pay'];
					$adj_amt_array=$_REQUEST['adj_amt'];
					$adj_dis_array=$_REQUEST['adj_dis'];
					
					$receipt_part_full_array=$_REQUEST['receipt_part_full'];
					$receipt_bill_pay_array=$_REQUEST['receipt_bill_pay'];
		
					$deduction_amt_array=$_REQUEST['deduction_amt'];
					$bill_received_amt_array=$_REQUEST['bill_received_amt'];
		
					$bill_bal_amt_array=$_REQUEST['bill_bal_amt'];
		
					$bill_remarks_array=$_REQUEST['bill_remarks'];

					/*  To Start Here for updating Variable and  SQL */

				


					for($b=0;$b<$commission_bill_entry_id_array_size;$b++){

						//$bill_voucher_date_array[$b]=convert_date($bill_voucher_date_array[$b]);
						$commission_amt_bill_array[$b]=blankToZero($commission_amt_bill_array[$b]);
						$commission_amt_pay_array[$b]=blankToZero($commission_amt_pay_array[$b]);
		
						$adj_amt_array[$b]=blankToZero($adj_amt_array[$b]);				
						$adj_dis_array[$b]=blankToZero($adj_dis_array[$b]);
		
						$bill_received_amt_array[$b]=blankToZero($bill_received_amt_array[$b]);
						$deduction_amt_array[$b]=blankToZero($deduction_amt_array[$b]);
						$bill_bal_amt_array[$b]=blankToZero($bill_bal_amt_array[$b]);
		

						if($receipt_part_full_array[$b]=="Full" || $receipt_part_full_array[$b]=="Part"){

							$sql_bill[$b]="insert into 
							txt_commission_receipt_bill_entry 
							(   
								receipt_entry_id,
								receipt_date,
								commission_bill_entry_id,
								commission_bill_number,
								commission_bill_date,
								commission_year,
								commission_mode,
								commission_amt_bill,
								commission_amt_pay,
								receipt_bill_pay,
								amount_adjusted,
								receipt_part_full,
								deduction_adjusted,
								received_amount,
								deduction_amount,
								balance_amount,
								remark,
								receipt_type,
								receipt_mode,
								create_user,
								create_date,
								last_update_user,
								last_update_date
							)
							values
							(
								'$receipt_entry_id',
								'$receipt_date',
								'$commission_bill_entry_id_array[$b]',
								'$commission_bill_number_array[$b]',
								'$commission_bill_date_array[$b]',
								'$commission_year_array[$b]',
								'$commission_mode_array[$b]',
								'$commission_amt_bill_array[$b]',
								'$commission_amt_pay_array[$b]',
								'$receipt_bill_pay_array[$b]',
								'$adj_amt_array[$b]',
								'$receipt_part_full_array[$b]',
								'$adj_dis_array[$b]',
								'$bill_received_amt_array[$b]',
								'$deduction_amt_array[$b]',
								'$bill_bal_amt_array[$b]',
								'$bill_remarks_array[$b]',
								'$receipt_type',
								'$receipt_mode',
								'$last_update_user',
								NOW(),
								'$last_update_user',
								NOW()
							)";
							$result=mysqli_query($con,$sql_bill[$b]);
						
							if(mysqli_errno($con)==0) 
							{ 
								$sql_success_code+=0;
								//$_SESSION['msg']="<div class='success-message'>Payment Entry Bill Successfully Added</div>";
								process_payment_entry_logging_disp(  " Payment Bill - Success" .mysqli_error($con));
								process_payment_entry_logging_disp(  "Payment Entry Id  - Bill - ".$commission_bill_entry_id_array[$b]);

							} 
							else 
							{
								$sql_success_code+=1;
								$sql_error_message=$sql_error_message."  BILL - ".$commission_bill_entry_id_array[$b] ." ";
								//$_SESSION['msg']="<div class='error-message'>Payment Entry Bill Details Not Addedd</div>";
								process_payment_entry_logging_disp(  " Payment Bill  - Fail" .mysqli_error($con));

	
								process_payment_entry_error_log("\n *******Fail **************** \n " .date_time());
								process_payment_entry_error_log("\n ".mysqli_error($con));
								process_payment_entry_error_log("\n".$sql_bill[$b]."\n ");								
							} //if(mysqli_errno($con)==0) 
						}  //if($bill_payment_type_array[$b]=="Full" || $bill_payment_type_array[$b]=="Part")

					} //for($b=0;$b<$bill_entry_id_array_size;$b++)
					if($sql_success_code>0){
						$_SESSION['msg']="<div class='error-message'>Payment Entry Bill Details Not Addedd ".$sql_error_message."</div>";

					}else{
						$_SESSION['msg']="<div class='success-message'>Payment Entry (".$receipt_entry_id.")  Successfully Added</div>";
					} //if($sql_success_code>0)
				} //if((mysqli_errno($con)==0))

			} //if($receipt_type=="Advance Receipt"  ) - else

			if($sql_success_code>0){
				$_SESSION['msg']="<div class='error-message'>Payment Entry Bill Details Not Addedd ".$sql_error_message."</div>";

			}else{
				$_SESSION['msg']="<div class='success-message'>Payment Entry (Receipt No -".$receipt_entry_id.")  Successfully updated</div>";
			} //if($sql_success_code>0)

		} //if((mysqli_errno($con)==0))
		if($sql_success_code>0){
			$_SESSION['msg']="<div class='error-message'>Payment Entry Cheque/GR & Bill Details Not updated ".$sql_error_message."</div>";

		}else{
			$_SESSION['msg']="<div class='success-message'>Payment Entry (Receipt No -".$receipt_entry_id.")  Successfully updated</div>";
		} //if($sql_success_code>0)

	}	//if(($_REQUEST['payment_entry_id']) && ($_SESSION['uid']==77))	
	
	release_connection($con);
	
	} //function update()
//}



function delete()
{
	$con=get_connection();
	
	process_payment_entry_logging_disp(  "Inside Delete Function");
	
	if(($_REQUEST['receipt_entry_id']) && ($_SESSION['uid']==77))
	{

		
		$last_update_user=$_SESSION['LOGID'];

		$receipt_entry_id=$_REQUEST['receipt_entry_id'];

		
		$update_sql_main= "UPDATE txt_commission_receipt_entry_main  set
			delete_tag='TRUE',
			delete_user='$last_update_user',
			delete_date=NOW()
			WHERE receipt_entry_id='$receipt_entry_id' ";
		
		process_payment_entry_logging_disp(  $update_sql_main);

		$result=mysqli_query($con,$update_sql_main);

	

		$sql_bill_del="UPDATE txt_commission_receipt_bill_entry SET
			delete_tag='TRUE',
			delete_user='$last_update_user',
			delete_date=NOW()
			where receipt_entry_id='$receipt_entry_id' ";

		$result=mysqli_query($con,$sql_bill_del);		

	}
	
	release_connection($con);

} //function delete()


?>
