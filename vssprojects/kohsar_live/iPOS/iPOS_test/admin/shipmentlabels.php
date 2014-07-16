<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js"></script>
<script src="../includes/js/common.js"></script>
<style>
body{
	font-family:Verdana, Geneva, sans-serif;
	background-color:#fff;
	font-size:12px;
}
table {
	border:1px solid #000;
	border-collapse:collapse;
}
table td,th{
	padding:3px;
	border:1px solid #000;
}
table th{
	font-weight:bold;
	color:#fff;
	background-color:#000;
}
</style>
<body>

<div style="padding:0px;font-size:17px;" align="center">
<img src="../images/esajeelogo.jpg" width="150" height="50">
<br />
<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
<b>Think globally shop locally</b>
</span>
</div>
<div style="font-family:Verdana, Geneva, sans-serif; font-size:14px;" align="center">
<b>Shipment Labels</b><br /><?php echo date("d:m:Y h:i:s a");?>
</div>
<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
?>
<div id="currencybox">
<?php echo $currencies;?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript: void(0)" title="Print This page" onclick="printpage()">Print<span class="printrecord">&nbsp;</span></a>&nbsp;|
<a href="javascript: void(0)" title="Print This page" onclick="emailnow()">Email<span class="email">&nbsp;</span></a>
</div><br />
<?php
$id			=	$_GET['id'];
$boxdata	=	$AdminDAO->getrows("packinglist","DISTINCT(packnumber) packnumber","fkshipmentid='$id' GROUP BY packnumber");
/*echo "<pre>";
print_r($boxdata);
echo "</pre>";
exit;*/

for($p=0;$p<sizeof($boxdata);$p++)
{
	$boxid 		=	$boxdata[$p]['packnumber'];
	$query		=	"SELECT packnumber,barcode,itemdescription,p.weight,lastpurchaseprice,pl.quantity,deadline
					FROM 
						shiplist,purchase p,packinglist pl
					WHERE
						pl.packnumber	=	'$boxid' AND
						pl.fkshipmentid	=	'$id' AND
						pl.fkpurchaseid	=	pkpurchaseid AND
						pkshiplistid	=	p.fkshiplistid
				";
	$shipdata	=	$AdminDAO->queryresult($query);
	/*echo "<pre>";
print_r($shipdata);
echo "</pre>";
exit;*/
?>
<div id="maildata">
<table width="900">
  <tr bgcolor="#CCCCCC">
    <td colspan="2"><strong>Box No.</strong></td>
    <td colspan="3"><?php echo $shipdata[0]['packnumber'];?></td>
    </tr>
  <tr>
    <th width="5%">Sr. #</th>
    <th width="20%">Barcode</th>
    <th width="35%">Item</th>
    <th width="20%">Quantity</th>
    <th width="20%">Weight</th> 
  </tr>
  <?php 
  for($i=0;$i<sizeof($shipdata);$i++)
  {
  ?>
  <tr bgcolor="<?php echo $color; ?>">
    <td><?php echo $i+1;?></td>
    <td><?php echo $shipdata[$i]['barcode'];?></td>
    <td><?php echo $shipdata[$i]['itemdescription'];?></td>
    <td><?php echo $shipdata[$i]['quantity'];?></td>   
     <td><?php echo $shipdata[$i]['weight'];?></td>
  </tr>
	  <?php
       /* $price		=	$shipdata[$i]['lastpurchaseprice']*$shipdata[$i]['quantity'];
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
            $pricetotal	=	$totalprice;
        }*/
        $weight	+=	($shipdata[$i]['quantity'])*($shipdata[$i]['weight']);
		$tqty	+=	$shipdata[$i]['quantity'];
      }
  }
  $weightkg=$weight/1000;
  ?>
  <tr>
    <th colspan="2">Total </th>
    <td><?php echo $i. " Items";?></td>
    <td><?php echo $tqty;?></td>
    <td><?php echo $weightkg." Kg ($weight gm)";?></td>
  </tr>
</table>
</div>
<div id="currencybox2" style="clear:both;margin-top:10px;">
<?php echo $currencies;?>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript: void(0)" title="Print This page" onclick="printpage()">Print<span class="printrecord">&nbsp;</span></a>&nbsp;|
<a href="javascript: void(0)" title="Print This page" onclick="emailnow()">Email<span class="email">&nbsp;</span></a>
</div><br />
<div id="emaildiv" style="display:none;">
	<form name="emalfrm" id="emailfrm" method="post" action="">
   	  <table width="738" border="0">
    	  <tr>
    	    <td height="50" colspan="2"><strong>Email This Report </strong>
            <div style="float:right">
            <span class="buttons">
                <button type="button" class="positive" onclick="sendemail(-1);">
                    <img src="../images/email_go.png" alt=""/> 
                   Send
                </button>
                <button type="button" class="negative" onclick="hideclass('emaildiv');">
                    <img src="../images/cross.png" alt=""/>
                    Cancel
                </button>
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
    	    <td height="50" colspan="2">
            <div style="float:left;">
            <span class="buttons">
                <button type="button" class="positive" onclick="sendemail(-1);">
                    <img src="../images/email_go.png" alt=""/> 
                   Send
                </button>
                <button type="button" class="negative" onclick="hideclass('emaildiv');">
                    <img src="../images/cross.png" alt=""/>
                    Cancel
                </button>
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
		window.location	=	'shipmentlabels.php?currency='+cid+'&id=<?php echo $id;?>';
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