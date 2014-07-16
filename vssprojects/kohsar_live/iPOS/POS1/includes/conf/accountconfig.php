<?php
	// cash sale
	$cashacc				=	1;
	$cashsaleacc			=	3;
	
	// credit card sale
	$ccmachineaccountacc	=	4;
	$ccsaleacc				=	5;
	
	// foreign currency sale
	$cashforeigncurrentacc	=	6;
	$fcsaleacc				=	7;
	
	// cheque sale
	$bankacc				=	2;
	$csaleacc				=	8;
	
	// Credit Card Payment Charges	 
	$ccmachinechargesacc	=	9;
	$machinebankacc			=	10;
	
	// Cheque Payment Charges ***************not used*************
	$bankchargesacc			=	11;
	$machinebankacc			=	10;
	
	// Foreign Currency Payment Charges ****** confirm from Hamid?? ********
	$fcchargesacc			= 	16;
	$cashforeigncurrentacc	=	6;
	
	// Credit Sale	
	$customeracc			=	0000;
	$csaleacc				=	8;
	
	// Cash Payout
	$payeeacc				=	0000;
	$cashacc				=	1;
	
	// Cheque Payout
	$payeeacc				=	0000;
	$machinebankacc			=	10;
	
	// Cash Collection
	$cashacc				=	1;
	$customeracc			=	0000;
	
	// Credit Card Collection
	$ccmachineaccountacc	=	4;
	$customeracc			=	0000;
	
	// Cheque Collection
	$machinebankacc			=	10;
	$customeracc			=	0000;
	
	// Foreign Currency Collection
	$cashforeigncurrentacc	=	6;
	$customeracc			=	0000;
	
	// Discount on cash sale *********** Confirm from Hamid... Discount trated as overall
	$discountallowedacc		=	12;
	$cashacc				=	1;
	
	// Discount on credit card sale
	$discountallowedacc		=	12;
	$customeracc			=	0000;
	
	// Discout received from supplier ************** not done yet***********
	$supplieracc			=	0000;
	$discountreceived		=	13;
	
	////*************************
	
	// Cash sale return
	$salereturnacc			=	14;
	$cashacc				=	1;
	
	// Credit Card sale Return *** not used *******
	$salereturnacc			=	14;
	$machinebankacc			=	10;
	
	// Cheque sale Return
	$salereturnacc			=	14;
	$bankacc				=	2;
		
	// Foreign Currency sale Return *** not used *********
	$salereturnacc			=	14;
	$cashforeigncurrentacc	=	6;
	
	// Credit Card sale Charges Return
	/*
	Charges return if
	before settlement of Credit card machine  when a customer says that he want to return credit card sale then we scratch credit card and use void option at this time then 						    there will be no charges on credit card return but when settlement of Credit card machine completed. Bank will charge credit card transaction charge there will be no 	    refund. we can return credit card sale amount through bank/chq/cash after deduction of credit card machine charges.
    So no need of entry.
	*/
	
	// Cheque Charges Return
	/*
	 Bank will not return any charges when you have used any bank service.
     there is no need of this entry but after deduction of charges we can pay to customer.
	*/
	
	// Foreign Currency sale Charges Return
	
	/*
	 we cannot return any charges when a bank deduct.
     no need of entry.
	*/
	
	// Closing Cash Difference (Extra)
	$cashacc				=	1;
	$diffincashacc			=	15;
	
	// Closing Cash Difference (short)
	$diffincashacc			=	15;
	$cashacc				=	1;

	// Cash returned if FC payment
	/* no need to record */ 
	
	
?>