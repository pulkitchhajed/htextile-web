<?php include("../includes/check_session.php");
include("../includes/config.php");
error_reporting(0);?>

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


echo "<script language='javascript'>";
echo "location.href='index.php'";
echo "</script>";


function save() 
{
	
		// get data 
	if(($_REQUEST['firm_name']) && ($_SESSION['uid']==77)) 
	{
		$con=get_connection();

		$firm_name=$_REQUEST['firm_name']; 
		$address=$_REQUEST['address'];
		$city=$_REQUEST['city'];
		$state=$_REQUEST['state'];
		$pincode=$_REQUEST['pincode'];
		$gstin=$_REQUEST['gstin'];
		$office_phone=$_REQUEST['office_phone'];
		$contact_person=$_REQUEST['contact_person'];
		$contact_number=$_REQUEST['contact_number'];
		$contact_person_2=$_REQUEST['contact_person_2'];
		$contact_number_2=$_REQUEST['contact_number_2'];		
		$sms_number=$_REQUEST['sms_number'];
		$whatsapp_number=$_REQUEST['whatsapp_number'];
		$email=$_REQUEST['email'];
		$website=$_REQUEST['website'];

		$group_id=$_REQUEST['group_name'];

		if($group_id=="" ){ 
			$group_id=0; 
		}		

		$commission_percentage=$_REQUEST['commission_percentage'];
		$comm_stat_pref=$_REQUEST['comm_stat_pref'];
		
		if($commission_percentage==""){ 
				$commission_percentage=0; 
		}
		$firm_type=$_REQUEST['firm_type'];
		$reference=$_REQUEST['reference'];
		$remarks=$_REQUEST['remarks'];
		$products=$_REQUEST['products'];
		$brands=$_REQUEST['brands'];
		$pan_number=$_REQUEST['pan_number'];
		$agent_id=$_REQUEST['agent_id'];

		if($agent_id==""){ 
			$agent_id=0; 
		}

		$bank_name=$_REQUEST['bank_name'];
		$bank_branch=$_REQUEST['bank_branch'];
		$ifsc_code=$_REQUEST['ifsc_code'];
		$bank_city=$_REQUEST['bank_city'];
		$bank_account_number=$_REQUEST['bank_account_number'];		


		$last_update_user=$_SESSION['LOGID'];

		
		if($_FILES['visiting_card']['name']!="") { // only if files are selected -- to pevent blank entry in table
		$file_name1 = $_FILES['visiting_card']['name'];
		$file_size1 =$_FILES['visiting_card']['size'];
		$file_tmp1 =$_FILES['visiting_card']['tmp_name'];
		$file_type1=$_FILES['visiting_card']['type'];	
        if($file_size1 > 2097152){
			$errors[]='File size must be less than 2 MB';
        }		
			$random_digit=time()+3;
			$file_name1 = str_replace(' ', '_', $file_name1);
			$file_name1 = $random_digit."_".$file_name1;
             move_uploaded_file($file_tmp1,"upload/".$file_name1); //echo $file_name1;
			 $visiting_card=$file_name1;
		}
			 
			 
			 
		if($_FILES['photo_1']['name']!="") { // only if files are selected -- to pevent blank entry in table
		$file_name1 = $_FILES['photo_1']['name'];
		$file_size1 =$_FILES['photo_1']['size'];
		$file_tmp1 =$_FILES['photo_1']['tmp_name'];
		$file_type1=$_FILES['photo_1']['type'];	
        if($file_size1 > 2097152){
			$errors[]='File size must be less than 2 MB';
        }		
			$random_digit=time()+4;
			$file_name1 = str_replace(' ', '_', $file_name1);
			$file_name1 = $random_digit."_".$file_name1;
             move_uploaded_file($file_tmp1,"upload/".$file_name1); //echo $file_name1;
			 $photo_1=$file_name1;
		}
			 
			 
		if($_FILES['photo_2']['name']!="") { // only if files are selected -- to pevent blank entry in table
		$file_name1 = $_FILES['photo_2']['name'];
		$file_size1 =$_FILES['photo_2']['size'];
		$file_tmp1 =$_FILES['photo_2']['tmp_name'];
		$file_type1=$_FILES['photo_2']['type'];	
        if($file_size1 > 2097152){
			$errors[]='File size must be less than 2 MB';
        }		
			$random_digit=time()+5;
			$file_name1 = str_replace(' ', '_', $file_name1);
			$file_name1 = $random_digit."_".$file_name1;
             move_uploaded_file($file_tmp1,"upload/".$file_name1); //echo $file_name1;
			 $photo_2=$file_name1;
		}
	
	
		$sql="insert into txt_company
		(firm_name,
		address,
		city,
		state,
		pincode,
		gstin,
		office_phone,
		contact_person,
		contact_number,
		contact_person_2,
		contact_number_2,		
		sms_number,
		whatsapp_number,
		email,
		website,
		group_id,
		commission_percentage,
		comm_stat_pref,
		firm_type,
		reference,
		remarks,
		pan_number,
		products,
		brands,
		visiting_card,
		photo_1,
		photo_2,
		create_user,
		create_date,
		last_update_user,
		last_update_date,
		agent_id,
		bank_name,
		bank_branch,
		ifsc_code,
		bank_city,
		bank_account_number) 
		values
		(UPPER('$firm_name'),
		'$address',
		'$city',
		'$state',
		'$pincode',
		'$gstin',
		'$office_phone',
		'$contact_person',
		'$contact_number',
		'$contact_person_2',
		'$contact_number_2',
		'$sms_number',
		'$whatsapp_number',
		'$email',
		'$website',
		'$group_id',
		'$commission_percentage',
		'$comm_stat_pref',
		'$firm_type',
		'$reference',
		'$remarks',
		'$pan_number',
		'$products',
		'$brands',
		'$visiting_card',
		'$photo_1',
		'$photo_2',
		'$last_update_user',
		NOW(),
		'$last_update_user',
		NOW(),
		'$agent_id',
		'$bank_name',
		'$bank_branch',
		'$ifsc_code',
		'$bank_city',
		'$bank_account_number')";
		//echo $sql; 

		$log_file = "my-errors.log";

		//error_log($sql,3,$log_file);
		//echo $sql;

		$result=mysqli_query($con,$sql); 
		/*
		echo "Pritesh-";
		echo $result;
		echo "-Shah-";
		echo mysqli_error($con);
		echo "-Indore";
		*/
		
		
			if(mysqli_error($con)=='') 
			{ 
				echo "into Session set";
				$_SESSION['msg'] .="<div class='success-message'>Company Name : $firm_name Type : $firm_type Successfully Added</div>";
			} 
			else 
			{
				echo "into Error set";
				$_SESSION['msg']="<div class='error-message'>Company Name : $firm_name Not Addedd Message: ". mysqli_error($con) ."</div>";
				//$_SESSION['msg']=$msg; 
				
			}

			$_SESSION['uid']=11;
			
			release_connection($con);
	}

	

}




function update()
{
	
	if(($_REQUEST['firm_name']) && ($_SESSION['uid']==77)) 
	{
		$con=get_connection();
		
		$company_id=$_REQUEST['company_id'];
		$firm_name=$_REQUEST['firm_name'];
		$address=$_REQUEST['address'];
		$city=$_REQUEST['city'];
		$state=$_REQUEST['state'];
		$pincode=$_REQUEST['pincode'];
		$gstin=$_REQUEST['gstin'];
		$office_phone=$_REQUEST['office_phone'];
		$contact_person=$_REQUEST['contact_person'];
		$contact_number=$_REQUEST['contact_number'];
		$contact_person_2=$_REQUEST['contact_person_2'];
		$contact_number_2=$_REQUEST['contact_number_2'];
		$sms_number=$_REQUEST['sms_number'];
		$whatsapp_number=$_REQUEST['whatsapp_number'];
		$email=$_REQUEST['email'];
		$website=$_REQUEST['website'];
		$group_id=$_REQUEST['group_name'];



		if($group_id=="" ){ 
			$group_id=0; 
		}		

		$commission_percentage=$_REQUEST['commission_percentage'];
		$comm_stat_pref=$_REQUEST['comm_stat_pref'];
		
		if($commission_percentage==""){ 
			$commission_percentage=0; 
		}

		$firm_type=$_REQUEST['firm_type'];
		$reference=$_REQUEST['reference'];
		$remarks=$_REQUEST['remarks'];
		$pan_number=$_REQUEST['pan_number'];
		$products=$_REQUEST['products'];
		$brands=$_REQUEST['brands'];		
		$agent_id=$_REQUEST['agent_id'];
		
		if($agent_id==""){ 
			$agent_id=0; 
		}

		$last_update_user=$_SESSION['LOGID'];

		$bank_name=$_REQUEST['bank_name'];
		$bank_branch=$_REQUEST['bank_branch'];
		$ifsc_code=$_REQUEST['ifsc_code'];
		$bank_city=$_REQUEST['bank_city'];
		$bank_account_number=$_REQUEST['bank_account_number'];		

		if($_FILES['visiting_card']['name']!="") { // only if files are selected -- to pevent blank entry in table
			$file_name1 = $_FILES['visiting_card']['name'];
			$file_size1 =$_FILES['visiting_card']['size'];
			$file_tmp1 =$_FILES['visiting_card']['tmp_name'];
			$file_type1=$_FILES['visiting_card']['type'];	
			if($file_size1 > 2097152){
				$errors[]='File size must be less than 2 MB';
			}		
			$random_digit=time()+3;
			$file_name1 = str_replace(' ', '_', $file_name1);
			$file_name1 = $random_digit."_".$file_name1;
				move_uploaded_file($file_tmp1,"upload/".$file_name1); //echo $file_name1;
				$visiting_card=$file_name1;
		}

		if($_FILES['photo_1']['name']!="") { // only if files are selected -- to pevent blank entry in table
			$file_name1 = $_FILES['photo_1']['name'];
			$file_size1 =$_FILES['photo_1']['size'];
			$file_tmp1 =$_FILES['photo_1']['tmp_name'];
			$file_type1=$_FILES['photo_1']['type'];	
			if($file_size1 > 2097152){
				$errors[]='File size must be less than 2 MB';
			}		
			$random_digit=time()+4;
			$file_name1 = str_replace(' ', '_', $file_name1);
			$file_name1 = $random_digit."_".$file_name1;
				move_uploaded_file($file_tmp1,"upload/".$file_name1); //echo $file_name1;
				$photo_1=$file_name1;
		}
					
					
		if($_FILES['photo_2']['name']!="") { // only if files are selected -- to pevent blank entry in table
			$file_name1 = $_FILES['photo_2']['name'];
			$file_size1 =$_FILES['photo_2']['size'];
			$file_tmp1 =$_FILES['photo_2']['tmp_name'];
			$file_type1=$_FILES['photo_2']['type'];	
			if($file_size1 > 2097152){
				$errors[]='File size must be less than 2 MB';
			}		
			$random_digit=time()+5;
			$file_name1 = str_replace(' ', '_', $file_name1);
			$file_name1 = $random_digit."_".$file_name1;
				move_uploaded_file($file_tmp1,"upload/".$file_name1); //echo $file_name1;
				$photo_2=$file_name1;
		}
		
				

		$sql  = " update txt_company set ";
		$sql .= " firm_name=UPPER('$firm_name'),";
		$sql .= " address='$address',";
		$sql .= " city='$city',";
		$sql .= " state='$state',"; 
		$sql .= " pincode='$pincode',";
		$sql .= " gstin='$gstin',"; 
		$sql .= " office_phone='$office_phone',"; 
		$sql .= " contact_person='$contact_person',";
		$sql .= " contact_number='$contact_number',";
		$sql .= " contact_person_2='$contact_person_2',";
		$sql .= " contact_number_2='$contact_number_2',";		
		$sql .= " sms_number='$sms_number',";
		$sql .= " whatsapp_number='$whatsapp_number',";
		$sql .= " email='$email',website='$website',";
		$sql .= " group_id='$group_id',";
		$sql .= " commission_percentage='$commission_percentage',";
		$sql .= " comm_stat_pref='$comm_stat_pref',";
		$sql .= " firm_type='$firm_type',";
		$sql .= " reference='$reference',";
		$sql .= " remarks='$remarks',";
		$sql .= " products='$products',";
		$sql .= " brands='$brands',";
		$sql .= " last_update_user='$last_update_user',";
		$sql .= " last_update_date=NOW(),";
		$sql .= " agent_id='$agent_id',";

		$sql .= " bank_name='$bank_name',";
		$sql .= " bank_branch='$bank_branch',";
		$sql .= " ifsc_code='$ifsc_code',";
		$sql .= " bank_city='$bank_city',";
		$sql .= " bank_account_number='$bank_account_number',";		
		
		if($visiting_card != "") {
			$sql .= " visiting_card='$visiting_card',";	}
		if($photo_1 != "") {
			$sql .= " photo_1='$photo_2',";	}
		if($photo_2 != "") {
			$sql .= " photo_2='$photo_2',";	}
			
		$sql .= " pan_number='$pan_number'";
		$sql .= " where company_id='$company_id'";

		$log_file = "my-errors.log";
		//error_log($sql,3,$log_file);

		$result=mysqli_query($con,$sql); 
		//echo $sql;
		//echo $result;
		
		if(mysqli_error($con)=='') 
		{ 
			$_SESSION['msg']="<div class='success-message'> " .  mysqli_error($con). "Company id : $company_id  Name : $firm_name  Type: $firm_type Successfully Updated</div>";
		} 
		else 
		{
			$_SESSION['msg']="<div class='error-message'>Company Not Updated Error : ". mysqli_error($con) ." </div>";
			//$_SESSION['msg']=$msg;
			
		}

				$_SESSION['uid']=11;

		release_connection($con);
				
	}
}



function delete()
{
	if(($_REQUEST['company_id']) && ($_SESSION['uid']=77))
	{
		$con=get_connection();
		$company_id=$_REQUEST['company_id'];
		$delete_user=$_SESSION['LOGID'];

		//$sql="delete from txt_company where company_id='$company_id'";
		$sql="update txt_company set delete_tag='TRUE',";
		$sql .= " delete_user='$delete_user',";
		$sql .= " delete_date=NOW()  ";
		$sql .= " where company_id='$company_id'";

		$result=mysqli_query($con,$sql);	
		if(mysqli_error($con)=='') 
		{ 
			$_SESSION['msg']="<div class='success-message'>Company Id : $company_id  Successfully Deleted</div>";
		} 
		else 
		{
			$msg=getSqlMessage(mysqli_error($con),"Company Not Deleted");
			$_SESSION['msg']="<div class='error-message'>$msg  :  ".mysqli_error($con)." </div>";
		}	
		
		release_connection($con);
	
	}
}




function removeFile()
{
	if(($_REQUEST['company_id']) && ($_SESSION['uid']=77))
	{
	$con=get_connection();
	$company_id=$_REQUEST['company_id'];
		$ft=$_REQUEST['ft'];  // ft= file type 
		
		if($ft=="visiting_card") 
		{
			$sql="select visiting_card from txt_company where company_id='$company_id'";
			$sql_update="update txt_company set visiting_card='' where company_id='$company_id'";
		}
		
		if($ft=="photo_1") 
		{
			$sql="select photo_1 from txt_company where company_id='$company_id'";
			$sql_update="update txt_company set photo_1='' where company_id='$company_id'";
		}
		
		if($ft=="photo_2") 
		{
			$sql="select photo_2 from txt_company where company_id='$company_id'";
			$sql_update="update txt_company set photo_2='' where company_id='$company_id'";
		}
		
		$result=mysqli_query($con,$sql);	
		//$row=mysql_fetch_row($result);
			
		mysqli_query($con,$sql_update);
		
		if(mysqli_errno($con)==0) { 
					$_SESSION['msg']="<div class='success-message'>File Successfully Deleted</div>";
				} else {
					$msg=getSqlMessage(mysqli_error($con),"File Not Deleteted");
					$_SESSION['msg']="<div class='success-message'>$msg</div>";
				}
				
				
		echo "<script language='javascript'>";
		echo "location.href='edit_company.php?company_id=$company_id'";
		echo "</script>";

		release_connection($con);

	}
}
?>