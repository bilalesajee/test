<?php 
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['cid'];
$type	=	$_GET['type'];
if($type==1)
{	
	//commented by Jafer Balti
	//checking if there is a shipment with this country
	$scur	=	$AdminDAO->getrows("shipment","exchangerate,shipmentcurrency","fkcountryid='$id' ORDER BY shipmentdate DESC LIMIT 0,1");
	if(sizeof($scur)>0)
	{
		$rate	=	$scur[0]['exchangerate'];
		$curid	=	$scur[0]['shipmentcurrency'];
		$curids	=	$AdminDAO->getrows("currency","pkcurrencyid,currencysymbol"," pkcurrencyid = '$curid'");
		$symbol	=	$curids[0]['currencysymbol'];
		$cid	=	$curids[0]['pkcurrencyid'];
		echo $rate."_".$symbol."_".$cid;
	}
	else
	{
		//selecting currency
		$currencyarray	= 	$AdminDAO->getrows("currency","*");
		$currencysel	=	"<select name=\"currencydd\" id=\"currencydd\" style=\"width:150px;\" onchange=\"loadrate(this.value,2)\"><option value=\"\" >Select Currency</option>";
		for($i=0;$i<sizeof($currencyarray);$i++)
		{
			$currencyname	=	$currencyarray[$i]['currencyname'];
			$currencyid		=	$currencyarray[$i]['pkcurrencyid'];
			$select			=	"";
			if($currencyid == $selected_currency)
			{
				$select = "selected=\"selected\"";
			}
			$currencysel2	.=	"<option value=\"$currencyid\" $select>$currencyname</option>";
		}
		$currency			=	$currencysel.$currencysel2."</select>";
		// end currency selection
		echo $currency;
	}
}
else
{
		$curids	=	$AdminDAO->getrows("currency","pkcurrencyid,currencysymbol,rate"," pkcurrencyid = '$id'");
		$rate	=	$curids[0]['rate'];
		$symbol	=	$curids[0]['currencysymbol'];
		$cid	=	$curids[0]['pkcurrencyid'];
		echo $rate."_".$symbol."_".$cid;
}
?>