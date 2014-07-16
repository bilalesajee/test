<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$shipmentid	=	$_GET['id'];
$param		=	$_GET['param'];
$oid		=	$_GET['oid'];
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency` = 1");
$defaultcurrency = $currency[0]['currencyname'];
// fetching edit section data
if($shipmentid!="-1")
{
	// selecting records for editing purposes
	$shipmentrow				= 	$AdminDAO->getrows("shipment s,currency c","s.shipmentname,s.totalvalue,s.fkcityid,s.fkdeststoreid,s.fkclientid,s.type,s.fkcountryid,s.fkdestcountryid,s.fkdestcityid,s.shipmentnotes,s.sdate,s.cdate,s.exchangerate,c.rate,c.currencysymbol,s.fkagentid,s.fkstatusid,s.weight"," pkshipmentid	=	'$shipmentid' AND pkcurrencyid=shipmentcurrency");
	$selected_srccity			=	$shipmentrow[0]['fkcityid'];
	$selected_store				=	$shipmentrow[0]['fkdeststoreid'];
	$selected_client			=	$shipmentrow[0]['fkclientid'];
	$selected_type				=	$shipmentrow[0]['type'];
	$selected_country			=	$shipmentrow[0]['fkcountryid'];
	$selected_destcountry		=	$shipmentrow[0]['fkdestcountryid'];
	$selected_destcity			=	$shipmentrow[0]['fkdestcityid'];
	$shipmentnotes				=	$shipmentrow[0]['shipmentnotes'];
	$sdate						=	implode("-",array_reverse(explode("-",$shipmentrow[0]['sdate'])));
	$cdate						=	implode("-",array_reverse(explode("-",$shipmentrow[0]['cdate'])));
	$selected_exchangerate		=	$shipmentrow[0]['exchangerate'];
	//$selected_rate				=	$shipmentrow[0]['rate'];
	$currency_symbol			=	$shipmentrow[0]['currencysymbol'];
	$weight						=	$shipmentrow[0]['weight'];
	if(!$weight)
	{
		$query					=	"SELECT 
										weight,quantity
									FROM
										`order`
									WHERE
										fkshipmentid='$shipmentid'
									";
		$shiplistdata			=	$AdminDAO->queryresult($query);
		if($shiplistdata)
		{
			foreach($shiplistdata as $sldata)
			{
				$sweight			=	$sldata['weight'];
				$qty				=	$sldata['quantity'];
				$weight				+=	$sweight*$qty;
			}
			$weightkg				=	$weight/1000;
		}
	}
	$totalvalue						=	$shipmentrow[0]['totalvalue'];
	//echo $totalvalue."is the total value<br>";
	if(!$totalvalue)
	{
		//echo "i am in total value when value is not available";
		$query					=	"SELECT 
										lastpurchaseprice,
										rate,
										sd.quantity
									FROM 
										shiplist s, currency,shiplistdetails sd
									WHERE 
										pkshiplistid=sd.fkshiplistid AND 
										s.fkshipmentid='$shipmentid' AND
										fkcurrencyid=pkcurrencyid
									";
		$shiplistdata			=	$AdminDAO->queryresult($query);
		if($shiplistdata)
		{
			foreach($shiplistdata as $sldata)
			{
				$sprice				=	$sldata['lastpurchaseprice'];
				$qty				=	$sldata['quantity'];
				$rate				=	$sldata['rate'];
				$price				=	$sprice*$qty;
				$totalvalue			+=	($price*$rate);
			}
			$totalvalue				=	$totalvalue/$selected_exchangerate;
		}
	}
	$shipmentname				=	$shipmentrow[0]['shipmentname'];
	//tracking info
	$shiptrackinglocal			=	$AdminDAO->getrows("shipmenttrackinglocal","*","fkshipmentid='$shipmentid'");
	$selected_vehicle			=	$shiptrackinglocal[0]['fkvehicleid'];
	$selected_employee1			=	$shiptrackinglocal[0]['fkemployeeid1'];
	$selected_employee2			=	$shiptrackinglocal[0]['fkemployeeid2'];
	$shiptracking				=	$AdminDAO->getrows("shipmenttracking","*","fkshipmentid='$shipmentid'");
//	print_r();
	$trackingnumber				=	$shiptracking[0]['trackingnumber'];
	$flightnumber				=	$shiptracking[0]['flightnumber'];
	$url						=	$shiptracking[0]['url'];
	//end tracking info
	$selected_agent				=	$shipmentrow[0]['fkagentid'];
	$selected_currency			=	$shipmentrow[0]['shipmentcurrency'];
	$selected_status			=	$shipmentrow[0]['fkstatusid'];
}// end edit section 
else
{
	$selected_status			=	1;
}
// selecting cities
$srccities			=	$AdminDAO->getrows("city","*","fkcountryid='$id'");
$citysel			=	"<select name=\"srccity\" id=\"srccity\"><option value=\"\">Select City</option>";
for($i=0;$i<sizeof($srccities);$i++)
{
	$cityname		=	$srccities[$i]['cityname'];
	$cityid			=	$srccities[$i]['pkcityid'];
	$citysel2		.=	"<option value=\"$cityid\">$cityname</option>";
}
$cities				=	$citysel.$citysel2."</select>";
// end select cities
// source countries
$srccountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1");
$countrysel			=	"<select name=\"srccountries\" id=\"srccountries\" style=\"width:144px;\" onchange=\"getcity(this.value,'')\"><option value=\"\">Select Country</option>";
for($i=0;$i<sizeof($srccountries);$i++)
{
	$countryname	=	$srccountries[$i]['countryname'];
	$countryid		=	$srccountries[$i]['pkcountryid'];
	$select		=	"";
	if($countryid == $selected_country)
	{
		$select = "selected=\"selected\"";
	}
	$countrysel2	.=	"<option value=\"$countryid\" $select>$countryname</option>";
}
$countries			=	$countrysel.$countrysel2."</select>";
// end source countries
// destination countries
$destcountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1");
$destcountrysel			=	"<select name=\"destcountries\" id=\"destcountries\" style=\"width:144px;\" onchange=\"getcity2(this.value,'')\"><option value=\"\">Select Country</option>";
for($i=0;$i<sizeof($srccountries);$i++)
{
	$countryname		=	$destcountries[$i]['countryname'];
	$countryid			=	$destcountries[$i]['pkcountryid'];
	$select		=	"";
	if($countryid == $selected_destcountry)
	{
		$select = "selected=\"selected\"";
	}
	$destcountrysel2	.=	"<option value=\"$countryid\" $select>$countryname</option>";
}
$destcountries			=	$destcountrysel.$destcountrysel2."</select>";
// end countries destination
// selecting agents
$agentarray		= 	$AdminDAO->getrows("supplieroragent","*", "supplieroragentdeleted<>1 AND isagent='a'");
$agentsel		=	"<select name=\"agents\" id=\"agents\" style=\"width:144px;\" ><option value=\"\">Select Agent</option>";
for($i=0;$i<sizeof($agentarray);$i++)
{
	$agentname	=	$agentarray[$i]['suppliername'];
	$agentid	=	$agentarray[$i]['pksupplierid'];
	$select		=	"";
	if($agentid == $selected_agent)
	{
		$select = "selected=\"selected\"";
	}
	$agentsel2	.=	"<option value=\"$agentid\" $select>$agentname</option>";
}
$agents			=	$agentsel.$agentsel2."</select>";
// end agents
/*
//selecting currency
$currencyarray	= 	$AdminDAO->getrows("currency","*");
$currencysel	=	"<select name=\"currency\" id=\"currency\" style=\"width:144px;\" onchange=\"loadrate(this.value)\"><option value=\"\" >Select Currency</option>";
for($i=0;$i<sizeof($currencyarray);$i++)
{
	$currencyname	=	$currencyarray[$i]['currencyname'];
	$currencyid		=	$currencyarray[$i]['pkcurrencyid'];
	$select		=	"";
	if($currencyid == $selected_currency)
	{
		$select = "selected=\"selected\"";
	}
	$currencysel2	.=	"<option value=\"$currencyid\" $select>$currencyname</option>";
}
$currency			=	$currencysel.$currencysel2."</select>";
// end currency selection
*/
// selecting shipment charges
$chargesarray		= 	$AdminDAO->getrows("charges","*","chargesdeleted<>1");
$chargesize			=	sizeof($chargesarray);
foreach($chargesarray as $chargeid)
{
	$charge[]	=	$chargeid['pkchargesid'];
}
// end shipment charges
// selecting first employee 
$employeearray		= 	$AdminDAO->getrows("employee,addressbook","*","employeedeleted<>1 AND fkaddressbookid=pkaddressbookid");
$empsel		=	"<select name=\"driver\" id=\"driver\" style=\"width:144px;\" ><option value=\"\" >Select Driver</option>";
for($i=0;$i<sizeof($employeearray);$i++)
{
	$empname		=	$employeearray[$i]['firstname']." ".$employeearray[$i]['lastname'];
	$empid			=	$employeearray[$i]['pkemployeeid'];
	$select		=	"";
	if($empid == $selected_employee1)
	{
		$select = "selected=\"selected\"";
	}
	$empsel2	.=	"<option value=\"$empid\" $select>$empname</option>";
}
$emp1			=	$empsel.$empsel2."</select>";
// end employee
// selecting second employee
$employeearray2		= 	$AdminDAO->getrows("employee,addressbook","*","employeedeleted<>1 AND fkaddressbookid=pkaddressbookid");
$empsel3		=	"<select name=\"supervisor\" id=\"supervisor\" style=\"width:144px;\" ><option value=\"\" >Select Supervisor</option>";
for($i=0;$i<sizeof($employeearray2);$i++)
{
	$empname2		=	$employeearray2[$i]['firstname']." ".$employeearray2[$i]['lastname'];
	$empid2			=	$employeearray2[$i]['pkemployeeid'];
	$select		=	"";
	if($empid2 == $selected_employee2)
	{
		$select = "selected=\"selected\"";
	}
	$empsel4	.=	"<option value=\"$empid2\" $select>$empname2</option>";
}
$emp2			=	$empsel3.$empsel4."</select>";
//end employee
//selecting vehicles
$vehiclesarray	= 	$AdminDAO->getrows("vehicle","*");
$vehiclesel	=	"<select name=\"vehicle\" id=\"vehicle\" style=\"width:144px;\" ><option value=\"\" >Select Vehicle</option>";
for($i=0;$i<sizeof($vehiclesarray);$i++)
{
	$vehiclename	=	$vehiclesarray[$i]['vehiclenumber'];
	$vehicleid		=	$vehiclesarray[$i]['pkvehicleid'];
	$select		=	"";
	if($vehicleid == $selected_vehicle)
	{
		$select = "selected=\"selected\"";
	}
	$vehiclesel2	.=	"<option value=\"$vehicleid\" $select>$vehiclename</option>";
}
$vehicleids			=	$vehiclesel.$vehiclesel2."</select>";
// end vehicles selection

// statuses
//$statuses		=	$AdminDAO->getrows("shipstatuses","pkstatusid,statusname","1");
$statuses		=	$AdminDAO->getrows("statuses","pkstatusid,statusname","1");

$statussel		=	"<select name=\"status\" id=\"status\" style=\"width:144px;\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($statuses);$i++)
{
	$statusname	=	$statuses[$i]['statusname'];
	$statusid	=	$statuses[$i]['pkstatusid'];
	$select		=	"";
	if($statusid == $selected_status)
	{
		$select = "selected=\"selected\"";
	}
	$statussel2	.=	"<option value=\"$statusid\" $select>$statusname</option>";
}
$statuses			=	$statussel.$statussel2."</select>";
// end statuses

?>
<script language="javascript" type="text/javascript">
jQuery(function($){
	$("#sdate").mask("99-99-9999");
	$("#cdate").mask("99-99-9999");
	if(document.getElementById('srccountries').value!="")
	{
		getcity(document.getElementById('srccountries').value,'<?php echo $selected_srccity;?>')
	}
	if(document.getElementById('destcountries').value!="")
	{
		getcity2(document.getElementById('destcountries').value,'<?php echo $selected_destcity;?>')
	}
	if(document.getElementById('desttype').value==1)
	{
		jQuery('#dest').load('loadstore.php?type=1&id=<?php echo $selected_store;?>');
	}
	else
	{
		jQuery('#dest').load('loadstore.php?type=2&id=<?php echo $selected_client;?>');
	}
});
function addshipment()
{
	options	=	{	
					url : 'insertshipmentfrm.php?param=<?php echo $param;?>',
					type: 'POST',
					success: shipres
				}
	jQuery('#shipmentfrm').ajaxSubmit(options);
}
function shipres(text)
{
	//alert(text);
	if(text=='')
	{
		adminnotice('Shipment data has been saved.',0,3000);
		document.getElementById('subsection').innerHTML='';		
		jQuery('#maindiv').load('manageshipment.php?qs='+'<?php echo $qs;?>');
		
	}
	else if(text!='' && !isNaN(text))
	{
		adminnotice('Shipment data has been saved. Now please Click on Move to move the selected orders to it.',0,4000);
		jQuery('#subsection').load('ordermove.php?id=<?php echo $oid;?>&shipid='+text);
	}
	else
	{
		adminnotice(text,0,3000);
	}
}
function loadrate(id,type)
{
	if(id)
	{
		options	=	{	
						url : 'getcur.php?type='+type+'&cid='+id,
						type: 'POST',
						success: fcrate
					}
		jQuery('#shipmentfrm').ajaxSubmit(options);
	}
}
function fcrate(text1)
{
	var totalcharges=0;
	newtext	=	text1.split("_");
	text	=	newtext[0];
	cur		=	newtext[1];
	curid	=	newtext[2];
	if(cur)
	{
		document.getElementById('exchangerate').value	=	text;
		document.getElementById('cursymbol').innerHTML	=	cur;
		document.getElementById('currency').value		=	curid;
	}
	else
	{
		document.getElementById('exchangerate').value		=	'';		
		document.getElementById('currenciesdiv').innerHTML	=	text1;
	}
}
function calcharges(id)
{
	var cid	=	id.split('_');
	chargesid	=	cid[0];
	var totalcharges  = 0,totalcharges2=0;
	if(chargesid == 'c')
	{
		charge1	=	(document.getElementById(id).value)*(document.getElementById('exchangerate').value);
		document.getElementById('c2_'+cid[1]).value	=	charge1;
		for(i=1;i<='<?php echo sizeof($chargesarray);?>';i++)
		{
			charge	=	document.getElementById('c_'+i).value;
			charge2	=	document.getElementById('c2_'+i).value;
			totalcharges	=	(totalcharges*1)+(charge*1);
			totalcharges2	=	(totalcharges2*1)+(charge2*1);
		}
		document.getElementById('totalcharges').innerHTML	= totalcharges;
		document.getElementById('totalchargesrs').innerHTML	= totalcharges2;
		document.getElementById('tchargesrs').value	= totalcharges2;		
	}
	else
	{
		charge2	=	(document.getElementById(id).value)/(document.getElementById('exchangerate').value);
		document.getElementById('c_'+cid[1]).value	=	charge2;
		for(i=1;i<='<?php echo sizeof($chargesarray);?>';i++)
		{
			charge	=	document.getElementById('c_'+i).value;
			charge2	=	document.getElementById('c2_'+i).value;
			totalcharges	=	(totalcharges*1)+(charge*1);
			totalcharges2	=	(totalcharges2*1)+(charge2*1);
		}
		document.getElementById('totalcharges').innerHTML	= totalcharges;
		document.getElementById('totalchargesrs').innerHTML	= totalcharges2;
		document.getElementById('tchargesrs').value	= totalcharges2;		
	}
}
function storeclient()
{
	if(document.getElementById('desttype').value == 1)
	{
		if(document.getElementById('newstoreclient').style.display	==	'none')
		{
			document.getElementById('newstoreclient').style.display	=	'block';
			jQuery('#newstoreclient').load('newstoreclient.php?type=1');
		}
		else
		{
			document.getElementById('newstoreclient').style.display='none';
		}
	}
	else
	{
		if(document.getElementById('newstoreclient').style.display	==	'none')
		{
			document.getElementById('newstoreclient').style.display	=	'block';
			jQuery('#newstoreclient').load('newstoreclient.php?type=2');
		}
		else
		{
			document.getElementById('newstoreclient').style.display='none';
		}
	}
}
function getcity(id,sid)
{
	$('#srccities').load('loadcities.php?cid=srccities&id='+id+'&sid='+sid);
	loadrate(id,1);
}
function getcity2(id,sid)
{
	$('#destcities').load('loadcities.php?cid=destcities&id='+id+'&sid='+sid);
}
function getcity3(id,sid)
{
	$('#clientcities').load('loadcities.php?cid=clientcities&id='+id+'&sid='+sid);
}
function addshipmentform()
{
		options	=	{	
						url : 'insertshipmentfrm.php',
						type: 'POST',
						success: addshipmentresponse
					}
		jQuery('#shipmentfrm').ajaxSubmit(options);
}
function addshipmentresponse(text)
{
	if(text=='')
	{
		adminnotice('Shipment data has been saved.',0,5000);
		jQuery('#maindiv').load('manageshipment.php?qs='+'<?php echo $qs;?>');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function hideclientstoredata()
{
	document.getElementById('newstoreclient').style.display='none';
	if(document.getElementById('desttype').value == 1)
	{
		jQuery('#dest').load('loadstore.php?type=1&id=<?php echo $selected_store;?>');
	}
	else
	{
		jQuery('#dest').load('loadstore.php?type=2&id=<?php echo $selected_client;?>');
	}
}
function addnewsrccities()
{
	if(document.getElementById('newsrccitiesdiv').style.display	==	'none')
		document.getElementById('newsrccitiesdiv').style.display	=	'block';
	else
		document.getElementById('newsrccitiesdiv').style.display	=	'none';
}
function addnewdestcities()
{
	if(document.getElementById('newdestcitiesdiv').style.display	==	'none')
		document.getElementById('newdestcitiesdiv').style.display	=	'block';
	else
		document.getElementById('newdestcitiesdiv').style.display	=	'none';
}
function addnewveh()
{
	if(document.getElementById('newveh').style.display	==	'none')
		document.getElementById('newveh').style.display	=	'block';
	else
		document.getElementById('newveh').style.display	=	'none';
}
function addnewagnt()
{
	if(document.getElementById('newagnt').style.display	==	'none')
		document.getElementById('newagnt').style.display	=	'block';
	else
		document.getElementById('newagnt').style.display	=	'none';
}
function addstore()
{
	options	=	{	
					url : 'insertnewstore.php',
					type: 'POST',
					success: storeresponse
				}
	jQuery('#newstorefrm').ajaxSubmit(options);
}
function addclient(text)
{
	options	=	{	
					url : 'insertclient.php',
					type: 'POST',
					success: storeresponse
				}
	jQuery('#newclientfrm').ajaxSubmit(options);
}
function storeresponse(text)
{
	if(text.indexOf('<li>')!='')
	{
		if(document.getElementById('desttype').value==1)
		{
			jQuery('#dest').load('loadstore.php?type=1&id='+text);
			document.getElementById('newstoreclient').style.display	=	'none';
		}
		else
		{
			jQuery('#dest').load('loadstore.php?type=2&id='+text);
			document.getElementById('newstoreclient').style.display	=	'none';
		}
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="shipdiv">
  <form  name="shipmentfrm" id="shipmentfrm" style="width:920px;" onSubmit="addshipment(); return false;" class="form" >
    <fieldset>
      <legend>
      <?php if($shipmentid=='-1'){echo 'Add New';}else{echo 'Update';}?>
      Shipment</legend>
      <div  style="float:right;"><span class="buttons">
        <button type="button" class="positive" onclick="addshipment();"> <img src="../images/tick.png" alt=""/>
        <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a></span></div>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td><?php if($shipmentid!='-1'){echo 'Shipment Name:';}?><br /></td>
                <td colspan="3" align="left"><strong><?php echo $shipmentname;?></strong></td>
            </tr>
<!--            <tr>
                <td>Status:</td>
                <td colspan="3" align="left"><?php// echo $statuses;?></td>
            </tr>-->
            <tr>
                <td>Expected Date:</td>
              <td width="30%" align="left"><input name="sdate" id="sdate" type="text" size="20" value="<?php echo $sdate;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
                <td width="22%">Confirmed Date:</td>
                <td width="34%"><input name="cdate" id="cdate" type="text" value="<?php echo $cdate;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
            </tr>
            <tr>
                <td>Source Country: <span class="redstar" title="This field is compulsory">*</span></td>
                <td align="left"><?php echo $countries;?></td>
                <td>Source City:  <span class="redstar" title="This field is compulsory">*</span></td>
                <td><span id="srccities">
  </span>&nbsp;<button type="button" class="positive" onclick="addnewsrccities();" title="Add More" alt="Add More"><img src="../images/add.png" alt=""/></button><div id="newsrccitiesdiv" style="display:none;"><input type="text" name="newsrccities" id="newsrccities" />&nbsp;<input type="text" name="newsrccitiescode" id="newsrccitiescode" /></div></td>
            </tr>
            <tr>
                <td>Currency Rate:  <span class="redstar" title="This field is compulsory">*</span></td>
                <td colspan="3"><span id="currenciesdiv"></span><span id="cursymbol"></span><input name="exchangerate" id="exchangerate" type="text" value="<?php echo $selected_exchangerate;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" onkeypress="return isNumberKey(event)" /></td>
            </tr>            
            <tr>
                <td>Destination Country:  <span class="redstar" title="This field is compulsory">*</span></td>
                <td><?php echo $destcountries; ?></td>
                <td>Destination City:  <span class="redstar" title="This field is compulsory">*</span></td>
                <td><span id="destcities">
              </span>&nbsp;<button type="button" class="positive" onclick="addnewdestcities();" title="Add More" alt="Add More"><img src="../images/add.png" alt=""/></button><div id="newdestcitiesdiv" style="display:none;"><input type="text" name="newdestcities" id="newdestcities" />&nbsp;<input type="text" name="newdestcitiescode" id="newdestcitiescode" /></div></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td><select name="desttype" id="desttype" style="width:144px;" onchange="hideclientstoredata();" >
                <option value="1" <?php if($selected_type==1){echo "selected=\"selected\"";}?>>Store</option>
                <option value="2" <?php if($selected_type==2){echo "selected=\"selected\"";}?>>Client</option>
              </select></td>
                <td>Store/Client:  <span class="redstar" title="This field is compulsory">*</span></td>
                <td><span id="dest"><?php echo $store; ?></span>&nbsp;<button type="button" class="positive" onclick="storeclient();" title="Add More" alt="Add More"><img src="../images/add.png" alt=""/></button></td>
            </tr>
                <span id="newstoreclient" style="display:none;position:absolute;"></span>
            <tr>
                <td>Tracking #:</td>
                <td><input type="text" name="trackingnumber" id="trackingnumber" value="<?php echo $trackingnumber;?>" /></td>
                <td>URL:</td>
                <td><input type="text" name="url" id="url" value="<?php echo $url;?>" />&nbsp;<a href="<?php echo $url;?>" target="_blank"><?php echo $url;?></a></td>
            </tr>
            <tr>
                <td>Flight #:</td>
                <td><input type="text" name="flight" id="flight" value="<?php echo $flightnumber;?>" /></td>
                <td>Driver:</td>
                <td><?php echo $emp1;?></td>
            </tr>
            <tr>
                <td>Supervisor:</td>
                <td><?php echo $emp2;?></td>
                <td>Vehicle #:</td>
                <td><?php echo $vehicleids;?>&nbsp;<button type="button" class="positive" onclick="addnewveh();" title="Add More" alt="Add More"><img src="../images/add.png" alt=""/></button><span id="newveh" style="display:none;"><input type="text" name="newvehicle" id="newvehicle" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></span></td>
            </tr>
            <tr>
                <td>Agent:</td>
                <td><?php echo $agents;?>&nbsp;<button type="button" class="positive" onclick="addnewagnt();" title="Add More" alt="Add More"><img src="../images/add.png" alt=""/></button><span id="newagnt" style="display:none;"><input type="text" name="newagent" id="newagent" /></span></td>
                <td>Value:</td>
                <td><input type="text" name="totalvalue" id="totalvalue" value="<?php echo round($totalvalue,2);?>" /></td>
            </tr>                
                
                
                
                
                
            </tr>
<!--            <tr>
                <td><?php// if($shipmentid!='-1'){echo 'Weight:';}?></td>
                <td><?php// if($shipmentid!='-1'){echo $weightkg."Kg ($weight grams)";}?></td>
                <td>Value:</td>
                <td><!--<?php// //echo $currency_symbol; ?><input type="text" name="totalvalue" id="totalvalue" value="<?php// echo round($totalvalue,2);?>" /></td>
            </tr>
          <tr >
            <td>&nbsp;</td>
            <td><div id="currsymbol"><?php// echo $currency_array[0]['currencysymbol'];?></div></td>
            <td colspan="2"><?php// echo $defaultcurrency;?></td>
          </tr>-->
          	<?php
			
	 		$i=1;
	 		 foreach($chargesarray as $charges)
	  		{
	  	$shipmentcharges			= 	$AdminDAO->getrows("shipmentcharges","*"," fkshipmentid='$shipmentid' AND 	fkchargesid='".$charges['pkchargesid']."'");
	  ?>
          <tr >
            <td width="14%"><?php //echo $charges['chargesname'];?>
              <input type="hidden" name="chargesid[]" value="<?php echo $charges['pkchargesid'];?>" /></td>
            <td><input name="charges_<?php echo $charges['pkchargesid'];?>" id="c_<?php echo $i;?>" type="hidden" value="<?php echo $shipmentcharges[0]['totalcharges'];?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" onkeypress="return isNumberKey(event);" onblur="calcharges(this.id)" />
              <div id="error3" class="error" style="display:none; float:right;"></div></td>
            <td colspan="2"><input name="chargesinrs[]" id="c2_<?php echo $i; ?>" type="hidden" value="<?php echo $shipmentcharges[0]['chargesinrs'];?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" onkeypress="return isNumberKey(event);" onblur="calcharges(this.id)" /></td>
          </tr>
          <?php
	  $totalcharges		+=	$shipmentcharges[0]['totalcharges'];
	  $chargesinrs		+=	$shipmentcharges[0]['chargesinrs'];	  
	  $i++;
	  }
	  		
	 	 	?>
<!--          <tr >
            <td>Total Charges:</td>
            <td><span id="currsymbol2"><?php// echo $currency_array[0]['currencysymbol']?></span><span id="totalcharges"><?php// echo $totalcharges; ?></span></td>
            <td colspan="2"><?php// echo $defaultcurrency;?><span id="totalchargesrs"><?php// echo $chargesinrs;?></span><input type="hidden" id="tchargesrs" name="tchargesrs" value="<?php// echo $chargesinrs;?>" /></td>
          </tr>-->
          <tr>
          	<td>Notes:</td>
            <td colspan="3"><textarea name="shipmentnotes" id="shipmentnotes"><?php echo stripslashes($shipmentnotes); ?></textarea><input type="hidden" name="currency" id="currency" value="" /></td>
          </tr>
          <tr >
            <td colspan="4"  align="left"><input name="shipmentid" type="hidden" value="<?php echo $shipmentid;?>" />
              <div class="buttons">
                <button type="button" class="positive" onclick="addshipment();"> <img src="../images/tick.png" alt=""/>
                <?php if($shipmentid=='-1'){echo 'Save';}else{echo 'Update';}?>
                </button>
                <a href="javascript:void(0);" onclick="hidediv('shipdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
          </tr>
      </table>
    </fieldset>
  </form>
</div>
<br />
<script language="javascript">
	focusfield('sdate');
</script>