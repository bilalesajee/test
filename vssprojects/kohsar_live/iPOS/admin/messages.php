<?php

include_once("../includes/security/adminsecurity.php");

if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

	include_once("dbgrid.php");

}//end edit

global $AdminDAO;

/*************************DATE CHECKS**************************/

/* $sdate				=	strtotime($_GET['sdate']); 

 $edate				=	strtotime($_GET['edate']);



 if($sdate != '' && $edate!='')

  {

   $cond = "  datetime  between '$sdate' and '$edate'"; 

  }

*/



$addressbookid= $_SESSION['addressbookid'];

$query = "select FROM_UNIXTIME(m.datetime,'%d-%m-%Y  %h:%i %p') as datetime,m.message_id,m.message,m.status as st,

IF (m.status=1,'Read','Unread') as status,CONCAT(b.firstname ,', ', b.lastname) as from_user,CONCAT(bb.firstname ,', ', bb.lastname) as to_user,m.subject

from $dbname_detail.messages m left join $dbname_main.addressbook b on b.pkaddressbookid = m.from_user left join $dbname_main.addressbook bb on bb.pkaddressbookid = m.to_user

where  (m.to_user = '$addressbookid' or m.from_user='$addressbookid') order by m.message_id desc";

$reportresult = $AdminDAO->queryresult($query);



/*////////////////////////////////////delete////////////////////////////////////////////

$message_id=$_GET['message_id'];



///////////////////////////////////////////delete/////////////////////////////////////

if($_GET['action']=='deleted')

{

$delete=mysql_query("delete from $dbname_main.messages where message_id='$message_id'") or die(mysql_error());

echo "<script>window.location='messages.php';</script>";

}*/

///////////////////////////////////////////submit////////////////////////////////////

/**************************************************************/



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<title>Messages</title>

<link rel="stylesheet" type="text/css" href="/style.css" />

<link href="style.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../includes/js/jquery.js"></script>

</head>

<script>





$(function(){



 var sThisVal='';

 

///////////////////////////////////mark as read//////////////////////////////

	$(".butt").click(function () {

var checkValues = $('input[name=message_id]:checked').map(function()

{

return $(this).val();

}).get();

	 $.ajax({

type: "POST",

url:'update_status.php',

data:{message_id:checkValues},

success: function(data){

	alert('Message Read');

myWindow=window.open('messages.php',"myWin","menubar,scrollbars,left=30px,top=40px,height=800px,width=1024px");



myWindow.focus();

}

});

 });

 //////////////////////////////////// for Mark As Unread///////////////////////////////////

 $(".butt2").click(function () {

var checkValues = $('input[name=message_id]:checked').map(function()

{

return $(this).val();

}).get();

	 $.ajax({

type: "POST",

url:'update_status_unread.php',

data:{message_id:checkValues},

success: function(data){

	alert('Message Unread');

myWindow=window.open('messages.php',"myWin","menubar,scrollbars,left=30px,top=40px,height=800px,width=1024px");



myWindow.focus();

}

});

 });

 ///////////////////////////////////////////////////////////////////





    // add multiple select / deselect functionality

    $("#selectall").click(function () {

          $('.case').attr('checked', this.checked);

    });

 

    // if all checkbox are selected, check the selectall checkbox

    // and viceversa

    $(".case").click(function(){

 

        if($(".case").length == $(".case:checked").length) {

            $("#selectall").attr("checked", "checked");

        } else {

            $("#selectall").removeAttr("checked");

        }

 

    });

});







	



</script>

<body bgcolor="#EDF7FE"><br />

<br />

<form  name="messageform" id="messageform" style="width:920px;" onSubmit="addform(); return false;" class="form">

<table width="101%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F4E7BB" class="border">

  <tr>

    <td height="24" colspan="7" align="right" bgcolor="#BBD9EE" class="headerfont"><button type="button" class="butt" >

  

    Mark as Read

</button>
<?php if($addressbookid==1888){?>
<button type="button" class="butt2" >

  

    Mark as Unread

</button>&nbsp;&nbsp;&nbsp; <?php }?><button type="button" onClick="JavaScript:window.close()" class="butt" >

  

    Close

</button></td>

  </tr>

  <tr>

    <td height="60" colspan="7" align="center" bgcolor="#BBD9EE" class="headerfont">Messages</td>

  </tr>

  <tr>

    <td width="6%" align="center" bgcolor="#BBD9EE" class="s1"><input type="checkbox" id="selectall"/></td>

    <td width="44%" align="center" bgcolor="#BBD9EE" class="s1">Subject</td>

    <td width="15%" align="center" bgcolor="#BBD9EE" class="s1">From User</td>

    <td width="17%" align="center" bgcolor="#BBD9EE" class="s1">To User </td>

    <td width="18%" align="center" bgcolor="#BBD9EE" class="s1">Date</td>

   <!-- <td width="12%" align="center" bgcolor="#BBD9EE" class="s1">Status</td>-->

    <!--<td width="13%" align="center" bgcolor="#BBD9EE" class="s1">Delete</td>-->

  </tr>

  <?php

				//$q=mysql_query("select * from news_events order by id") or die(mysql_error());

				for($i=0;$i<count($reportresult);$i++)

	

				{ 

				if($reportresult[$i]['st'] == 1)

				{

				?>

  <tr>

   <td align="center" 20% valign="top" bgcolor="#EDF7FE" ><input type="checkbox"  name="message_id" class="case" id="message_id" value="<?php echo $reportresult[$i]['message_id'];?>" /></td>

  

    <td align="left" 20% valign="top" bgcolor="#EDF7FE" class="topmenu"> <?php echo "<a  href=\"messages_detail.php?message_id=".$reportresult[$i]['message_id']."\">".$reportresult[$i]['subject']."</a><br />" ; ?></td>

    <td align="left" width="15%" valign="top" bgcolor="#EDF7FE" class="s4"><?php echo $reportresult[$i]['from_user'];?></td>

    <td align="left" width="17%" valign="top"  bgcolor="#EDF7FE" class="s4"><?php echo $reportresult[$i]['to_user'];?></td>

    <td width="18%" height="34" align="center" valign="top"  bgcolor="#EDF7FE" class="s4"><?php echo $reportresult[$i]['datetime'];?></td>

    <!--<td width="12%" height="34" align="center" valign="top"  bgcolor="#EDF7FE" class="s4"><?php// echo $reportresult[$i]['status'];?></td>-->

   <!-- <td height="34" width="13%" align="center"  bgcolor="#EDF7FE" class="s4"><a href="messages.php?action=deleted&amp;message_id=<?php// echo $reportresult[$i]['message_id'];?>" onClick='return confirm("Are U Sure Want To Delete");' ><img src="1273649412_Error.png" alt="ff" width="30" height="30" border="0" /></a></td>-->

  </tr>

  <?php } else {?>

	   <tr>

	     <td align="center" 20% valign="top" bgcolor="#DA6745" ><input type="checkbox" name="message_id" class="case" id="message_id" value="<?php echo $reportresult[$i]['message_id'];?>" /></td> 

    <td align="left" 20% valign="top" bgcolor="#DA6745" class="topmenu"> <?php echo "<a  href=\"messages_detail.php?message_id=".$reportresult[$i]['message_id']."\">".$reportresult[$i]['subject']."</a><br />" ; ?></td>

    <td align="left" width="15%" valign="top" bgcolor="#DA6745" class="s5"><?php echo $reportresult[$i]['from_user'];?></td>

    <td align="left" width="17%" valign="top"  bgcolor="#DA6745" class="s5"><?php echo $reportresult[$i]['to_user'];?></td>

    <td width="18%" height="34" align="center" valign="top"  bgcolor="#DA6745" class="s5"><?php echo $reportresult[$i]['datetime'];?></td>

   <!-- <td width="12%" height="34" align="center" valign="top"  bgcolor="#DA6745" class="s5"><?php// echo $reportresult[$i]['status'];?></td>-->

   <!-- <td height="34" width="13%" align="center"  bgcolor="#DA6745" class="s5"><a href="messages.php?action=deleted&amp;message_id=<?php// echo $reportresult[$i]['message_id'];?>" onClick='return confirm("Are U Sure Want To Delete");' ><img src="1273649412_Error.png" alt="ff" width="30" height="30" border="0" /></a></td>-->

  </tr>

	  

	 <?php  }?>

  <tr>

    <td  colspan="7" align="left" valign="top" bgcolor="#EDF7FE" class="topmenu" 20%><hr/></td>

  </tr>

  <?php }?>

</table>

</form>



