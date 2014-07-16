<?php

include("../includes/security/adminsecurity.php");

global $AdminDAO;
?>
<script language="javascript">

$().ready(function() 

	{

		$("#fromdate").mask("99-99-9999");

		$("#todate").mask("99-99-9999");

		document.getElementById('fromdate').focus();

	});

function hideform()

{

	document.getElementById('payoutdiv').style.display='none';

}

</script>

<div id="report"></div>

<div id="payoutdiv">

<br />

<div id="error" class="notice" style="display:none"></div>

<form name="reportform" id="reportform" onSubmit="addform(); return false;" style="width:920px;" class="form">

<fieldset>

<legend>

	Payouts Report

</legend>

<div style="float:right">

<span class="buttons">

    <button type="button" class="positive" onclick="showreport();">

	        <img src="../images/tick.png" alt=""/> 

	        Generate Report            </button>

	      <a href="javascript:void(0);" onclick="hidediv('payoutdiv');" class="negative">

	        <img src="../images/cross.png" alt=""/>

	        Cancel            </a>

  </span>

</div>

<table>

	<tbody>

	<tr>

	  <td>From Date: </td>

	  <td><div id="error1" class="error" style="display:none; float:right;"></div>

	    <input name="fromdate" id="fromdate" type="text" value="" onkeydown="javascript:if(event.keyCode==13) {todate.focus(); return false;}" size="8"> dd-mm-yyyy</td>

	  </tr>

	<tr>

		<td>To Date </td>

		<td><div id="error2" class="error" style="display:none; float:right;"></div><input name="todate" id="todate" type="text" value="" onkeydown="javascript:if(event.keyCode==13) {showreport();return false;}" size="8"> dd-mm-yyyy</td>

	</tr>

	<!-- -->

    <!--ADDED BY FAHAD 06-06-2012-->
<tr >

	  <td>Counter</td>

	  <td valign="top">

      	<?php //echo $storeid;?>

        <select name="countername" id="countername">

      	<option value=''>Select Counter</option>

        

        <?php

		 $sql="select 

				*

			from 

				$dbname_detail.counter

				

				

			where 

				fkstoreid='$storeid' 

				";

		$counterarr	=	$AdminDAO->queryresult($sql);

		for($i=0;$i<count($counterarr);$i++)

		{

			$countername	=	$counterarr[$i]['countername'];

			

		?>

        	<option value='<?php echo $countername;?>'><?php echo $countername;?></option>

      <?php

		}

	  ?>

      

      </select>

      

      </td>

	  </tr>
      <!--ADDED BY FAHAD 06-06-2012-->
	<tr>

	  <td colspan="2"  align="left">

	    <div class="buttons">

	      <button type="button" class="positive" onclick="showreport();">

	        <img src="../images/tick.png" alt=""/> 

	        Generate Report            </button>

	      <a href="javascript:void(0);" onclick="hidediv('payoutdiv');" class="negative">

	        <img src="../images/cross.png" alt=""/>

	        Cancel            </a>          </div>        </td>				

	  </tr>

	</tbody>

</table>

</fieldset>	

<input type="hidden" name="customerid" value = <?php echo $customerid?> />	

</form>

</div><br />

<script language="javascript">

function showreport()

{

	var fromdate		=	document.getElementById('fromdate').value;

	var todate			=	document.getElementById('todate').value;
	var countername		=	document.getElementById('countername').value;

	var w				=	800;

	var h				=	600;

	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+w+',height='+h+',left=100,top=25';

 	window.open('generatepayoutreport.php?fromdate='+fromdate+'&todate='+todate+'&countername='+countername,'Payouts Report',display); 	 

}

</script>