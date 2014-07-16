<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$id			=	$_REQUEST['currencyid'];
$name		=	filter($_REQUEST['name']);
$symbol		=	$_REQUEST['symbol'];
$rate		=	$_REQUEST['rate'];
if(sizeof($_POST)>0)
{
	if($name=='')
	{
		$error="<li>Currency name must be provided.</li>";
	}
	if($symbol=='')
	{
		$error.="<li>Currency symbol must be provided e.g($ for US Dollar).</li>";
	}
	
	if($rate=='')
	{
		$error.="<li>Currency rate must be provided.</li>";
	}
	if($error!='')
	{
		echo $error;
		exit;
	}
		
	$field		=	array('currencyname','currencysymbol','rate');
	$value		=	array($name,$symbol,$rate);
	//for add new
	if($id=="-1")
	{
		$unique 	= 	$AdminDAO->isunique('currency', 'pkcurrencyid', $id, 'currencyname', $name);
		if($unique=='1')
		{
				echo "<li>Currency with this name <b><u>$name</u></b> already exists. Please choose another name.</li>";
				exit;
		}
	
		$AdminDAO->insertrow('currency',$field,$value);
	}
	else //for edit
	{
		$currency = $AdminDAO->getrows('currency','currencyname',"`currencyname`='$name' AND currencydeleted <> '1'");		
		if(count($currency)>1)
		{	
			echo "<li>Currency with this name <b><u>$name</u></b> already exists. Please choose another name.</li>";
			exit;
		}
		$AdminDAO->updaterow('currency',$field,$value,"`pkcurrencyid`='$id'");
	}
}//else
//echo $err;
?>