<?php include("../includes/check_session.php");
include("../includes/config.php"); ?>

<?php 

$search_supplier_code="";
if(isset($_REQUEST['search_supplier_account_code'])){
  $search_supplier_code=$_REQUEST['search_supplier_account_code'];
}
$search_buyer_code="";
if(isset($_REQUEST['search_buyer_account_code'])){
  $search_buyer_code=$_REQUEST['search_buyer_account_code'];
}


$vou_start_date="";
if(isset($_REQUEST['vou_start_date'])){
  $vou_start_date=$_REQUEST['vou_start_date'];
 // echo $vou_start_date;
}

$vou_end_date="";
if(isset($_REQUEST['vou_end_date'])){
  $vou_end_date=$_REQUEST['vou_end_date'];
  //echo $vou_end_date;
}

?>

<form  name='process_multiple' method="post" id="process_multiple" enctype="multipart/form-data" action='upload_multiple_bill_imgfile_name.php' onsubmit="">

<input type='hidden' name='search_supplier_code' id='search_supplier_code' value='<?php echo $search_supplier_code ?>' >
<input type='hidden' name='search_buyer_code' id='search_buyer_code' value='<?php echo $search_buyer_code ?>' >
<input type='hidden' name='vou_start_date' id='vou_start_date' value='<?php echo $vou_start_date ?>' >
<input type='hidden' name='vou_end_date' id='vou_end_date' value='<?php echo $vou_end_date ?>' >

<input type='hidden' name='bill_report_disp' id='bill_report_disp' value='OK' >

<?php

//bill_entry_id

$bill_entry_id_array = array();
if(isset($_REQUEST['bill_entry_id'])){
    $bill_entry_id_array=$_REQUEST['bill_entry_id'];
    //echo $bill_entry_id_array;
}
$bill_entry_id_array_size=sizeof($bill_entry_id_array);


//bill_upload

//$bill_upload_array = array();
//if(isset($_REQUEST['bill_upload'])){
   // $bill_upload_array=$_REQUEST['bill_upload'];
//}
//$bill_upload_array_size=sizeof($bill_upload_array);

$con=get_connection();

for($b=0;$b<$bill_entry_id_array_size;$b++){

    //echo "Pritesh -- <BR>";
    $bill_entry_id= $bill_entry_id_array[$b];
    //echo $bill_entry_id;
    //echo "!--";

    //$_FILES['bill_upload']['name']
    //$bill_upload_element=$_FILES['bill_upload_'.$bill_entry_id]['name'];

    $bill_filename1=$_REQUEST['bill_filename1_'.$bill_entry_id];
    $bill_filename2=$_REQUEST['bill_filename2_'.$bill_entry_id];
    $bill_filename3=$_REQUEST['bill_filename3_'.$bill_entry_id];
    $bill_filename4=$_REQUEST['bill_filename4_'.$bill_entry_id];
    $bill_filename5=$_REQUEST['bill_filename5_'.$bill_entry_id];
    //echo $bill_upload_element;
    //echo $bill_upload_array[$b]['name'];
    //echo "!--";
    //echo "!-- <BR>";

    $last_update_user=$_SESSION['LOGID'];
    $sql =" update txt_bill_entry set  ";
    $sql .=" last_update_user='$last_update_user' ";


   $sql_filename=""; 
    // File Name Must be filled
    if($bill_filename1 != "") {
      if ($bill_filename1=="---"){
        $sql_filename .= " , bill_filename1=''  ";
      } else {
        $sql_filename .= " , bill_filename1='$bill_filename1'  ";
      }
      
    }

    // File Name Must be filled
    if($bill_filename2 != "") {
      if ($bill_filename2=="---"){
        $sql_filename .= " , bill_filename2='' ";

      } else {
        $sql_filename .= " , bill_filename2='$bill_filename2' ";
        
      }
      
     
    }

    // File Name Must be filled
    if($bill_filename3 != "") {
      if ($bill_filename3=="---"){
        $sql_filename .= " , bill_filename3=''  ";
      } else {
        $sql_filename .= " , bill_filename3='$bill_filename3'  ";
      }
    }
    // File Name Must be filled
    if($bill_filename4 != "") {
      if ($bill_filename4=="---"){
        $sql_filename .= " , bill_filename4=''  ";
      } else {
        $sql_filename .= " , bill_filename4='$bill_filename4'  ";
      }
    }


    // File Name Must be filled
    if($bill_filename5 != "") {
      if ($bill_filename5=="---"){
        $sql_filename .= " , bill_filename5=''  ";
      } else {
        $sql_filename .= " , bill_filename5='$bill_filename5'  ";
      }
    }

    if($sql_filename != "")
    {
      $sql.=$sql_filename;
    }

    $sql .= "        
    where bill_entry_id='$bill_entry_id' ";


    if($sql_filename != "")
    {
        echo $sql;
        echo "<br>";
        $result=mysqli_query($con,$sql);
        echo $result;
        echo "<br>";        
    }




    
}




?>

</form>
<script>
document.getElementById('process_multiple').submit();
</script>