function checkNumberValueMandatory(txtVal, dispMsg) {


    var t_amount = (document.getElementById(txtVal).value);

    if (t_amount == "") {
        document.getElementById(dispMsg).innerHTML = "Field Mandatory";
        t_amount = "";
        document.getElementById(txtVal).value = t_amount;
        document.getElementById(txtVal).focus();
    } else if (!isNaN(t_amount)) {
        document.getElementById(dispMsg).innerHTML = "Number";
        t_amount = (new Number(t_amount)).toFixed(2);
        document.getElementById(txtVal).value = t_amount;
        document.getElementById(dispMsg).innerHTML = "";
    } else {
        document.getElementById(dispMsg).innerHTML = "Should be Number";
        t_amount = "";
        document.getElementById(txtVal).value = t_amount;
        document.getElementById(txtVal).focus();
    }
    return t_amount;

}

function checkNumberValueNonMandatory(txtVal, dispMsg) {

    var t_amount = (document.getElementById(txtVal).value);

    if (t_amount == "") {
        // This Condition is Blank as "" is NaN 
        // Appropriate message are taken care of in above called Function

    } else if (!isNaN(t_amount)) {
        document.getElementById(dispMsg).innerHTML = "Number";
        t_amount = (new Number(t_amount)).toFixed(2);
        document.getElementById(txtVal).value = t_amount;
        document.getElementById(dispMsg).innerHTML = "";
    } else {
        document.getElementById(dispMsg).innerHTML = "Should be Number";
        t_amount = "";
        document.getElementById(txtVal).value = t_amount;
        document.getElementById(txtVal).focus();
    }
    return t_amount;

}


function billAmountOnChange() {

    var bill_amt = checkNumberValueMandatory("bill_amount", "bill_msg");

    if (bill_amt == "") {
        // This Condition is Blank as "" is Less Then 0 
        // Appropriate message are taken care of in above called Function
    } else if (bill_amt <= 0) {
        document.getElementById("bill_msg").innerHTML = "Should Be Greater Then 0";
        bill_amt = ""
        document.getElementById('bill_amount').value = bill_amt;
        document.getElementById("bill_amount").focus();
    }
    billAmountCalculate();

}



function rdAmountOnChange() {


    var rd_amt = checkNumberValueNonMandatory("rate_difference", "rd_msg");


    rdCalculate();


    billAmountCalculate();


    /*
        if (rd_amt == "") {
            // This Condition is Blank as "" is Less Then 0 
            // Appropriate message are taken care of in above called Function
        } else if (rd_amt <= 0) {
            document.getElementById("rd_msg").innerHTML = "Should Be Greater Then 0";
            rd_amt = ""
            document.getElementById('rate_difference').value = rd_amt;
            document.getElementById("rate_difference").focus();
        }
    	*/
    // billAmountCalculate();

}


function meterAmountOnChange() {

    var mtr_amt = checkNumberValueNonMandatory("meter", "mtr_msg");

    rdCalculate();

    billAmountCalculate();
    /*
        if (mtr_amt == "") {
            // This Condition is Blank as "" is Less Then 0 
            // Appropriate message are taken care of in above called Function
        } else if (mtr_amt <= 0) {
            document.getElementById("mtr_msg").innerHTML = "Should Be Greater Then 0";
            mtr_amt = ""
            document.getElementById('meter').value = mtr_amt;
            document.getElementById("meter").focus();
        }
    	*/
    //billAmountCalculate();

}



function gstAmountOnChange() {

    var gst_amt = checkNumberValueMandatory("gst", "gst_msg");

    if (gst_amt == "") {
        // This Condition is Blank as "" is Less Then 0 
        // Appropriate message are taken care of in above called Function
    } else if (gst_amt <= 0) {
        document.getElementById("gst_msg").innerHTML = "Should Be Greater Then 0";
        gst_amt = ""
        document.getElementById('gst').value = gst_amt;
        document.getElementById("gst").focus();
    }
    billAmountCalculate();

}

function discountAmountOnChange() {

    var dis_amt = checkNumberValueMandatory("discount", "dis_msg");

    if (dis_amt == "") {
        // This Condition is Blank as "" is Less Then 0 
        // Appropriate message are taken care of in above called Function
    } else if (dis_amt <= 0) {
        document.getElementById("dis_msg").innerHTML = "Should Be Greater Then 0";
        dis_amt = ""
        document.getElementById('discount').value = dis_amt;
        document.getElementById("discount").focus();
    }

    discountCalc();

    billAmountCalculate();

}










function processBlanktoZero(amtVal) {

    if (amtVal == "" || isNaN(amtVal)) {
        amtVal = 0;
    } else {
        amtVal = (new Number(amtVal));
    }
    return amtVal;
}



function discountCalc() {

    // rate_diff_amount_calc
    // bill_amt_before_gst_calc
    // discount
    // discount_amount_calc


    bill_amt_b_g = processBlanktoZero(document.getElementById("bill_amt_before_gst_calc").value);
    rd_amt_calc = processBlanktoZero(document.getElementById("rate_diff_amount_calc").value);
    amt_before_dis = (bill_amt_b_g - rd_amt_calc);
    dis_per = processBlanktoZero(document.getElementById("discount").value);

    dis_amt = ((amt_before_dis * dis_per) / 100);
    document.getElementById("discount_amount_calc").value = (dis_amt).toFixed(2);




}

function gstCalc() {
    gst_per = processBlanktoZero(document.getElementById("gst").value);
    net_amt = processBlanktoZero(document.getElementById("bill_amount").value);
    gst_amount = net_amt - (((net_amt) / (100 + gst_per)) * 100);
    document.getElementById("gst_amount_calc").value = (gst_amount).toFixed(2);
    document.getElementById("bill_amt_before_gst_calc").value = (net_amt - gst_amount).toFixed(2);


}

function paymentAmountCalculate() {
    // Formula ((Bill Amount (Before GST) - Rate Difference Amount) - Discount on Balance ) + GST Amount
    // payment_amount_calc
    //discount_amount_calc
    //rate_diff_amount_calc
    bill_amt_b_gst = processBlanktoZero(document.getElementById("bill_amt_before_gst_calc").value);
    rate_diff_amt = processBlanktoZero(document.getElementById("rate_diff_amount_calc").value);
    dis_amt = processBlanktoZero(document.getElementById("discount_amount_calc").value);
    gst_amt = processBlanktoZero(document.getElementById("gst_amount_calc").value);
    payment_amt = (((bill_amt_b_gst - rate_diff_amt) - dis_amt) + gst_amt);

    document.getElementById("payment_amount_calc").value = (payment_amt).toFixed(2);
}

function rdCalculate() {
    // rate_difference
    // meter

    rd = processBlanktoZero(document.getElementById("rate_difference").value);
    mtr = processBlanktoZero(document.getElementById("meter").value);

    rdAmount_calc = rd * mtr;
    document.getElementById("rate_diff_amount_calc").value = (rdAmount_calc).toFixed(2);

}

function calcBalCalulate() {
    // bill_amt_before_gst_calc
    // rate_diff_amount_calc
    // discount_amount_calc
    // bal_amt_1
    // bal_amt_2

    bill_amt_before_g = processBlanktoZero(document.getElementById("bill_amt_before_gst_calc").value);
    rd_amt = processBlanktoZero(document.getElementById("rate_diff_amount_calc").value);
    dis_amt = processBlanktoZero(document.getElementById("discount_amount_calc").value);

    balOne = (bill_amt_before_g - rd_amt);
    document.getElementById("bal_amt_1").value = (balOne).toFixed(2);

    balTwo = (balOne - dis_amt);
    document.getElementById("bal_amt_2").value = (balTwo).toFixed(2);

}

function billAmountCalculate() {
    //discountCalc();
    //netAmountCalculate();
    gstCalc();

    discountCalc();
    calcBalCalulate()

    paymentAmountCalculate();



}