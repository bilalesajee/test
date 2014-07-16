<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$currencyid	=	$_GET['currency'];
$qs			=	$_GET['qs'];
$ids		=	$_GET['ids'];
$ids		=	trim($ids,',');
$idsarr		=	explode(",",$ids);
/*$liststatus	=	$_SESSION['liststatus'];
if($liststatus)
{
	$status		=	" AND fkstatusid='$liststatus'";
}*/
$query			=	"SELECT 
				pkshiplistid,
				barcode,
				itemdescription,
				quantity,
				CONCAT(currencysymbol,lastpurchaseprice) as lastpurchaseprice,
				weight,
				code3,
				(SELECT GROUP_CONCAT(companyname) FROM supplier,shiplistsupplier sl WHERE sl.fksupplierid=pksupplierid AND  fkshiplistid=pkshiplistid) as companyname,
				storename,
				DATE_FORMAT(deadline,'%d-%m-%y') as deadline,
				CONCAT(firstname,' ',lastname) as name,
				statusname
			FROM
				shiplist LEFT JOIN currency ON (fkcurrencyid=pkcurrencyid) LEFT JOIN countries ON(shiplist.fkcountryid=pkcountryid) LEFT JOIN store on (fkstoreid=pkstoreid) LEFT JOIN addressbook ON (shiplist.fkaddressbookid=pkaddressbookid), statuses
			WHERE
				fkstatusid	=	pkstatusid $status
			";
if($ids)
{
	$query		.=	" AND pkshiplistid IN ($ids) $status $sort GROUP BY
				pkshiplistid";
	$shipdata	=	$AdminDAO->queryresult($query);
}
else
{
	$query		.=	" $status $sort";
	/* start search */
	$searchString	=	trim(stripslashes(filter($_REQUEST['searchString'])));
	$searchField 	=	$_REQUEST['searchField'];
	$search 		=	$_REQUEST['_search'];
	$searchOper	 	=	$_REQUEST['searchOper'];
	$searchoperator	=	$_REQUEST['searchOper'];
	if(strpos($query,$_GET['field'])!=false )
	{
		$sort_index		=	$_GET['field'];
		$sort_order		=	$_GET['order'];
	}
	$condition		=	"";
	if($search!='false')
	{
		switch($searchOper)
		{
			case 'bw': 
			{
				$searchOper	=	" LIKE '$searchString". "%' ";
				break;
			}
			case 'eq': 
			{
				$searchOper	=	" = '$searchString'";
				break;
			}
			case 'ne': 
			{
				$searchOper	=	" <> '$searchString'";
				break;
			}
			case 'lt': 
			{
				$searchOper	=	" < '$searchString'";
				break;
			}
			case 'le': 
			{
				$searchOper	=	" <= '$searchString'";
				break;
			}
			case 'gt': 
			{
				$searchOper	=	" > '$searchString'";
				break;
			}
			case 'ge': 
			{
				$searchOper	=	" >= '$searchString'";
				break;
			}
			case 'ew': 
			{
				$searchOper	=	"LIKE '%"."$searchString'";
				break;
			}
			case 'cn': 
			{
				
				$searchq		=	strip_tags($searchString);
				$list			=	explode(' ',$searchq);
				
				foreach($list as $val)// preparing the search condition
				{
					$condition.="%$val%";
				}
				$condition	=	str_replace('%%','%',$condition);
				$searchOper	=	" LIKE '$condition' ";
				break;
			}
		}
		if($searchField!='')
		{
			$condition	=	"  $searchField $searchOper ";
			
		}
	}
	if($condition!='')
	{
		$query	.= " HAVING ".$condition;
	}
	
	//echo $query;
	
	if($sort_index!='' && $sort_order!='')
	{
		$sort		=	" ORDER BY $sort_index $sort_order ";
		$sort_qs	=	"&field=$sort_index&order=$sort_order";
	}
	else
	{
		
		if($sortorder!='' )
		{
			$sort	=	"ORDER BY ".$sortorder; // takes field name and field order e.g. brandname DESC
		}
		else
		{
			//$sort=" ORDER BY 1 DESC";
		}
	}
	/* end search */
	$query;
	$shipdata	=	$AdminDAO->queryresult($query);
}
//echo $query;
// currencies
$currencies		=	$AdminDAO->getrows("currency","pkcurrencyid,currencysymbol","currencydeleted<>1");
$currencysel	=	"<select name=\"currency\" id=\"currency\" style=\"width:100px;\" onchange=\"selcurrency(this.value)\"><option value=\"\">Select Currency</option>";
for($j=0;$j<sizeof($currencies);$j++)
{
	$currencysymbol	=	$currencies[$j]['currencysymbol'];
	$pkcurrencyid	=	$currencies[$j]['pkcurrencyid'];
	$select			=	"";
	if($pkcurrencyid == $currencyid)
	{
		$select 	= "selected=\"selected\"";
	}
	$currencysel2	.=	"<option value=\"$pkcurrencyid\" $select>$currencysymbol</option>";
}
$currencies			=	$currencysel.$currencysel2."</select>";
// end currencies
$qstring			=	$_SERVER['QUERY_STRING'];
$qstring			=	trim($qstring,"currency=");
?>
<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js"></script>
<script src="../includes/js/common.js"></script>
<body style="background-color:#FFF;">

<div style="padding:0px;font-size:17px;" align="center">
<img src="../images/esajeelogo.jpg" width="150" height="50">
<br />
<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
<b>Think globally shop locally</b>
</span>
</div>
<div style="font-family:Verdana, Geneva, sans-serif; font-size:14px;" align="center">
<b>Wish List</b><br /><?php echo date("d:m:Y h:i:s a");?>
</div>
<div id="currencybox">
<?php echo $currencies;?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript: void(0)" title="Print This page" onclick="printpage()"><span class="printrecord">&nbsp;</span></a>
<a href="javascript: void(0)" title="Print This page" onclick="emailnow()"><span class="email">&nbsp;</span></a>
</div>
<div id="maildata">
<table width="900" align="left" style=" margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;">
  <tr>
    <th width="30">Sr. #</th>
    <th width="102">Barcode</th>
    <th width="127">Item</th>
    <th width="45">Weight</th> 
    <th width="45">Price</th>
    <th width="45">Quantity</th>
    <th width="60">Deadline</th>
  	<th width="82">Country</th>
    <th width="82">Supplier</th>
  <th width="82">Requested By</th>
  <th width="82">Added By</th>
  <th width="66">Status</th>
  
 
  </tr>
  <?php 
  for($i=0;$i<sizeof($shipdata);$i++)
  {
	  if($i%2==0)
	  {
	  	$color	=	"#F8F8F8";
	  }
	  else
	  {
		$color	=	"#ECECFF";
	  }
  ?>
  <tr bgcolor="<?php echo $color; ?>">
    <td><?php echo $i+1;?></td>
    <td><?php echo $shipdata[$i]['barcode'];?></td>
    <td><?php echo $shipdata[$i]['itemdescription'];?></td>
   
     <td><?php echo $shipdata[$i]['weight'];?></td>
    <td><?php echo $shipdata[$i]['currencysymbol'].$shipdata[$i]['lastpurchaseprice'];?></td>
    <td><?php echo $shipdata[$i]['quantity'];?></td>
    
   
    <td><?php echo $shipdata[$i]['deadline'];?></td>
    <td><?php echo $shipdata[$i]['code3'];?></td>
     <td><?php echo $shipdata[$i]['companyname'];?></td>
    
    <td><?php echo $shipdata[$i]['storename'];?></td>
    <td><?php echo $shipdata[$i]['name'];?></td>
     <td><?php echo $shipdata[$i]['statusname'];?></td>
    

  </tr>
  <?php
  	$price		=	$shipdata[$i]['lastpurchaseprice']*$shipdata[$i]['quantity'];
	$rate		=	$shipdata[$i]['rate'];
	$priceinrs	=	$price*$rate;
	//$lastprice=$lastprice+$shipdata[$i]['lastpurchaseprice'];
	$totalprice	+=	$priceinrs;
	if($currencyid)
	{
		$currency	=	$AdminDAO->getrows("currency","currencysymbol,rate","pkcurrencyid='$currencyid'");
		$rate		=	$currency[0]['rate'];
		$symbol		=	$currency[0]['currencysymbol'];
		$pricetotal	=	$totalprice/$rate;
	}
	else
	{
		//based on Rs.
		//base on Default Currency
		$pricetotal	=	$totalprice;
	}
	$weight	+=	($shipdata[$i]['quantity'])*($shipdata[$i]['weight']);
  }
  $weightkg=$weight/1000;
  ?>
  <tr>
    <td colspan="12"><hr/></td>
  </tr>
  <tr>
    <td colspan="2"><strong>Total Items: <?php echo $i;?></strong></td>
    <td colspan="2"><strong>Total Weight: <?php echo $weightkg."Kg ($weight grams)";?></strong></td>
    <td colspan="8">Total Worth: <?php echo $symbol." ".number_format($pricetotal,2);?></td>
  </tr>
</table>
</div>
<div id="currencybox2" style="clear:both;">
<?php echo $currencies;?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript: void(0)" title="Print This page" onclick="printpage()"><span class="printrecord">&nbsp;</span></a>
<a href="javascript: void(0)" title="Print This page" onclick="emailnow()"><span class="email">&nbsp;</span></a>
</div>

<div id="emaildiv" style="display:none">
	<form name="emalfrm" id="emailfrm" method="post" action="">
   	  <table width="738">
    	  <tr>
    	    <td height="22" colspan="2"><strong>Email This Report </strong>
            <div style="float:right">
            <span class="buttons">
                <button type="button" class="positive" onclick="sendemail(-1);">
                    <img src="../images/email_go.png" alt=""/> 
                   Send
                </button>
                 <a href="javascript:void(0);" onclick="hideclass('emaildiv');" class="negative">
                    <img src="../images/cross.png" alt=""/>
                    Cancel
                </a>
            </span>
            </div>
            </td>
  	    </tr>
    	  <tr>
    	    <td width="127">From</td>
    	    <td width="599" valign="middle"><br />
            <?php
				$sql="select 
							pkaddressbookid,CONCAT(a.firstname,' ',a.lastname) as name,
							 a.email
							from 
								addressbook a
							where pkaddressbookid='$empid'
							
							";
			$addarray	=	$AdminDAO->queryresult($sql);
			//print_r($addarray);
			$ename				=	$addarray[0]['name'];
			$email				=	$addarray[0]['email'];
			?>
            <input name="fromemails" type="text" id="fromemails"  size="58" readonly="readonly" value="<?php print "$ename <$email>";?>"/></td>
  	    </tr>
    	  <tr>
    	    <td>To</td>
    	    <td valign="middle">
            <select name="tolist" id="tolist" onchange="toemail('tolist')" style="width:375px;">
  	      		<?php
				$sql="select 
							pkaddressbookid,CONCAT(a.firstname,' ',a.lastname) as name,
							 a.email
							from 
								addressbook a
							";
					$addarray	=	$AdminDAO->queryresult($sql);
				for($a=0;$a<count($addarray);$a++)
				{
					$pkaddressbookid	=	$addarray[$a]['pkaddressbookid'];
					$ename				=	$addarray[$a]['name'];
					$email				=	$addarray[$a]['email'];
				?>
                <option value="<?php echo $email;?>"><?php echo $ename;?></option>
            	<?php
				}
				?>
            </select><br />
   	        <textarea name="toemails" id="toemails" cols="65" rows="1"></textarea></td>
  	    </tr>
    	<tr>
    	    <td>Cc</td>
    	    <td><textarea name="ccemails" id="ccemails" cols="65" rows="1"></textarea></td>
  	    </tr>
    	<tr>
    	    <td>Bcc</td>
    	    <td><textarea name="bccemails" id="bccemails" cols="65" rows="1"></textarea></td>
  	    </tr>
        <tr>
    	    <td>Subject</td>
    	    <td><input type="text" name="subject" id="subject" size="58" ></td>
        </tr>
    	<tr>
    	    <td>Message</td>
    	    <td><textarea name="message" id="message" cols="65" rows="8"></textarea></td>
        </tr>
    	  <tr>
    	    <td colspan="2">
            	         <div style="float:left">
            <span class="buttons">
                <button type="button" class="positive" onclick="sendemail(-1);">
                    <img src="../images/email_go.png" alt=""/> 
                   Send
                </button>
                 <a href="javascript:void(0);" onclick="hideclass('emaildiv');" class="negative">
                    <img src="../images/cross.png" alt=""/>
                    Cancel
                </a>
            </span>
            </div>
            </td>
   	    </tr>
  	  </table>
	<input type="hidden" value="" name="mailtext" id="mailtext" />
    </form>
</div>
</body>
</html>
<script language="javascript">
	function printpage()
	{
		document.getElementById('currencybox').style.display='none';
		document.getElementById('currencybox2').style.display='none';
		window.print();
		//window.close();
	}
	function emailnow()
	{
		document.getElementById('emaildiv').style.display='block';
		document.getElementById('tolist').focus();
	}
	function hideclass(divid)
	{
		document.getElementById(divid).style.display='none';
	}
	function fromemail(id)
	{
		var email=document.getElementById(id).value;
		var obj=document.getElementById(id);
		 var name = obj.options[obj.selectedIndex].text;
    	//document.getElementById("h_myselect").value = theText;
		//alert(theText);
		if(email!='')
		{
			var preemails	=	document.getElementById('fromemails').value;
			if(preemails!='')
			{
				preemails=preemails+',';	
			}
			document.getElementById('fromemails').value=preemails+'"'+name+'"<'+email+'>';
			document.getElementById('fromemails').focus();
		}
		else
		{
			alert("No Email address is associated with this person.");	
			document.getElementById('fromemails').focus();
		}
	}
	function toemail(id)
	{
		var email=document.getElementById(id).value;
		var obj=document.getElementById(id);
		 var name = obj.options[obj.selectedIndex].text;
    	//document.getElementById("h_myselect").value = theText;
		//alert(theText);
		if(email!='')
		{
			var preemails	=	document.getElementById('toemails').value;
			if(preemails!='')
			{
				preemails=preemails+',';	
			}
			document.getElementById('toemails').value=preemails+'"'+name+'"<'+email+'>';
			document.getElementById('toemails').focus();
		}
		else
		{
			alert("No Email address is associated with this person.");	
			document.getElementById('toemails').focus();
		}
	}
	function sendemail()
	{
		var fromemails=document.getElementById('fromemails').value;
		var toemails=document.getElementById('toemails').value;
		if(fromemails=='')
		{
			alert("Please provide email addrtess in From email.");
			 fromemails=document.getElementById('fromemails').focus();
			 return false;
		}
		if(toemails=='')
		{
			alert("Please provide at least One email address in To email.");
			 fromemails=document.getElementById('toemails').focus();
			 return false;
		}
		var maildata=document.getElementById('maildata').innerHTML;
		//alert(maildata);
		document.getElementById('mailtext').value=maildata;
		//mail();
		sendmail();
	}
	function selcurrency(cid)
	{
		window.location	=	'printshiplist.php?currency='+cid+'&<?php echo $qstring;?>';
	}
	function sendmail()
	{
		loading('System is Saving The Data....');
		options	=	{	
						url : 'sendmail.php',
						type: 'POST',
						success: response
					}
		jQuery('#emailfrm').ajaxSubmit(options);
	}
	function response(text)
	{
		if(text=='')
		{
			adminnotice('Mail has been sent.',0,5000);
			//jQuery('#maindiv').load('managebrands.php?'+'<?php echo $qs?>');
			//hidediv('emaildiv');
			hideclass('emaildiv');
			
		}
		else
		{
			adminnotice(text,0,5000);	
		}
		//hideform();
	}

</script>
