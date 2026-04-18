<table width="100%" border="0" align="center" style="border:1px solid #e5f1f8;background-color:#FFFFFF">

<tr>
  <td height="326" valign="top"><table width="100%" style="margin-top:0px" align="center" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                          <td valign="top">
                          <div class="content_padding">
      
  
  <table cellpadding="0" cellspacing="0" border="0">
          <tr>
           <td width="100%" height="138" valign="top">
            

<table class="tbl_border" width="100%" >
  <tr>
    <th width="24">S.No..</th>
    <!-- <th width="27">Edit</th>       -->
    <!-- <th width="27">View</th>             -->
    <th width="50">Bill Id</th>      
      <!--<th width="60">Voucher Number</th> -->
    <th width="100">Voucher Date</th>
    <th width="54">Bill Number</th>
    <th width="100">Bill Date</th>

    <th width="250">Supplier </th>
    <th width="250">Buyer </th>
    <th width="50">Dis %</th>
    <th width="80">Net Amt</th>
    <th width="50">GST Amt</th>
    <th width="80">Bill Amount</th>
    <th width="80">Bill Page 1</th>
    <th width="80">Bill Page 2</th>
    <th width="80">Bill Page 3</th>
    <th width="80">Bill Page 4</th>
    <th width="80">Bill Page 5</th>


<!--        <th width="50">Delete</th> -->
  </tr>
<?php 

  // function to convert the date formate from yyyy-mm-dd to dd-mm-yyyy to store in mysql
  function return_folder_name_from_entry_date($date) {
	  
    if(!empty($date) && $date!='0000-00-00' && $date!='1970-01-01' && $date!='2080-01-01') {
	  	$temp_date=explode("-",$date);
		$dd=$temp_date[2];
		$mm=$temp_date[1];
		$yy=$temp_date[0];
		$new_date=$dd."-".$mm."-".$yy;

    $current_year=$yy;
    $next_year=$yy+1;
    $prev_year=$yy-1;

    if($mm > 3){

      $main_folder=$current_year."_".$next_year;

    }else{

      $main_folder=$prev_year."_".$current_year;

    }

    $month_folder=$current_year."_".$mm;

    $day_folder=$current_year."_".$mm."_".$dd;

    $path="/bill_images/".$main_folder."/".$month_folder."/". $day_folder;

		return $path;
	} else {
		return;
	}				
  }
  // end function 	



$search_supplier_code="";
if(isset ($_REQUEST['search_supplier_account_code'])){
  $search_supplier_code=$_REQUEST['search_supplier_account_code'];
}


$search_buyer_code="";
if (isset($_REQUEST['search_buyer_account_code'])){
  $search_buyer_code=$_REQUEST['search_buyer_account_code'];
}




$bill_start_date="";
if(isset($_REQUEST['bill_start_date'])){
  $bill_start_date=$_REQUEST['bill_start_date'];
}


$bill_end_date="";
if(isset($_REQUEST['bill_end_date'])){
  $bill_end_date=$_REQUEST['bill_end_date'];
}


$vou_start_date="";

if(isset($_REQUEST['vou_start_date'])){
  $vou_start_date=$_REQUEST['vou_start_date'];
}

$vou_end_date="";

if(isset($_REQUEST['vou_end_date'])){
  $vou_end_date=$_REQUEST['vou_end_date'];

}

$search_bill_entry_id="";
if(isset($_REQUEST['search_bill_entry_id'])){
  $search_bill_entry_id=$_REQUEST['search_bill_entry_id'];
}

$search_bill_number="";

if(isset($_REQUEST['search_bill_number'])){
  $search_bill_number=$_REQUEST['search_bill_number'];
}


$order="";
if(isset($_REQUEST['order'])){
  $order=$_REQUEST['order'];
}

?>    
<?php
$con=get_connection();



$sql="select * from txt_company where delete_tag='FALSE' order by company_id ASC";
$result=mysqli_query($con,$sql);

$rowcount=0;
// Creating Company Array with reverse Key Value Pair 
// because array_search function searched value and returns key
// first value Dummy to show the position of details
$company_array=array("Value"=>"Key"); 
while($rs=mysqli_fetch_array($result))
{
$companyRow[$rowcount][0]=$rs['company_id'];
$companyRow[$rowcount][1]=$rs['firm_name'];
$com_array=array($rs['firm_name']=>$rs['company_id']);
$company_array=array_merge($company_array,$com_array);


$rowcount++;

}

$result -> free_result();

$sql="select * from txt_bill_entry 
where delete_tag='FALSE' ";
if($search_supplier_code!=''){
  $sql.=" AND supplier_account_code='$search_supplier_code' ";
}

if($search_buyer_code!=''){
  $sql.=" AND buyer_account_code='$search_buyer_code' ";
}

$sql_bill_start_date=convert_date($bill_start_date);
if($bill_start_date!=''){
  $sql.=" AND bill_date>='$sql_bill_start_date' ";
}

$sql_bill_end_date=convert_date($bill_end_date);
if($bill_end_date!=''){
  $sql.=" AND bill_date<='$sql_bill_end_date' ";
}

$sql_vou_start_date=convert_date($vou_start_date);
if($vou_start_date!=''){
  $sql.=" AND voucher_date>='$sql_vou_start_date' ";
}

$sql_vou_end_date=convert_date($vou_end_date);


if($vou_end_date!=''){
  $sql.=" AND voucher_date<='$sql_vou_end_date' ";
}

if($search_bill_entry_id!=''){
$sql.=" AND bill_entry_id ='$search_bill_entry_id' ";
}
if($search_bill_number!=''){
$sql.=" AND bill_number ='$search_bill_number' ";
}


// Entry Date
$sql_order_by=' voucher_date DESC ,bill_entry_id DESC';
if($order=='Bill Date'){
$sql_order_by=' bill_date DESC,bill_number DESC';
}


$sql.=" ORDER BY $sql_order_by ";
$result=mysqli_query($con,$sql);
//echo $sql ;
$col_switch=0;
$td_col="style='background-color:#FF0000'";

$count=0;
while($rs=mysqli_fetch_array($result))
{
  if ($col_switch==0){
    $col_switch=1;
    $td_col="style='background-color:#FFFFFF'";
  }else{
    $col_switch=0;
    $td_col="style='background-color:#99FF99'";
  }
  $bill_entry_id=$rs[0];
  echo "<tr $td_col >";
  echo "<td >".++$count."</td>";
  echo "<td>".$rs['bill_entry_id']."</td>";
?>
 <input type='hidden' name='bill_entry_id[]' id='bill_entry_id'  value='<?php echo $rs['bill_entry_id'] ?>' >
<?php

    echo "<td>".rev_convert_date($rs['voucher_date'])."</td>";
    $bill_num=$rs['bill_number'];
    echo "<td>".$rs['bill_number']."</td>";
    echo "<td>".rev_convert_date($rs['bill_date'])."</td>";




    $disp_transport_name=$disp_supp_name=$disp_buyer_name=$disp_agent_name="Not Found";



    $disp_supp_name=array_search($rs['supplier_account_code'],$company_array);
    $disp_buyer_name=array_search($rs['buyer_account_code'],$company_array);

    echo "<td>".$disp_supp_name."</td>";

    echo "<td>".$disp_buyer_name."</td>";


    echo "<td align='right' >".zeroToBlank($rs['discount_percentage'])."</td>";
    echo "<td align='right'>".$rs['net_amount']."</td>";
    echo "<td align='right'>".$rs['gst_amount']."</td>";

    echo "<td align='right'>".$rs['bill_amount']."</td> ";


   // echo "</tr> <tr> <td colspan=3>";
    echo "<td align='center'> ";



    if($rs['bill_filename1']!="") { ?>
        <?php 
        /*
        <a href='<?php echo $web_path; ?>bill_entry/upload/<?php echo $rs['bill_upload']; ?>'>Bill Copy</a>
        */
        ?>
        <img src="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename1'] ?> " alt='Bill' width='500' height='600'>

        <br> <br>
        <a href="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename1'] ?> "><?php echo $rs['bill_filename1'] ?> </a>
        <br> To Remove Please Enter Three Dash --- in text box below<br> 
        <input type=text size=10 name="bill_filename1_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename1" >


    <?php 
    } else { ?>
            <!-- <img src="../images/upload_2.png" alt="no image" id="prev0" style="width:25px;height:25px;" name="prev_img1[]" /> -->
            <?php 
            /*
            <input type="file" name="bill_upload_<?php echo $rs['bill_entry_id'] ?>" id="bill_upload"> 
            */
            ?>
            <input type=text size=10 name="bill_filename1_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename1" >
    <?php 
    } 
    echo "</td>";


    echo "<td align='center'> ";
    if($rs['bill_filename2']!="") { ?>
        <?php 
        /*
        <a href='<?php echo $web_path; ?>bill_entry/upload/<?php echo $rs['bill_upload']; ?>'>Bill Copy</a>
        */
        ?>
        <img src="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename2'] ?> " alt='Bill' width='500' height='600'>
        <br> <br>

        <a href="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename2'] ?> ">B<?php echo $rs['bill_filename2'] ?></a>     
        <br> <br>
        <input type=text size=10 name="bill_filename2_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename2" >

    <?php 
    } else { ?>
            <!-- <img src="../images/upload_2.png" alt="no image" id="prev0" style="width:25px;height:25px;" name="prev_img1[]" /> -->
            <?php 
            /*
            <input type="file" name="bill_upload_<?php echo $rs['bill_entry_id'] ?>" id="bill_upload"> 
            */
            ?>
            <input type=text size=10 name="bill_filename2_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename2" >
    <?php 
    } 
    echo "</td>";


    echo "<td align='center'> ";
    if($rs['bill_filename3']!="") { ?>
        <?php 
        /*
        <a href='<?php echo $web_path; ?>bill_entry/upload/<?php echo $rs['bill_upload']; ?>'>Bill Copy</a>
        */
        ?>
        <img src="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename3'] ?> " alt='Bill' width='500' height='600'>
        <br> <br>


        <a href="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename3'] ?> "><?php echo $rs['bill_filename3'] ?></a>          
        <br> <br>
        <input type=text size=10 name="bill_filename3_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename3" >        
    <?php 
    } else { ?>
            <!-- <img src="../images/upload_2.png" alt="no image" id="prev0" style="width:25px;height:25px;" name="prev_img1[]" /> -->
            <?php 
            /*
            <input type="file" name="bill_upload_<?php echo $rs['bill_entry_id'] ?>" id="bill_upload"> 
            */
            ?>
            <input type=text size=10 name="bill_filename3_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename3" >
    <?php 
    } 
    echo "</td>";


    echo "<td align='center'> ";
    if($rs['bill_filename4']!="") { ?>
        <?php 
        /*
        <a href='<?php echo $web_path; ?>bill_entry/upload/<?php echo $rs['bill_upload']; ?>'>Bill Copy</a>
        */
        ?>
        <img src="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename4'] ?> " alt='Bill' width='500' height='600'>
        <br> <br>


        <a href="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename4'] ?> "><?php echo $rs['bill_filename4'] ?></a>     
        <br> <br>

        <input type=text size=10 name="bill_filename4_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename4" >        
    <?php 
    } else { ?>
            <!-- <img src="../images/upload_2.png" alt="no image" id="prev0" style="width:25px;height:25px;" name="prev_img1[]" /> -->
            <?php 
            /*
            <input type="file" name="bill_upload_<?php echo $rs['bill_entry_id'] ?>" id="bill_upload"> 
            */
            ?>
            <input type=text size=10 name="bill_filename4_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename4" >
    <?php 
    } 
    echo "</td>";


    echo "<td align='center'> ";
    if($rs['bill_filename5']!="") { ?>
        <?php 
        /*
        <a href='<?php echo $web_path; ?>bill_entry/upload/<?php echo $rs['bill_upload']; ?>'>Bill Copy</a>
        */
        ?>
        <img src="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename5'] ?> " alt='Bill' width='500' height='600'>
        <br>  <br>


        <a href="<?php echo return_folder_name_from_entry_date($rs['voucher_date']) ?>/<?php echo $rs['bill_filename5'] ?> "><?php echo $rs['bill_filename5'] ?></a>        
        <br> <br> 
        <input type=text size=10 name="bill_filename5_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename5" >
    <?php 
    } else { ?>
            <!-- <img src="../images/upload_2.png" alt="no image" id="prev0" style="width:25px;height:25px;" name="prev_img1[]" /> -->
            <?php 
            /*
            <input type="file" name="bill_upload_<?php echo $rs['bill_entry_id'] ?>" id="bill_upload"> 
            */
            ?>
            <input type=text size=10 name="bill_filename5_<?php echo $rs['bill_entry_id'] ?>" id="bill_filename5" >
    <?php 
    } 
  echo "</td>";
  echo"</tr>";

}

?>
</table>
