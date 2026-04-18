<?php 
include("log.php");

// DRY approch -- Don't Repeat Yourself 
if(false) {
	// do nothing
} else {
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASSWORD', '');
	//define('DB_DATABASE', 'agencysystemdemo');	
	define('DB_DATABASE', 'htex');	
}
/*
$mysql = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Error:".mysql_error());
mysql_select_db(DB_DATABASE);
*/
function get_connection(){
	$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE) or die("Error:".mysqli_connect_error());
	return $con;
}

function release_connection($con){
	mysqli_close($con);
}
	//error_reporting(0);
  // function to convert the date formate from dd-mm-yy to yy/mm/dd to store in mysql
  function convert_date($date) {
    if(!empty($date) ) {
		if($date=='00-00-0000' || $date==''){
			return '1970-01-01';
		}
	  	$temp_date=explode("-",$date);
		$dd=$temp_date[0];
		$mm=$temp_date[1];
		$yy=trim($temp_date[2]);
		$new_date=$yy."-".$mm."-".$dd;
		return $new_date;
	} else {
		return '1970-01-01';
	}				
  }
  // end function 	


  // function to convert the date formate from yyyy-mm-dd to dd-mm-yyyy to store in mysql
  function rev_convert_date($date) {
	  
    if(!empty($date) && $date!='0000-00-00' && $date!='1970-01-01' && $date!='2080-01-01') {
	  	$temp_date=explode("-",$date);
		$dd=$temp_date[2];
		$mm=$temp_date[1];
		$yy=$temp_date[0];
		$new_date=$dd."-".$mm."-".$yy;
		return $new_date;
	} else {
		return;
	}				
  }
  // end function 	

  /*
  function db_query($sql) {
	$result_set=array();
	$result=mysql_query($sql);
	while($rs=mysql_fetch_assoc($result)) {
		$result_set[]=$rs;
	}
	return $result_set;
 }	

 */
/********************************************************************
		FUNCTION FOR MYSQL SUCCESS/ERROR NUMBER AND THEIR MESSAGES
*********************************************************************/
	function getSqlMessage($error_no,$str) {
		switch ($error_no) {
/*			case 0:
				// 0 for no error or Success of query
				$msg= " Successfully";
				break;
*/			case 1451:
				$msg= " Can not delete as it linked with some another data";
				break;
			case 1452:
				$msg= " Can not add as it linked with some another data";
				break;
		}
		return $str.$msg;
	}
	
/**********************************************************
		FUNCTION dateDiff($start_date, $end_date) gives the number of days between two dates in yyyy-mm-dd format
***********************************************************/		
 function dateDiff($start, $end) {
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $start_ts - $end_ts;
	if($end_ts=="") 
		return 0;
	return round($diff / 86400);
}	

function zeroToBlank($num){
	if(is_nan($num)){
		return "";
	}else if($num==0){
		return "";
	}
	return $num;

}
function defaultDateToBlank($date){
	if($date=='0000-00-00' || $date=='1970-01-01' || $date=='2030-01-01'){
		return "";
	}
	return $date;
}

function blankToZero($num){
	if(is_null($num)){
		return 0;
	}
	if(is_string($num)){
		
		return floatval($num);
	}
	if(is_nan($num)){
		return 0;
	}
	return $num;

}


function moneyFormatIndia($num) {
    $explrestunits = "" ;
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}

function IND_money_format($money){

	$decimal = (string)($money - floor($money));
	$money = floor($money);
	$length = strlen($money);
	$m = '';
	$money = strrev($money);
	for($i=0;$i<$length;$i++){
		if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
			$m .=',';
		}
		$m .=$money[$i];
	}
	$result = strrev($m);
	$decimal = preg_replace("/0\./i", ".", $decimal);
	$decimal = substr($decimal, 0, 3);
	if( $decimal != '0'){
	$result = $result.$decimal;
	}
	return $result;


}
	
	/*
	$num = 1234567890.123;

	$num = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
	*/

?>