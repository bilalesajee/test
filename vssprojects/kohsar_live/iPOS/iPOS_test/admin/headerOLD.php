<?php
require_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$employeeid				=	$_SESSION['employeeid'];
$_SESSION['section']	=	$_GET['sectionid'];
$section				=	$_SESSION['section'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head>
<?php //add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition ?>
	<!--<script type="text/javascript" src="../includes/js/jquery.js"></script>-->
	<script type="text/javascript" src="../includes/js/jquery-1.5.js"></script>
	<script type="text/javascript" language="javascript" src="../includes/uitokenizer/tokenizerassets/jquery.tokenizer.js"></script>
	<link href="../includes/uitokenizer/tokenizerassets/token-input.css" rel="stylesheet" type="text/css" />
	<link href="../includes/uitokenizer/tokenizerassets/token-input-horizental.css" rel="stylesheet" type="text/css" />
    
	<link rel="stylesheet" href="../includes/js/datepicker/ui.datepicker.css" type="text/css" media="screen" title="core css file" charset="utf-8" />
    <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
	<link href="../includes/css/button.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="../includes/css/jquery.autocomplete.css" />
    <link href="../includes/css/jNice.css" rel="stylesheet" type="text/css" />
    
	<script type="text/javascript" src="../includes/js/common.js"></script>
    <script src="../includes/js/jquery.form.js" type="text/javascript"></script>
     <script src="../includes/js/ajaxfileupload.js" type="text/javascript"></script>
    <script type='text/javascript' src='../includes/js/jquery.autocomplete.js'></script>
	 <script src="../includes/js/datepicker/ui.datepicker.js" type="text/javascript" charset="utf-8"></script>
	<script type='text/JavaScript' src='../includes/js/datecontrol.js'></script>
    <script type="text/javascript" src="../includes/js/jquery.jNice.js"></script>
    <script src='../includes/js/jquery.simplemodal.js' type='text/javascript'></script>
    <script src='../includes/js/jquery.maskedinput.js' type='text/javascript'></script>
	<script src="../includes/js/shortcut.js"></script>
	<script src="../includes/js/jquery.bstablecrosshair.js"></script>
	<script type="text/javascript" language="javascript" src="../includes/js/jquery.dataTables.js"></script>
<?php 	//add comment by ahsan 24/02/2012// }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012?>
    
<?php 	//add comment by ahsan 24/02/2012// }//end edit?>    
<script language="javascript" type="text/javascript">
shortcut.add("f2",function() 
{
	var barcode	=	prompt("Enter Barcode");
	document.getElementById('bcstock').style.display	=	'block';
	//jQuery('#bcstock').load('bcstock.php?bc='+barcode);
	//openpopup(500,500,'bcstock.php');
	if(barcode)
	{
		barcode	=	trim(barcode);
		loadsection('bcstock','bcstock.php?bcx='+barcode);
	}
	else
	{
		return false;
	}

});
shortcut.add("f3",function() 
{
	barcodeproduct();
});
function barcodeproduct()
{
	var barcode	=	prompt("Enter Barcode to find Product");
<?php	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition?>
	if(barcode)
<?php	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012?>
	if(barcode!='')
<?php }//end edit?>	
	{
		barcode	=	trim(barcode);
		loadsection('maindiv','manageproducts.php?barcode='+barcode);
	}
	else
	{
		return false;
	}
}
var previous = '';
function selecttab(id,url)
{
	//$('#riz').load('footer.php');
	document.getElementById(id).className="active";
	document.getElementById(id+'_b').className="active";
	if(id != previous && previous!= '')
	{
		document.getElementById(previous).className="inactive";
		document.getElementById(previous+'_b').className="inactive";		
	}
	loadsection('center-column',url);
	previous  = id;
}
function loadsection(div,url)
{
	$('#'+div).load(url);
}
function show_types(id,clos)
{	
	
	if(clos=='1')
	{
		document.getElementById(id).style.display='block';

	}else
	{
		document.getElementById(id).style.display='none';
		
	}
	
}
function loadactionitem(page,id)
{
	 loadsection('center-column',page+'?id='+id);
}
 $(document).ready(function() {
	
//  loadsection('center-column','managearrivals.php');
  /*selecttab('2_tab','managearrivals.php');*/
	window.history.forward(1);
});
function openpopup(wid,hig,page)
 {
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	//jQuery('body').append('closingprint.php');
 	window.open(page,'Closing',display); 	 
}
function selectmodule(moduleid)
{
	window.location='index.php?sectionid='+moduleid;
}
<?php 	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012?>
function focusfield(id)
{
	document.getElementById(id).focus();
}
function printgrid(title)
{
	var wid=800;
	var hig='';
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	//jQuery('body').append('closingprint.php');
 	//alert(title);
	window.open("printgrid.php?title="+title,'print',display); 	
}
<?php }//end edit ?>
</script>
</head>
<?php
	if(sizeof($_SESSION['screens'])==0)
	{
		include("../includes/classes/error.php");
		$Error->display(1);
		//header("Location:userlogin.php");
		print"<script>window.location='userlogin.php';</script>";
		/*print"<script>
			function Func1()
			{
				window.location='userlogin.php';		
			}
			function Func1Delay()
			{
				setTimeout('Func1()', 0000);
			}
			Func1Delay();

		</script>";*/
		exit;
		
	}
$screens	=	$_SESSION['screenids'];
$screens	= 	@implode(",", $screens);
?>
<body>
<div id="bcstock"></div>
<div id="main">
<div id="notice" style="display:none"></div>
	<div id="header" >
        <div id="header_left"></div>
   <div id="header_right"></div>

        <div style="margin:5px 20px 0 150px;float:left;">
            Welcome <b><font color="#DA6745"><?php echo $_SESSION['name'];?></font></b> 	You are logged in as <b><font color="#DA6745"><?php echo $_SESSION['groupname']; ?></font></b>&nbsp;&nbsp;&nbsp;&nbsp;
		Go to
        
		<select name="selmodule" id="selmodule" onchange="selectmodule(this.value);">
        <?php
		//selecting modules
		/*//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
			$sectionres	=	$AdminDAO->getrows("section","*","status=1");
		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012*///add comment by ahsan 24/02/2012// 
			
			$sectionres	=	$AdminDAO->getrows("section","*","status=1 ORDER BY `sectionname` ASC");	//commented by jafer balti [09-03-12]

			//**************coding by jafer balti [09-03-12]********************//
			
				
			/*$screenids	=	implode(',',$_SESSION['screenids']);			
			$sectionres	=	$AdminDAO->queryresult("
			SELECT section.*
			FROM screen, section
			WHERE pkscreenid
			IN ($screenids)
			AND fksectionid = pksectionid
			AND status=1
			GROUP BY fksectionid
			ORDER BY pksectionid ASC");	*/
			//**************coding by jafer balti [09-03-12]********************//					

		//add comment by ahsan 24/02/2012// }//end edit
		if($section=='')
		{
			$section	=	$sectionres[0]['pksectionid'];
		}
		for($i=0;$i<sizeof($sectionres);$i++)
		{
			$sectionid		=	$sectionres[$i]['pksectionid'];
			$sectionname	=	$sectionres[$i]['sectionname'];
		?>
        	<option value="<?php echo $sectionid;?>" <?php if($sectionid==$section){ echo "selected=\"selected\"";}?>><?php echo $sectionname;?></option>
        <?php
		}
		$tabres		=	$AdminDAO->getrows("screen s","*"," pkscreenid IN ($screens) AND fkmoduleid = (select pkmoduleid from module where modulename='Admin') AND fksectionid='$section' ","displayorder","ASC");
		?>
        </select>
        </div>
        <div style="float:right;margin:5px 10px 0 0;">
             <a href="../signout.php">
                <img src="../images/signout.png" alt=""/>
            </a>
        </div>
<ul id="top-navigation">
<?php
$chk = 0;
for($i=0;$i<sizeof($tabres);$i++)
{
	$screenname		=	$tabres[$i]['screenname'];
	/*$firstscreen	=	$tabres[0]['pkscreenid'];
	$firsturl		=	$tabres[0]['url'];*/
	$pkscreenid		=	$tabres[$i]['pkscreenid'];
	$screenurl		=	$tabres[$i]['url'];
	$visibility		=	$tabres[$i]['visibility'];
	
	//edit by Ahsan on 09/02/2012
	$display_screen_tabs = "<li id=\"{$pkscreenid}_tab\">
							<span><span>
							<a href=\"javascript:void(0)\" onclick=\"javascript: selecttab('{$pkscreenid}_tab','{$screenurl}');\">
							$screenname
							</a>
							</span></span>
							</li>";
	
	//1. main, 2. global (main & store), 3. local (store)
	if($_SESSION['siteconfig'] == 1){
        if($visibility==1 || $visibility==2)
        {
			echo $display_screen_tabs;
			if ($chk == '0'){
				$firstscreen	=	$tabres[$i]['pkscreenid'];
				$firsturl		=	$tabres[$i]['url'];
				$chk++;
			}
        }
	}
	
	if($_SESSION['siteconfig'] == 2){
        if($visibility==1 || $visibility==2 || $visibility==3)
        {
			echo $display_screen_tabs;
			if ($chk == '0'){
				$firstscreen	=	$tabres[$i]['pkscreenid'];
				$firsturl		=	$tabres[$i]['url'];
				$chk++;
			}
        }
	}
	if($_SESSION['siteconfig'] == 3){	
		if($visibility==2 || $visibility==3)
		{
			echo $display_screen_tabs;
			if ($chk == '0'){
				$firstscreen	=	$tabres[$i]['pkscreenid'];
				$firsturl		=	$tabres[$i]['url'];
				$chk++;
			}
		}
	}

/*	if($visibility==2 || $visibility==3)
	{
	?>
    <li id="<?php echo $pkscreenid.'_tab';?>">
        <span><span>
        <a href="javascript:void(0)" onclick="javascript: selecttab('<?php echo $pkscreenid.'_tab';?>','<?php echo $screenurl;?>');">
            <?php echo $screenname;?>
        </a>
        </span></span>
    </li>
    <?php
	}*/
	//end edit
}
?>
</ul>
</div>