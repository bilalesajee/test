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
		$discounts		=	$this->billDAO->getrows("$dbname_detail.sale","*","pksaleid = '$saleid'");
		$globaldiscount	=	$discounts[0]['globaldiscount'];
		//$adjustment		=	$discounts[0]['adjustment'];
		//adjustment has changed 
		$adjustquery	=	"SELECT ((SELECT ((IF(sum(tendered)IS NULL,0,sum(tendered)))-(IF(sum(amount)IS NULL,0,sum(amount)))) as amt FROM $dbname_detail.cashpayment ,$dbname_detail.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)
							+
							(SELECT ((IF(sum(tendered)IS NULL,0,sum(tendered)))-(IF(sum(amount)IS NULL,0,sum(amount)))) as amt FROM $dbname_detail.ccpayment ,$dbname_detail.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)
							+
							(SELECT ((IF(sum(tendered*rate)IS NULL,0,sum(tendered*rate)))-(IF(sum(amount*rate)IS NULL,0,sum(amount*rate)))) as amt FROM $dbname_detail.fcpayment ,$dbname_detail.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)
							+
							(SELECT (IF(sum(tendered)IS NULL,0,sum(tendered)))-(IF(sum(amount)IS NULL,0,sum(amount))) as amt FROM $dbname_detail.chequepayment ,$dbname_detail.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)) AS adjustment
							";
		$adjustments	=	$this->billDAO->queryresult($adjustquery);
		$adjustment		=	$adjustments[0]['adjustment'];
		return $globaldiscount."_".$adjustment;
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
		$saledetails		=	$this->billDAO->getrows("$dbname_detail.saledetail",'pksaledetailid,fkstockid,saleprice, sum( quantity ) AS quantity, sum( saleprice * quantity ) AS subtotal,boxsize '," fksaleid='$saleid' AND quantity>0 GROUP BY `fkstockid`,`saleprice`");
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
			$sql=" SELECT itemdescription as PRODUCTNAME
			FROM 
				$dbname_detail.stock s,barcode b
			WHERE 
				s.fkbarcodeid=b.pkbarcodeid
				AND s.pkstockid='$stockid' 
			";
		$products	=	$this->billDAO->queryresult($sql);
		$product	=	$products[0]['PRODUCTNAME'];
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
	function totalbills($id)
	{
		global $dbname_detail;
		$bills	=	$this->billDAO->getrows("$dbname_detail.bill","*","fksaleid = '$id'");
		return sizeof($bills);
	}
	function countername($id)
	{
		global $dbname_detail;
		$counters	=	$this->billDAO->getrows("$dbname_detail.sale","countername","pksaleid='$id'");
		return $counters[0]['countername'];
	}
	function billdetails($id)
	{
		global $dbname_detail;
		$cash	=	$this->billDAO->getrows("$dbname_detail.cashpayment","SUM(amount) as cash","fksaleid = '$id'");
		$cc		=	$this->billDAO->getrows("$dbname_detail.ccpayment","SUM(amount) as cc","fksaleid = '$id'");
		$fc		=	$this->billDAO->getrows("$dbname_detail.fcpayment","SUM(amount) as fc,rate","fksaleid = '$id' GROUP BY amount");
		$ch		=	$this->billDAO->getrows("$dbname_detail.chequepayment","SUM(amount) as ch","fksaleid = '$id'");
		$cashtotal	=	$cash[0]['cash'];
		$cctotal	=	$cc[0]['cc'];
		for($i = 0; $i<sizeof($fc);$i++)
		{
			$fctotal	+=	($fc[$i]['fc'])*($fc[$i]['rate']);
		}
		$chtotal	=	$ch[0]['ch'];
		return $cashtotal."_".$cctotal."_".$fctotal."_".$chtotal;
	}
	function billamount($id)
	{
		global $dbname_detail;
		$cash	=	$this->billDAO->getrows("$dbname_detail.cashpayment","SUM(tendered) as cash","fksaleid = '$id'");
		$cc		=	$this->billDAO->getrows("$dbname_detail.ccpayment","SUM(tendered) as cc","fksaleid = '$id'");
		$fc		=	$this->billDAO->getrows("$dbname_detail.fcpayment","SUM(tendered) as fc,rate","fksaleid = '$id' GROUP BY amount");
		$ch		=	$this->billDAO->getrows("$dbname_detail.chequepayment","SUM(tendered) as ch","fksaleid = '$id'");
		$cashtotal	=	$cash[0]['cash'];
		$cctotal	=	$cc[0]['cc'];
		for($i = 0; $i<sizeof($fc);$i++)
		{
			$fctotal	+=	($fc[$i]['fc'])*($fc[$i]['rate']);
		}
		$chtotal	=	$ch[0]['ch'];
		return $cashtotal."_".$cctotal."_".$fctotal."_".$chtotal;
	}
	function ccdetails($id)
	{
		global $dbname_detail;
		$ccinfo	=	$this->billDAO->getrows("$dbname_detail.ccpayment","amount,ccno","fksaleid='$id'");
		return $ccinfo;
	}
	
}
?>