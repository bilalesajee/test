<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$param	=	$_GET['param'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	/*********************************** POST DATA *********************************/
	/*******************************************************************************/
	$shipmentid		=	$_POST['shipmentid'];
	$srccountries	=	$_POST['srccountries'];
	$currency		=	$_POST['currency'];
	$sdate			=	$_POST['sdate'];
	$tchargesrs		=	$_POST['tchargesrs'];
/*	if($sdate	== "")
	{
		$msg.="<li>Please enter arrival date.</li>";
	}*/
	if($srccountries == "")
	{
		$msg.="<li>Please select a source country.</li>";
	}
	$srccities		=	$_POST['newsrccities'];
	if($srccities == "")
	{
		$srccities	=	$_POST['srccities'];
		$cityid		=	$_POST['srccities'];
	}
	else
	{
		$scitycode	=	$_POST['newsrccitiescode'];
		if($scitycode == "")
		{
			$msg.="<li>Please enter a source city code.</li>";
		}
		else
		{
			$unique = $AdminDAO->isunique('city','pkcityid',$cityid,'cityname',$srccities);
			if($unique=='1')
			{
				$msg.="<li>City with this name <b><u>$srccities</u></b> already exists. Please choose another name.</li>";	
			}
		}
	}
	if($srccities == "")
	{
		$msg.="<li>Please select a source city.</li>";
	}
	$desttype			=	$_POST['desttype']; // 1 = store and 2 = client
	$fkdeststoreid		=	$_POST['store']; 
	if($desttype==1 && $fkdeststoreid=='')
	{
		$msg.=	"<li>Please select a destination store.";
	}
	$fkstoreid			=	$_SESSION['storeid']; 
	$fkclientid			=	$_POST['client'];
	if($desttype==2 && $fkclientid=='')
	{
		$msg.=	"<li>Please select a client.</li>";
	}
	$destcountries		=	$_POST['destcountries'];
	if($destcountries == "")
	{
		$msg.="<li>Please select a destination country.</li>";
	}
	$destcities		=	$_POST['newdestcities'];
	if($destcities == "")
	{
		$destcities	=	$_POST['destcities'];
		$dcityid	=	$_POST['destcities'];
	}
	else
	{
		$dcitycode	=	$_POST['newdestcitiescode'];
		if($dcitycode == "")
		{
			$msg.="<li>Please enter a destination city code.</li>";
		}
		else
		{
			$unique = $AdminDAO->isunique('city','pkcityid',$dcityid,'cityname',$destcities);
			if($unique=='1')
			{
				$msg.="<li>City with this name <b><u>$destcities</u></b> already exists. Please choose another name.</li>";	
			}
		}
	}
	if($destcities == "")
	{
		$msg.="<li>Please select a destination city.</li>";
	}
	/*if($currency=='')
	{
		$msg.="<li>Please select currency of shipment.</li>";
	}*/
	if($srccities == "")
	{
		$srccities	=	$_POST['srccities'];
		$cityid		=	$_POST['srccities'];
	}
	if($currency == "")
	{
		$msg.="<li>Please select shipment currency.</li>";
	}
	$agents			=	$_POST['newagent'];
	if($agents=="")
	{
		$agents		=	$_POST['agents'];
		$agentid	=	$_POST['agents'];
	}
	else
	{
		$unique = $AdminDAO->isunique('supplieroragent','pksupplierid',$agentid,'suppliername',$agents);
		if($unique=='1')
		{
			$msg.="<li>Agent with this name <b><u>$agents</u></b> already exists. Please choose another name.</li>";	
		}
	}
/*	if($agents == "")
	{
		$msg.="<li>Please select shipment agent.</li>";
	}
*/	$vehicle		=	$_POST['newvehicle'];
	if($vehicle=="")
	{
		$vehicle		=	$_POST['vehicle'];
		$vehicleid		=	$_POST['vehicle'];
	}
	else
	{
		$unique = $AdminDAO->isunique('vehicle','pkvehicleid',$vehicleid,'vehiclenumber',$vehicle);
		if($unique=='1')
		{
			$msg.="<li>Vehicle with this number <b><u>$vehicle</u></b> already exists. Please choose another number.</li>";	
		}
	}
	if($msg!='')
	{
		echo $msg;
		exit;
	}
	// saving new source cities info
	if($_POST['newsrccities']!="")
	{
		$field		=	array('cityname','fkcountryid','code');
		$value		=	array($srccities,$srccountries,$scitycode);
		$srccities	=	$AdminDAO->insertrow('city',$field,$value);
	}
	// saving new destination cities info
	if($_POST['newdestcities']!="")
	{
		$field		=	array('cityname','fkcountryid','code');
		$value		=	array($destcities,$destcountries,$dcitycode);
		$destcities	=	$AdminDAO->insertrow('city',$field,$value);
	}
	// saving new agents
	if($_POST['newagent']!="")
	{
		$field		=	array('suppliername','isagent');
		$value		=	array($agents,'a');
		$agents		=	$AdminDAO->insertrow('supplieroragent',$field,$value);
	}
	// saving new vehicle
	if($_POST['newvehicle']!="")
	{
		$field		=	array('vehiclenumber');
		$value		=	array($vehicle);
		$vehicle	=	$AdminDAO->insertrow('vehicle',$field,$value);
	}
	/*******************************************************************************/
	// start client/store data
	//
	/*if($desttype==1 && $fkdeststoreid=="")
	{
		//insert into store data
		$newstorename	=	$_POST['newstorename'];
		$email			=	$_POST['email'];
		$address		=	$_POST['address'];
		$clientcountries=	$_POST['clientcountries'];
		$newclientcities=	$_POST['newclientcities'];
		if($newclientcities == "")
		{
			$newclientcities	=	$_POST['clientcities'];
		}
		else
		{
			$field		=	array('cityname','fkcountryid');
			$value		=	array($newclientcities,$clientcountries);
			$newclientcities	=	$AdminDAO->insertrow('city',$field,$value);
		}
		$state			=	$_POST['state'];
		$zip			=	$_POST['zip'];
		$phone			=	$_POST['phone'];
		$fax			=	$_POST['fax'];
		$sfields		=	array('storename','storephonenumber','storeaddress','fkcityid','fkstateid','zipcode','fkcountryid','email','fax');
		$sdata			=	array($newstorename,$phone,$address,$newclientcities,$state,$zip,$clientcountries,$email,$fax);
		$fkdeststoreid	=	$AdminDAO->insertrow("store",$sfields,$sdata);
	}
	else if($desttype==2 && $fkclientid=="")
	{
		// insert into client data
		$fname			=	$_POST['fname'];
		$lname			=	$_POST['lname'];
		$company		=	$_POST['company'];
		$email			=	$_POST['email'];
		$mobile			=	$_POST['mobile'];
		$address1		=	$_POST['address1'];
		$address2		=	$_POST['address2'];
		$clientcountries=	$_POST['clientcountries'];
		$newclientcities=	$_POST['newclientcities'];
		if($newclientcities == "")
		{
			$newclientcities	=	$_POST['clientcities'];
		}
		else
		{
			$field		=	array('cityname','fkcountryid');
			$value		=	array($newclientcities,$clientcountries);
			$newclientcities	=	$AdminDAO->insertrow('city',$field,$value);
		}
		$state			=	$_POST['state'];
		$zip			=	$_POST['zip'];
		$phone			=	$_POST['phone'];
		$fax			=	$_POST['fax'];
		$username		=	$_POST['username'];
		$password		=	$_POST['password'];
		$nic			=	$_POST['nic'];
		$afields		=	array('firstname','lastname','email','mobile','phone','address1','address2','fkcityid','fkstateid','zip','fkcountryid','fax','username','password','nic');
		$adata			=	array($fname,$lname,$email,$mobile,$phone,$address1,$address2,$newclientcities,$state,$zip,$clientcountries,$fax,$username,$password,$nic);
		$fkaddressbookid=	$AdminDAO->insertrow("addressbook",$afields,$adata);
		$cfields		=	array('fkaddressbookid','company');
		$cdata			=	array($fkaddressbookid,$company);
		$fkclientid		=	$AdminDAO->insertrow("client",$cfields,$cdata);
	}*/
	//end client/store data
	/*******************************************************************************/
	// generating shipment name
	if($shipmentid==-1)
	{
		$srccitycodes	=	$AdminDAO->getrows("city","code","pkcityid='$srccities'");
		$srccitycode	=	$srccitycodes[0]['code'];
		$destcitycodes	=	$AdminDAO->getrows("city","code","pkcityid='$destcities'");
		$destcitycode	=	$destcitycodes[0]['code'];
		$shipmentname	=	$srccitycode."-".$destcitycode;
		$flag			=	0;
		$i				=	2;
		do{
			$shipmentinfo		=	$AdminDAO->getrows("shipment","1"," shipmentname='$shipmentname'");
			if(sizeof($shipmentinfo)>0)
			{
				$shipmentname	=	$srccitycode."-".$destcitycode."-".$i;
			}
			else
			{
				$flag	=	1;
			}
			$i++;
		}while($flag == 0);
	}
	else
	{
		$existingshipdata	=	$AdminDAO->getrows("shipment","shipmentname","pkshipmentid='$shipmentid'");
		$shipmentname		=	$existingshipdata[0]['shipmentname'];
	}
	$sdate			=	implode("-",array_reverse(explode("-",$sdate)));
	$cdate			=	implode("-",array_reverse(explode("-",$_POST['cdate'])));
	$exchangerate	=	$_POST['exchangerate'];
	if($nagent!='' && $nagent!='Add New')
	{
		$field		=	array('suppliername','isagent');
		$value		=	array($nagent,'a');
		$agent	=	$AdminDAO->insertrow('supplieroragent',$field,$value);	
	}
	else
	{
		$agent			=	filter($_REQUEST['agent']);
	}
	$charges		=	$_REQUEST['chargesid'];
	$fkstatusid		=	1;//$_POST['status'];//1 for open
	$shipmentnotes	=	filter($_POST['shipmentnotes']);
	$shipmentval	=	$_POST['totalvalue'];
	/***********************************************************************************/
	/*********************************** END POST DATA *********************************/
	$shipmentdate	=	time();
	//insert Shipment finally
	$field		=	array('type','shipmentdate','sdate','cdate','shipmentname','fkagentid','fkcountryid','fkcityid','fkdestcountryid','fkdestcityid','shipmentcurrency','exchangerate','totalvalue','fkstatusid','fkstoreid','fkdeststoreid','fkclientid','shipmentnotes','amountinrs');
	$value		=	array($desttype,$shipmentdate,$sdate,$cdate,$shipmentname,$agents,$srccountries,$srccities,$destcountries,$destcities,$currency,$exchangerate,$shipmentval,$fkstatusid,$fkstoreid,$fkdeststoreid,$fkclientid,$shipmentnotes,$tchargesrs);
	if($shipmentid=='-1')
	{
		$shipmentid	=	$AdminDAO->insertrow('shipment',$field,$value);	
	}
	else
	{
		$AdminDAO->updaterow('shipment',$field,$value," pkshipmentid	=	'$shipmentid'");	
	}
	// starting the tracking section
	$trackingnumber	=	$_POST['trackingnumber'];
	$url			=	$_POST['url']; 
	$flight			=	$_POST['flight'];
	// inserting/updating first set of tracking info
	$tfields	=	array('trackingnumber','url','flightnumber','fkshipmentid');
	$tvalues	=	array($trackingnumber,$url,$flight,$shipmentid);
	$AdminDAO->deleterows("shipmenttracking","fkshipmentid='$shipmentid'",1);
	$trackingid	=	$AdminDAO->insertrow("shipmenttracking",$tfields,$tvalues);
	$driver			=	$_POST['driver'];
	$supervisor		=	$_POST['supervisor'];
	// inserting/updating first set of tracking info
	$lfields	=	array('fkvehicleid','fkemployeeid1','fkemployeeid2','fkshipmentid');
	$lvalues	=	array($vehicle,$driver,$supervisor,$shipmentid);
	$AdminDAO->deleterows("shipmenttrackinglocal","fkshipmentid='$shipmentid'",1);
	$localtrackingid	=	$AdminDAO->insertrow("shipmenttrackinglocal",$lfields,$lvalues);
	// end tracking section
	if(count($charges)>0)
	{
		foreach($charges as $chid)
		{
			$chvalue			=	$_REQUEST['charges_'.$chid];
			$field				=	array('fkchargesid','totalcharges','chargescurrency','chargesinrs','fkshipmentid');
			$value				=	array($chid,$chvalue,$currency,$exchangerate*$chvalue,$shipmentid);
			$AdminDAO->deleterows('shipmentcharges',"fkshipmentid	=	'$shipmentid' AND fkchargesid='$chid'",1);
			$shipmentchargesid	=	$AdminDAO->insertrow('shipmentcharges',$field,$value);	
		}
	}
	// if param is sent then it means we are coming from orders move to shipment screen
	if($param=='move')
	{
		echo $shipmentid;
		exit;
	}
}
?>