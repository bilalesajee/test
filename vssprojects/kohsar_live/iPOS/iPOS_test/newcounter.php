<?php
include("includes/security/adminsecurity.php");
global $AdminDAO;
@session_start();
$tempsaleid	=	$_SESSION['tempsaleid'];
?>
<form id="counterform" name="counterform" method="post" action="">
        <table width="300" align="left" class="epos">
        	<tr>
            	<th colspan="2">Active Counters</th>
            </tr>
            <?php
			// counters
			$counters		=	$AdminDAO->getrows("$dbname_detail.counter c,$dbname_detail.closinginfo ci","c.countername,pkclosingid,fkaddressbookid","c.countername=ci.countername and ci.closingstatus='i' and c.countertype=1 and ci.pkclosingid<>'$closingsession'");
			$countersel		=	"<select name=\"counter\" id=\"counter\" style=\"width:150px;\" ><option value=\"\">Select Counter</option>";
			for($i=0;$i<sizeof($counters);$i++)
			{
				$countername	=	$counters[$i]['countername'];
				$closingid		=	$counters[$i]['pkclosingid'];
				$addressbookid	=	$counters[$i]['fkaddressbookid'];
				$countersel2	.=	"<option value=\"$countername-$closingid-$addressbookid\" >Counter $countername ($closingid)</option>";
			}
			$counter			=	$countersel.$countersel2."</select>";
			// end statuses
			?>
        	<tr>
            	<td><?php echo $counter;?></td>
            </tr>
            <tr>
                <td colspan="5" align="center">          	
                   <span class="buttons" style="font-size:12px;"> 
                    <button type="button" name="button" id="button" onclick="movesale();">
                        <img src="images/tick.png" alt=""/> 
                       Save                </button>
                    <button type="button" name="button2" id="button2x" onclick="javascript:jQuery('#movetocounter').fadeOut();">
                        <img src="images/cross.png" alt=""/> 
                       Cancel                </button>
                    </span>   
                </td>
			</tr>
            <input type="hidden" name="saleid" id="saleid" value="<?php echo $tempsaleid;?>"  />
            <input type="hidden" name="closingid" id="closingid" value="<?php echo $closingsession;?>"  />
        </table>
		<script language="javascript">
		function movesale()
		{
			options	={	
			url : 'newcounteract.php',
			type: 'POST',
			success: movecounter
		}
		jQuery('#counterform').ajaxSubmit(options,function(){return false});
		}
		function movecounter(text)
		{
			
			if(text!='')
			{
				notice(text,'',5000);	
			}
			else
			{
				refreshnav();
			}
		}
		</script>
  </form>