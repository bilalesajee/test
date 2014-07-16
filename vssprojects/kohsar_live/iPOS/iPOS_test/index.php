<?php
ob_start();
@session_start();
error_reporting(7);
$empid		=	$_SESSION['addressbookid'];
if($_GET['pos'])
{
	$poscounter	=	$_GET['pos'];
}
if(isset($_SESSION['countername'])){//edit by Ahsan on 23/02/2012, added if condition
	$poscounter	=	$_SESSION['countername'];
}
if(!isset($_SESSION['pos_section']) || !isset($empid) || $empid=='' || $empid==0)
{
	header("Location:userlogin.php?pos=$poscounter");
	exit;
}
/*echo '<pre>';
print_r($_SESSION);
echo '</pre>';*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Esajee POS</title>
<link rel="stylesheet" type="text/css" href="includes/css/style.css" />
<link rel="stylesheet" type="text/css" href="includes/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="includes/css/ui.datepicker.css" />
<script type="text/javascript" src="includes/js/jquery.js"></script>
<script src='includes/js/jquery.maskedinput.js' type='text/javascript'></script>
<script src="includes/js/jquery.form.js" type="text/javascript"></script>
<script src="includes/js/shortcut.js"></script>
<script src="includes/js/common.js" type="text/javascript"></script>
<script type='text/javascript' src='includes/js/ui.datepicker.js'></script>
<script type='text/javascript' src='includes/js/jquery.autocomplete.js'></script>
<script language="javascript" type="text/javascript">
	var previous = 'Sale_tab';
	function selecttab(id,url)
	{ 
		loading("Loading...");
		document.getElementById(id).className="current";
		if(!previous)
		{
			previous = 'Sale_tab';
		}
		if(id!= previous)
		{
			document.getElementById(previous).className="";
		}
		loadsection('main-content',url);
		previous  = id;
	}
	function loadsection(div,url)
	{
		$('#'+div).load(url);
	}
   $(document).ready(function() 
	{
		
  		loadsection('main-content','sale.php');
 });
function activatesale(val)
{
	loadsection('main-content','sale.php?tempsaleid='+val);
}
function activatetpmode(pass)
{
	chk	=	document.getElementById('tpval').innerHTML;
	//alert(chk);
	if(chk==1)
	{
		loadsection('main-content','sale.php?tpmode=1');
	}
	else
	{
		password=prompt('Please enter your password!');
		if (password==pass)
		{
			loadsection('main-content','sale.php?tpmode=1');
		}
		else
		{
			return false;
		}
	}
}
function fnctpmode()
{
	$('#tpmodediv').load('tpmodefrm.php');
}
function cancelbf()
{
	loadsection('main-content','sale.php');
}
// commented by Yasir -- 08-07-11
/*shortcut.add("Ctrl+insert",function() 
{
	loadsection('main-content','barcodefilterfrm.php');
	return false;
});
*/
shortcut.add("Ctrl+home",function() 
{
	cancelbf();
	return false;
});
shortcut.add("Ctrl+e",function() 
{
	fnctpmode();
	return false;
});
function fnchotelmode()
{
	loadsection('main-content','sale.php?tpmode=2');
}
shortcut.add("Ctrl+y",function() 
{
	fnchotelmode();
	return false;
});

shortcut.add("Ctrl+s",function() 
{
	selecttab('Sale_tab','sale.php')
	return false;
});
shortcut.add("Ctrl+b",function() 
{
	selecttab('Billing_tab','billing.php')
	return false;
});

shortcut.add("Ctrl+c",function() 
{
	selecttab('Customers_tab','customers.php')
	return false;
});
shortcut.add("F2",function() 
{
	if(tempsaleid != '')
	{
		loadsection('main-content','payment.php');
	}
	return false;
});
shortcut.add("Ctrl+i",function() 
{
	printaleinvice('<?php echo $tempsaleid;?>');
	return false;
});

shortcut.add("Ctrl+u",function() 
{
	selecttab('Payouts_tab','payouts.php')
	return false;
});
// commented by Yasir -- 08-07-11
/*shortcut.add("Ctrl+o",function() 
{
	selecttab('Promotions_tab','promos.php')
	return false;
});*/
// added by Yasir -- 08-07-11
shortcut.add("Ctrl+o",function() 
{
	if(document.getElementById('newcustomer').style.display=='block')
	{
		document.getElementById('newcustomer').style.display='none';
	}
});
//
shortcut.add("Ctrl+F6",function() 
{
	selecttab('Delivery Chalan_tab','billing.php?param=deliverychalan')
	return false;
});

/*shortcut.add("esc",function() 
{ jQuery('#popupdiv').show();
  loadsection('popupdiv','cancelsale.php');
});
*/
shortcut.add("Esc",function() 
{
  jQuery('#cancelsalepopup').show();
  loadsection('cancelsalepopup','cancelsale.php');
});
function cancel_sale(){
 jQuery('#cancelsalepopup').show();
  loadsection('cancelsalepopup','cancelsale.php');
}
function errorcancel()
{
	document.getElementById('cancel_error').style.display = 'block';
	document.cancelsalefrm.uname.value = '';
	document.cancelsalefrm.pass.value = '';
	document.cancelsalefrm.uname.focus();
}
function errordiscount()
{
	document.getElementById('discount_error').style.display = 'block';
	document.discountfrm.uname2.value = '';
	document.discountfrm.pass2.value = '';
	document.discountfrm.uname2.focus();
}
function errorpayout()
{
	document.getElementById('payout_error').style.display = 'block';
	document.payoutfrm.uname3.value = '';
	document.payoutfrm.pass3.value = '';
	document.payoutfrm.uname3.focus();
}
// commented by Yasir -- 08-07-11
/*shortcut.add("Ctrl+m",function() 
{
	selecttab('Hotel_tab','sale.php?tpmode=2');
	//selecttab('Payment Methods_tab','payments.php')
	return false;
});
*/
// added by Yasir -- 08-07-11
shortcut.add("Ctrl+m",function() 
{
	if(document.getElementById('disc').style.display=='block')
	{
		document.getElementById('disc').style.display='none';
	}
});
//
shortcut.add("Ctrl+l",function() 
{
	loadsection('main-content','sale.php?salecompleted=3');
	return false;
});
shortcut.add("Ctrl+h",function() 
{
	document.getElementById('holdingsales').focus();
	return false;
});
// commented by Yasir -- 08-07-11
/*shortcut.add("Ctrl+g",function() 
{
	selecttab('Closing_tab','closinginfo.php');
	return false;
});
*/
shortcut.add("Ctrl+g",function() 
{
	document.getElementById('holdingsales').focus();
	return false;
});
shortcut.add("Ctrl+r",function() 
{
	selecttab('Currency Rates_tab','rates.php');
	return false;
});
////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//////////////////function added by fahad 13-2-12/////////////////////////////////////////////////////////
shortcut.add("Ctrl+J",function() 
{
	// alert('hello its fadi');
	
    if(document.getElementById('profit_check').style.display=='block')
	{
		document.getElementById('profit_check').style.display='none';
	}
	else
	{
		jQuery('#profit_check').load('profit_check.php');
		document.getElementById('profit_check').style.display='block';
	}	
	//jQuery('#profit_check').show();
	//loadsection('profit_check','profit_check.php');
	return false;
});
//////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

shortcut.add("Ctrl+;",function() 
{
//open the pricechange screen
	//alert('hello');
	loadsection('main-content','pricechangetasks.php');
	return false;
});

shortcut.add("Ctrl+z",function() 
{
	var hsales	=	document.getElementById('holdingsales');
	var len		=	(hsales.length) -1;
	if( len > 0)
	{
		alert('Please clear '+len+' sale(s) on hold.');
		return false;
	}
	loadclosingfrm();
    			
});

//shortcuts
function loadclosingfrm()
{
	jQuery("#closingfrmdiv").load("closingfrm.php");
	document.getElementById('closingfrmdiv').style.display='block';
}
shortcut.add("F5",function() 
{
	//loadsection('main-content','sale.php');
	refreshnav();
	return false;
});
shortcut.add("F12",function() 
{
	
	jQuery('#deleteitemreason').show();
	loadsection('deleteitemreason','delsolditemdialog.php');
	return false;
});
/*shortcut.add("Ctrl+s",function() 
{
	shortcutsale();
});
*/shortcut.add("f1",function() 
{
	if(document.getElementById('shortcuts').style.display=='block')
	{
		document.getElementById('shortcuts').style.display='none';
	}
	else
	{
		jQuery('#shortcuts').load('shortcut2.php');
		document.getElementById('shortcuts').style.display='block';
	}
});
shortcut.add("Ctrl+right",function() 
{
	var billindex	=	document.getElementById('billindex').value;
	getbills(billindex,'next');
	return false;
});
shortcut.add("Ctrl+left",function() 
{
	var billindex	=	document.getElementById('billindex').value;
	getbills(billindex,'back');
	return false;
});
shortcut.add("Ctrl+Alt+b",function() 
{
	loading('Loading...');
	document.getElementById('billsadvancesearch').style.display='block';
	jQuery('#billsadvancesearch').load('billadvancesearch.php?act=fresh');
	//$('#billsadvancesearch').modal();
	return false;
});
shortcut.add("Ctrl+alt+p",function() 
{
	//document.getElementById('currentsaleid').value;
	if(document.getElementById('currentsaleid'))
	{
		//alert('hello');
		var currentsaleid	=	document.getElementById('currentsaleid').value;
		printaleinvice(currentsaleid);
		return false;
	}
	
});
shortcut.add("End",function() 
{
	if(document.getElementById('advancebillingsearching'))
	{
		cancelbillsearch();
	}
	return false;
});

function cancelbillsearch()
{
		
		jQuery('billsadvancesearch').html('');
		document.getElementById('billsadvancesearch').style.display='none';
		//loadsection('main-content','sale.php');
		selecttab('Sale_tab','sale.php');
		return false;
	   // autodivid='';
				
}


function getinput(msg,msg2)
{
	var declearedamount = prompt(msg, "");	
	alert(declearedamount);
	if(declearedamount!='null')
	{
		return 	declearedamount;
	}
	else
	{
		return false;
	}
}
function notice(text,cancel,timelimit)
{
	if(cancel=='1')
	{
		jQuery('#notice').hide();

	}
	else
	{
		document.getElementById('notice').style.display='block';
		text=text+" <a href='javascript:notice(0,1,0)'><img src='images/hr.gif' border='0' /></a>";
		jQuery('#notice').html(text);
		jQuery('#notice').fadeOut(timelimit);	
	}
}
function printaleinvice(tempsaleid)
{
	
var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
		//alert('from empty');
	window.open('generatebill.php?tempsaleid='+tempsaleid,'Invoice',display); 
}
function printcustomerinvoice(tempsaleid,customerid)
{
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=600,left=100,top=25';
	window.open('generatecreditorreport.php?tempsaleid='+tempsaleid+'&customerid='+customerid,'Invoice',display); 
}
function printcollectionbill(customerid)
{
	
var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
 	window.open('generatecollectionbill.php?customerid='+customerid,'Invice',display); 
}
function printpayoutbill(text)
{
	
var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
 	window.open('generatepayoutbill.php?text='+text,'Invice',display); 
}
function delsaleitem()
{
	
	/*if(confirm("Are you sure to delete this item from sale."))
	{*/
		
		var	delitems	=	document.getElementById('delitems').value;
		var delreason	=	document.getElementById('delreason').value;
		if(delreason=='' || delitems == '')
		{
			alert("Please select item and reason to delete this item from sale.");
			document.getElementById('delreason').focus();
			return false;
		}
		else
		{
			jQuery('#instancediv').load('delsolditem.php?'+delitems+'&action=del&delreason='+delreason);
			loadsection('main-content','sale.php');
			notice('The item has been deleted from the current sale.',0,5000);
		}
	/*}*/
}

/* payment shortcuts*/
function newcashprocess()
{
	processcash();
	return false;
}
/*shortcut.add("Ctrl+w",function() 
{
	newcashprocess();
	
});*/
shortcut.add("F3",function() 
{
	if(document.getElementById('completetransactionbutton'))
	{
		paycomplete();
	}
	return false;
});
shortcut.add("F4",function() 
{
	givediscount();
	return false;
});
shortcut.add("F6",function() 
{
	processdiscount();
	return false;
});
shortcut.add("F7",function() 
{
	newcustomer();
	return false;
});
shortcut.add("F8",function() 
{
	savecustomer(0);
	return false;
});
shortcut.add("F9",function() 
{
	savecustomer(1);
	return false;
});
shortcut.add("Ctrl+x",function() 
{
	loadsection('main-content','sale.php');
	return false;
});
shortcut.add("F10",function() 
{
	loadsection('main-content','sale.php?salecompleted=adjustment');
	return false;
});
shortcut.add("F11",function() 
{
 // commented and added processcash by Yasir -- 08-07-11	
	//givereturn();
	processcash();
	return false;
});
shortcut.add("Ctrl+F1",function()
{
	jQuery('#editsaleitems').show();
	loadsection('editsaleitems','editsaleitems.php');
	return false;
});
shortcut.add("Ctrl+,",function()
{
	jQuery('#movetocounter').show();
	loadsection('movetocounter','newcounter.php');
	return false;
});
shortcut.add("Ctrl+F2",function() 
{
	jQuery('#popupdiv').show();
	loadsection('popupdiv','searchbillitem.php');
	return false;
});
// added by yasir 12-09-11
shortcut.add("Ctrl+1",function() 
{
	jQuery('#cancelsalepopup').show();
	loadsection('cancelsalepopup','barcodeprice.php');	
	return false;
});
//
shortcut.add("Ctrl+v",function() 
{
	//alert('hello');
	//jQuery('#popupdiv').show();
	jQuery('#main-content').load('creditorbilling.php');
	return false;
});
shortcut.add("Ctrl+k",function() 
{
	loadsection('main-content','sale.php?breakmode=1');
	return false;
});
shortcut.add("Ctrl+d",function() 
{
	loadsection('main-content','sale.php?discountsmode=1');
	return false;
});
function refreshnav()
{
	loadsection('content','includes/menu/usercontrols.php');
	loadsection('navigation','includes/menu/nav.php');
	loadsection('main-content','sale.php');	
}
/* END of payment shortcuts*/
function startclock()
{
	var thetime=new Date();
	var nhours=thetime.getHours();
	var nmins=thetime.getMinutes();
	var nsecn=thetime.getSeconds();
	var nday=thetime.getDay();
	var nmonth=thetime.getMonth();
	var ntoday=thetime.getDate();
	var nyear=thetime.getYear();
	var AorP=" ";
	if (nhours>=12)
    	AorP="P.M.";
	else
    	AorP="A.M.";
	if (nhours>=13)
    	nhours-=12;
	if (nhours==0)
   		nhours=12;
	if (nsecn<10)
 		nsecn="0"+nsecn;
	if (nmins<10)
 		nmins="0"+nmins;
	if (nday==0)
  		nday="Sunday";
	if (nday==1)
  		nday="Monday";
	if (nday==2)
  		nday="Tuesday";
	if (nday==3)
  		nday="Wednesday";
	if (nday==4)
  		nday="Thursday";
	if (nday==5)
  		nday="Friday";
	if (nday==6)
  		nday="Saturday";
		nmonth+=1;
	if (nyear<=99)
  		nyear= "19"+nyear;
	if ((nyear>99) && (nyear<2000))
 		nyear+=1900;
		document.getElementById('timeclock').innerHTML=nhours+": "+nmins+": "+nsecn+" "+AorP+" "+nday+", "+ntoday+"/"+nmonth+"/"+nyear;
		setTimeout('startclock()',1000);
} 
</script>
</head>
<body>
<div id="container">
<span id="breakmodediv" style="margin:5px 0 0 110px;float:left;position:absolute;background-color:#F00;width:70px; font-weight:bold; color:#FFF; text-decoration:blink; display:none;  padding:10px;" title="In Break Mode you are only allowed for credit sales. cash Sales are stoped.">Break Mode</span>
<div id="mnu1" style="height:20px;"><div style="float:left;"><img src="images/esajeelogo.jpg" width="95" height="33" alt="Esajee and Co." class="img" /></div><div id="timeclock" style="float:left;margin:5px 0 0 250px;color:#333399;font-weight:bold;"></div><span id="discountsmodediv" style="margin:5px 0 0 750px;float:left;position:absolute;background-color:#F00;width:100px; font-weight:bold; color:#FFF; text-decoration:blink; display:none;  padding:10px;" title="In Break Mode you are only allowed for credit sales. cash Sales are stoped.">Discounts Mode</span><a href="signout.php"><span style="position:absolute;margin:5px 0 0 372px;"><img src="images/signout.gif" border="0" /></span></a>
</div>
<div id="loading" class="loading" style="display:none;"></div>
<div id="notice" class="notice" style="display:none"></div>
<div id="popupdiv" class="delreasonbox" style="margin-top:180px">
</div>
<div id="cancelsalepopup" class="cancelsalebox" style="position:absolute;margin-top:180px">
</div>
<div id="content">
    <?php 
        include_once("includes/menu/usercontrols.php");
    ?>
</div>
<br  />
<div id="navigation">
<?php 
	include_once("includes/menu/nav.php");
?>
</div>
    <div id="billsadvancesearch" style="display:none;position:absolute;background:#FFF; z-index:100000; border:double; border-color:#CC0033; width:645px">
	
	</div>
	<div id="main-content"></div>
    <div id="openingfrmdiv" style="display:none;position:absolute;background:#FFF;"></div>
		<div id="useralertsdiv">
		<?php 
            include_once("useralerts.php");
        ?>
		</div>
    <div id="shortcuts" style="display:none;position:fixed;z-index:101;background-color:#fff;bottom:0;" class="help">
    </div>
       <!-- Div added by fahad 14-2-12 -->
  <div id="profit_check" background:#FFF0E1; 
	
	
		style="display:none;overflow: -moz-scrollbars-vertical;
	overflow-y: auto;
	position:absolute;   z-index:101;  background:#FFF0E1; height:605px;  width:973px;  border:#FFA042 3px solid; " class="help">
    </div>
 <!-- Div added by fahad 14-2-12 -->
 
    <div id="closingfrmdiv" style="display:none;position:absolute;z-index:100;background-color:#fff; margin-top:-120px;"></div>
</div>
<div id="tpmodediv" style="display:none;"></div><div id="tpval" style="display:none;"></div>
<div id="cancelsalediv" style="display:none;"></div>
</body>
</html>
<?php 
if(!isset($_SESSION['closingsession']) || $_SESSION['closingsession']=='' || $_SESSION['closingsession']==0)
{
	$closingquery	=	"SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
	$closingarray	=	$AdminDAO->queryresult($closingquery);
	$closingsession	=	$closingarray[0][pkclosingid];
	if($closingsession=='')
	{
		?>
        <script language="javascript" type="text/javascript">
		$('#openingfrmdiv').load('openingfrm.php');
		document.getElementById('openingfrmdiv').style.display	=	'block';
		</script>
        <?php
	}
}
ob_end_flush();
?>
<SCRIPT language="JavaScript">
<!--
startclock();
//-->
</SCRIPT>