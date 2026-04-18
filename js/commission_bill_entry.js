

function processBlanktoZero(amtVal){

	if(amtVal=="" || isNaN(amtVal)){
		amtVal=0;
	} else {
		amtVal=(new Number(amtVal));
	}
	return amtVal;
}


function checkNumberValueMandatory(txtVal,dispMsg){
	

	var t_amount=(document.getElementById(txtVal).value);

		if(t_amount==""){
			document.getElementById(dispMsg).innerHTML="Field Mandatory";
			t_amount="";
			document.getElementById(txtVal).value=t_amount;
			document.getElementById(txtVal).focus();
		} else if(!isNaN(t_amount)){
			document.getElementById(dispMsg).innerHTML="Number";
			t_amount=(new Number(t_amount)).toFixed(2);
			document.getElementById(txtVal).value=t_amount;
			document.getElementById(dispMsg).innerHTML="";

		}else {
			document.getElementById(dispMsg).innerHTML="Should be Number";
			t_amount="";
			document.getElementById(txtVal).value=t_amount;
			document.getElementById(txtVal).focus();
		}
	return t_amount;	

}

function checkNumberValueNonMandatory(txtVal,dispMsg){

	var t_amount=(document.getElementById(txtVal).value);
	
		if(t_amount==""){
			// This Condition is Blank as "" is NaN 
			// Appropriate message are taken care of in above called Function
		
		} else if(!isNaN(t_amount)){
			document.getElementById(dispMsg).innerHTML="Number";
			t_amount=(new Number(t_amount)).toFixed(2);
			document.getElementById(txtVal).value=t_amount;
			document.getElementById(dispMsg).innerHTML="";
		}else {
			document.getElementById(dispMsg).innerHTML="Should be Number";
			t_amount="";
			document.getElementById(txtVal).value=t_amount;
			document.getElementById(txtVal).focus();
		}
	return t_amount;	

}

// New //
function totalBillAmountOnChange(){

	var grs_amt=checkNumberValueMandatory("total_bill_amount","total_bill_amt_msg");

		if(grs_amt==""){
			// This Condition is Blank as "" is Less Then 0 
			// Appropriate message are taken care of in above called Function
		} else 	if(grs_amt<=0){
			document.getElementById("total_bill_amt_msg").innerHTML="Should Be Greater Then 0";
			grs_amt=""
			document.getElementById('total_bill_amount').value=grs_amt;
			document.getElementById("total_bill_amount").focus();
		}
	//commissionAmountCalculate();

	gstPercentOnChange();
	//calculateNetBillAmount();
	
}



// New //
function totalGRAmountOnChange(){

	var grs_amt=checkNumberValueMandatory("total_gr_amount","total_gr_msg");

		if(grs_amt==""){
			// This Condition is Blank as "" is Less Then 0 
			// Appropriate message are taken care of in above called Function
		} else 	if(grs_amt<0){
			document.getElementById("total_gr_msg").innerHTML="Should Be Greater Then or equal to 0";
			grs_amt=""
			document.getElementById('total_gr_amount').value=grs_amt;
			document.getElementById("total_gr_amount").focus();
		}
		//commissionAmountCalculate();
		
		gstPercentOnChange();
		//calculateNetBillAmount();
	
}


// New //
function totalDiscountAmountOnChange(){

	var grs_amt=checkNumberValueMandatory("total_discount_amount","dis_msg");

		if(grs_amt==""){
			// This Condition is Blank as "" is Less Then 0 
			// Appropriate message are taken care of in above called Function
		} else 	if(grs_amt<0){
			document.getElementById("dis_msg").innerHTML="Should Be Greater Then or equal to 0";
			grs_amt=""
			document.getElementById('total_discount_amount').value=grs_amt;
			document.getElementById("total_discount_amount").focus();
		}
		//commissionAmountCalculate();

		gstPercentOnChange();
		//calculateNetBillAmount();
	
}

// New //
function totalPaymentAmountOnChange(){

	var grs_amt=checkNumberValueMandatory("total_payment_amount","tot_pay_msg");

		if(grs_amt==""){
			// This Condition is Blank as "" is Less Then 0 
			// Appropriate message are taken care of in above called Function
		} else 	if(grs_amt<0){
			document.getElementById("tot_pay_msg").innerHTML="Should Be Greater Then or equal to 0";
			grs_amt=""
			document.getElementById('total_payment_amount').value=grs_amt;
			document.getElementById("total_payment_amount").focus();
		}
		//commissionAmountCalculate();

		gstPercentOnChange();
		//calculateNetBillAmount();

	
}



// New //
function calculateNetBillAmount(){
	

	tot_bill_amt=document.getElementById('total_bill_amount').value;
	tot_GR_amt=document.getElementById('total_gr_amount').value;
	tot_dis_amt=document.getElementById('total_discount_amount').value;

	net_bill_amt=tot_bill_amt-tot_GR_amt-tot_dis_amt;

	document.getElementById('net_bill_amt').value=net_bill_amt;

	//gstPercentOnChange();
	//calculateNetAmountLessGST();


}


// New //
function calculateNetAmountLessGST(){

	net_bill_amt=document.getElementById('net_bill_amt').value;
	gst_bill=document.getElementById('gst_amount_bill').value;
	bill_amount_less_gst=net_bill_amt-gst_bill;
	document.getElementById("bill_amount_less_gst").disabled=false;
	document.getElementById('bill_amount_less_gst').value=bill_amount_less_gst;
	document.getElementById("bill_amount_less_gst").disabled=true;

	total_payment_amount=document.getElementById('total_payment_amount').value;
	gst_amount_payment=document.getElementById('gst_amount_payment').value;
	total_payment_amount_less_gst=total_payment_amount-gst_amount_payment;
	document.getElementById("total_payment_amount_less_gst").disabled=false;
	document.getElementById('total_payment_amount_less_gst').value=total_payment_amount_less_gst;
	document.getElementById("total_payment_amount_less_gst").disabled=true;




}

// New //
function gstPercentOnChange(){

	var gst_per=checkNumberValueMandatory("gst_percent","gst_msg");

	calculateNetBillAmount();	

	if(gst_per>=100){
		alert (gst_per);
		document.getElementById("gst_amount_payment").disabled=false;
		document.getElementById("gst_amount_payment").value="";
		document.getElementById("gst_amount_payment").disabled=true;

		document.getElementById("gst_amount_bill").disabled=false;
		document.getElementById("gst_amount_bill").value="";
		document.getElementById("gst_amount_bill").disabled=true;

		document.getElementById("gst_msg").innerHTML="Should be less then 100";
		document.getElementById("gst_percent").value="";
		document.getElementById("gst_percent").focus();
	} else if(gst_per>0){
		var gst_amount_bill=(((document.getElementById("net_bill_amt").value*gst_per)/(Number(gst_per)+100))).toFixed(2);

		document.getElementById("gst_amount_bill").disabled=false;
		document.getElementById("gst_amount_bill").value=gst_amount_bill;

		document.getElementById("gst_amount_bill").disabled=true;

		var gst_amount_payment=(((document.getElementById("total_payment_amount").value*gst_per)/(Number(gst_per)+100))).toFixed(2);

		document.getElementById("gst_amount_payment").disabled=false;
		document.getElementById("gst_amount_payment").value=gst_amount_payment;
		document.getElementById("gst_amount_payment").disabled=true;
	} else if(gst_per<0){
		document.getElementById("gst_amount_payment").disabled=false;
		document.getElementById("gst_amount_payment").value="";
		document.getElementById("gst_amount_payment").disabled=true;

		document.getElementById("gst_amount_bill").disabled=false;
		document.getElementById("gst_amount_bill").value="";
		document.getElementById("gst_amount_bill").disabled=true;

		document.getElementById("gst_msg").innerHTML="Should be greater then 0";
		document.getElementById("gst_percent").value="";
		document.getElementById("gst_percent").focus();			
	} else if(gst_per==0){
		document.getElementById("gst_msg").innerHTML="Should be greater then 0";

		document.getElementById("gst_amount_payment").disabled=false;
		document.getElementById("gst_amount_payment").value="";
		document.getElementById("gst_amount_payment").disabled=true;

		document.getElementById("gst_amount_bill").disabled=false;
		document.getElementById("gst_amount_bill").value="";
		document.getElementById("gst_amount_bill").disabled=true;

		document.getElementById("gst_percent").value="";
		document.getElementById("gst_percent").focus();	
	}	

	calculateNetAmountLessGST();
	commissionPercentOnChange();

}


//New //
function commissionPercentOnChange(){

	//calculateNetBillAmount();

	//document.getElementById('log_msg').innerHTML="in CommissionPercentOnChange";


	var commission_percent=checkNumberValueMandatory("commission_percent","comm_msg");

	if(commission_percent>=100){
		//document.getElementById('log_msg').innerHTML="Comm >100";
		document.getElementById("commission_amt_pay").disabled=false;
		document.getElementById("commission_amt_pay").value="";
		document.getElementById("commission_amt_pay").disabled=true;

		document.getElementById("commission_amt_bill").disabled=false;
		document.getElementById("commission_amt_bill").value="";
		document.getElementById("commission_amt_bill").disabled=true;

		document.getElementById("comm_msg").innerHTML="Should be less then 100";
		document.getElementById("commission_percent").value="";
		document.getElementById("commission_percent").focus();
	} else if(commission_percent>0){
		//document.getElementById('log_msg').innerHTML="Comm >0";
		var commission_amt_bill=((document.getElementById("bill_amount_less_gst").value*commission_percent)/100).toFixed(2);

		document.getElementById("commission_amt_bill").disabled=false;
		document.getElementById("commission_amt_bill").value=commission_amt_bill;
		document.getElementById("commission_amt_bill").disabled=true;

		var commission_amt_pay=((document.getElementById("total_payment_amount_less_gst").value*commission_percent)/100).toFixed(2);

		document.getElementById("commission_amt_pay").disabled=false;
		document.getElementById("commission_amt_pay").value=commission_amt_pay;
		document.getElementById("commission_amt_pay").disabled=true;
	} else if(commission_percent<0){
		document.getElementById("commission_amt_pay").disabled=false;
		document.getElementById("commission_amt_pay").value="";
		document.getElementById("commission_amt_pay").disabled=true;

		document.getElementById("commission_amt_bill").disabled=false;
		document.getElementById("commission_amt_bill").value="";
		document.getElementById("commission_amt_bill").disabled=true;

		document.getElementById("comm_msg").innerHTML="Should be greater then 0";
		document.getElementById("commission_percent").value="";
		document.getElementById("commission_percent").focus();			
	} else if(commission_percent==0){
		document.getElementById("comm_msg").innerHTML="Should be greater then 0";

		document.getElementById("commission_amt_pay").disabled=false;
		document.getElementById("commission_amt_pay").value="";
		document.getElementById("commission_amt_pay").disabled=true;

		document.getElementById("commission_amt_bill").disabled=false;
		document.getElementById("commission_amt_bill").value="";
		document.getElementById("commission_amt_bill").disabled=true;

		document.getElementById("commission_percent").value="";
		document.getElementById("commission_percent").focus();	
	}	


}

function commissionModeOnChange(){

}









