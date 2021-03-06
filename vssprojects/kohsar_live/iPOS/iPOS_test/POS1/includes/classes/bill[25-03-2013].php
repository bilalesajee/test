<?php
class Bill
{
	var $billDAO	=	"";
	function Bill($dao)
	{
		$this->billDAO	=	$dao;
	}
	function globaldiscount($saleid)
	{
		global $dbname_detail;
		if((strstr($_SERVER['REQUEST_URI'],'/admin/')==true) && ($_SESSION['siteconfig']!=1)){//edit by ahsan 22/02/2012
			$discounts		=	$this->billDAO->getrows("$dbname_detail.sale","*","pksaleid = '$saleid'");
		}else{//run this code if accessed from POS
			$discounts		=	$this->billDAO->getrows("$dbname_detail.sale","globaldiscount","pksaleid = '$saleid'");
		}//end edit
		$globaldiscount	=	$discounts[0]['globaldiscount'];
		// second sale discount
		$sdiscounts		=	$this->billDAO->getrows("$dbname_detail.salediscount","sum(amount) amount","fksaleid='$saleid'");
		$salediscount	=	$sdiscounts[0]['amount'];
		//total discount (sale discount + global discount)
		$totaldiscount	=	$globaldiscount+$salediscount;
		//$adjustment		=	$discounts[0]['adjustment'];
		//adjustment has changed 
		// changed By Yasir -- 07-07-11. Added AND paymenttype <> 'c'
		$adjustquery	=	"SELECT ((SELECT ((IF(sum(tendered)IS NULL,0,sum(tendered)))-(IF(sum(amount)IS NULL,0,sum(amount)))) as amt FROM $dbname_detail.payments ,$dbname_detail.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid AND paymenttype <> 'c')) AS adjustment
							";
		$adjustments	=	$this->billDAO->queryresult($adjustquery);
		$adjustment		=	$adjustments[0]['adjustment'];
		return $totaldiscount."_".$adjustment;
	}
	function getitemstotal($saleid)
	{
		global $dbname_detail;
		$sales			=	$this->billDAO->getrows("$dbname_detail.saledetail","SUM(quantity) as quantity","fksaleid = '$saleid' GROUP BY `fkstockid`,`saleprice`");
		$num	=	0;
		for($s=0;$s<sizeof($sales);$s++)
		{
			if($sales[$s]['quantity']!=0)
			{
				$num++;
			}
		}
		return $num;
	}
	function getqtytotal($saleid)
	{
		global $dbname_detail;
		$sales			=	$this->billDAO->getrows("$dbname_detail.saledetail","SUM(quantity) as quantity","fksaleid = '$saleid'");
		$num	=	0;
		for($s=0;$s<sizeof($sales);$s++)
		{
			if($sales[$s]['quantity']!=0)
			{
				$num+=$sales[$s]['quantity'];
			}
		}
		return $num;
	}
	function getsaletime($saleid)
	{
		global $dbname_detail;
		$times			=	$this->billDAO->getrows("$dbname_detail.sale","FROM_UNIXTIME(datetime,'%d-%m-%y %h:%m:%s') as stime","pksaleid = '$saleid'");
		$saletime		=	$times[0]['stime'];
		return $saletime;
	}
	function getsaledetails($saleid)
	{
		global $dbname_detail;
		//$saledetails		=	$this->billDAO->getrows("saledetail","*","fksaleid = '$saleid'","timestamp","DESC");
		$saledetails		=	$this->billDAO->getrows("$dbname_detail.saledetail",'pksaledetailid,fkstockid,saleprice, sum( quantity ) AS quantity, sum( saleprice * quantity ) AS subtotal,boxsize,fkdiscountid '," fksaleid='$saleid' AND quantity>0 GROUP BY `fkstockid`,`saleprice`,`fkdiscountid`");
		return $saledetails;
	}
	function getsaledetails2($saleid)
	{
		global $dbname_detail;
		//$saledetails		=	$this->billDAO->getrows("saledetail","*","fksaleid = '$saleid'","timestamp","DESC");
		$saledetails		=	$this->billDAO->getrows("$dbname_detail.saledetail",'pksaledetailid,fkstockid,saleprice, sum(quantity) as qty,sum( saleprice * quantity ) AS subtotal,boxsize '," fksaleid='$saleid' AND quantity<0 GROUP BY `fkstockid`,`saleprice`");
		return $saledetails;
	}
	function getsalestore($saleid)
	{
		global $dbname_detail;
		$salestore			=	$this->billDAO->getrows("$dbname_detail.sale s, store st LEFT JOIN countries ON (pkcountryid = st.fkcountryid) LEFT JOIN city ON (pkcityid = st.fkcityid) LEFT JOIN state ON (pkstateid = st.fkstateid)","storename,storephonenumber,storeaddress,cityname,countryname,statename,zipcode,email,fax,billfooter","s.pksaleid = '$saleid' AND s.fkstoreid = st.pkstoreid");
		return $salestore;
	}
	function getsaleproduct($stockid)
	{
		global $dbname_detail;
			$sql=" SELECT itemdescription,shortdescription
			FROM 
				$dbname_detail.stock s,barcode b
			WHERE 
				s.fkbarcodeid=b.pkbarcodeid
				AND s.pkstockid='$stockid' 
			";
		$products	=	$this->billDAO->queryresult($sql);
		$product	=	$products[0]['shortdescription'];
		if($product=='')
		{
			$product	=	$products[0]['itemdescription'];
		}
		return $product;
	}
	function getproductnotes($stockid)
	{
		global $dbname_detail;
		$sql=" SELECT pkbarcodeid
			FROM productattribute pa
			RIGHT JOIN (
			product p, attribute a
			) ON ( pa.fkproductid = p.pkproductid
			AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, stock s,barcode b
			WHERE pkproductid = pa.fkproductid
			AND pkattributeid = pa.fkattributeid
			AND pkproductattributeid = fkproductattributeid
			AND pkattributeid = ao.fkattributeid
			AND pkattributeoptionid = pi.fkattributeoptionid
			AND b.fkproductid = pkproductid
			AND pi.fkbarcodeid = b.pkbarcodeid
			AND s.fkbarcodeid=b.pkbarcodeid
			AND s.pkstockid='$stockid' 
			GROUP BY
			pkbarcodeid
			";
		$barcodes		=	$this->billDAO->queryresult($sql);
		
		$barcode		=	$barcodes[0]['pkbarcodeid'];
		$notes	=	$this->billDAO->getrows('note,itemnote',"*","fkbarcodeid = '$barcode'");
		return $notes;
	}
	function totalbills($id,$status) // $status added by Yasir -- 05-07-11
	{
		global $dbname_detail;
		$bills	=	$this->billDAO->getrows("$dbname_detail.bill","*","fksaleid = '$id' AND status = '$status'");
		return sizeof($bills);
	}
	function countername($id)
	{
		global $dbname_detail;
		$counters	=	$this->billDAO->getrows("$dbname_detail.sale","countername","pksaleid='$id'");
		return $counters[0]['countername'];
	}
	// function added by Yasir -- 06-07-11
	function duplicatebilldetails($id)
	{
		global $dbname_detail;
		$cash	=	$this->billDAO->getrows("$dbname_detail.payments","SUM(amount) as cash","fksaleid = '$id' AND paymenttype <> 'c' AND paymentmethod='c'");
		$cashtotal	=	$cash[0]['cash'];
		$cc		=	$this->billDAO->getrows("$dbname_detail.payments","SUM(amount) as cc","fksaleid = '$id' AND paymenttype <> 'cc' AND paymentmethod='cc'");
		$cctotal	=	$cc[0]['cc'];
		$fc		=	$this->billDAO->getrows("$dbname_detail.payments","SUM(amount) as fc","fksaleid = '$id' AND paymenttype <> 'fc' AND paymentmethod='fc'");
		$fctotal	=	$fc[0]['fc'];
		$ch		=	$this->billDAO->getrows("$dbname_detail.payments","SUM(amount) as ch","fksaleid = '$id' AND paymenttype <> 'c' AND paymentmethod='ch'");		
		$chtotal	=	$ch[0]['ch'];
		return $cashtotal."_".$cctotal."_".$fctotal."_".$chtotal;
	}
	//
	function billdetails($id)
	{
		global $dbname_detail;
		/*$cash	=	$this->billDAO->getrows("$dbname_detail.cashpayment","SUM(amount) as cash","fksaleid = '$id'");
		$cc		=	$this->billDAO->getrows("$dbname_detail.ccpayment","SUM(amount) as cc","fksaleid = '$id'");
		$fc		=	$this->billDAO->getrows("$dbname_detail.fcpayment","SUM(amount) as fc,rate","fksaleid = '$id' GROUP BY amount");
		$ch		=	$this->billDAO->getrows("$dbname_detail.chequepayment","SUM(amount) as ch","fksaleid = '$id'");*/
		$paymentarr		=	$this->billDAO->getrows("$dbname_detail.sale","cash,cc,fc,cheque","pksaleid = '$id'");
		$cashtotal	=	$paymentarr[0]['cash'];
		$cctotal	=	$paymentarr[0]['cc'];
		/*for($i = 0; $i<sizeof($fc);$i++)
		{
			$fctotal	+=	($fc[$i]['fc'])*($fc[$i]['rate']);
		}*/
		$chtotal	=	$paymentarr[0]['cheque'];
		$fctotal	=	$paymentarr[0]['fc'];
		return $cashtotal."_".$cctotal."_".$fctotal."_".$chtotal;
	}
	function billamount($id)
	{
		global $dbname_detail;		
		$paymentarr		=	$this->billDAO->getrows("$dbname_detail.sale","cash,cc,fc,cheque,adjustment","pksaleid = '$id'"); // added adjustment by Yasir -- 08-07-11
		$cashtotal	=	$paymentarr[0]['cash'];
		$cctotal	=	$paymentarr[0]['cc'];
		$chtotal	=	$paymentarr[0]['cheque'];
		$fctotal	=	$paymentarr[0]['fc'];
		$adj		=	$paymentarr[0]['adjustment']; // added by Yasir -- 08-07-11
		return $cashtotal."_".$cctotal."_".$fctotal."_".$chtotal."_".$adj;	// added by Yasir -- 08-07-11 ."_".$adj
	}
	// function added by Yasir -- 07-07-11 
	function duplicatebillamount($id)
	{
		global $dbname_detail;
		// Changed by Yasir -- 06-07-11. Added  AND paymenttype <> 'c'
		$cash	=	$this->billDAO->getrows("$dbname_detail.payments","SUM(tendered) as cash","fksaleid = '$id' AND paymenttype <> 'c' AND paymentmethod='c'");
		$cashtotal	=	$cash[0]['cash'];
		$cc		=	$this->billDAO->getrows("$dbname_detail.payments","SUM(tendered) as cc","fksaleid = '$id' AND paymenttype <> 'c' AND paymentmethod='cc'");		
		$cctotal	=	$cc[0]['cc'];
		$fc		=	$this->billDAO->getrows("$dbname_detail.payments","SUM(tendered) as fc","fksaleid = '$id' AND paymenttype <> 'c' AND paymentmethod='fc'"); // Changed by Yasir 24-06-11. Previously was Group By amount. tendered replaced by tendered*rate
		$fctotal	=	$fc[0]['fc'];
		$ch		=	$this->billDAO->getrows("$dbname_detail.payments","SUM(tendered) as ch","fksaleid = '$id' AND paymenttype <> 'c' AND paymentmethod='ch'");
		$chtotal	=	$ch[0]['ch'];
		/* Commented by Yasir -- 06-07-11
		$cashtotal	=	$cash[0]['cash'];
		$cctotal	=	$cc[0]['cc'];
		for($i = 0; $i<sizeof($fc);$i++)
		{
			$fctotal	+=	($fc[$i]['fc'])*($fc[$i]['rate']);
		}
		$chtotal	=	$ch[0]['ch'];*/		
		return $cashtotal."_".$cctotal."_".$fctotal."_".$chtotal;		
	}
	//
	function ccdetails($id)
	{
		global $dbname_detail;
		$ccinfo	=	$this->billDAO->getrows("$dbname_detail.payments","amount,ccno","fksaleid='$id' AND paymentmethod='cc'");
		return $ccinfo;
	}
	function updatepayment($amount,$method,$saleid)
	{
		global $dbname_detail;;
		$time=time();
		if($method=='c')
		{
			$meth='cash';
		}
		elseif($method=='cc')
		{
			$meth='cc';
			
		}
		elseif($method=='fc')
		{
			$meth='fc';
			
		}
		elseif($method=='ch')
		{
			$meth='cheque';
			
		}
		$sql="Update $dbname_detail.sale set $meth=($meth+($amount)),updatetime='$time' where pksaleid='$saleid'";	
		$this->billDAO->queryresult($sql);
	}

	
}
?>