<?php

include_once("../includes/security/adminsecurity.php");

global $AdminDAO;

//print_r($AdminDAO);

$groupid	=	$_REQUEST['id'];

$groupscreen	=	array();

$groupfield		=	array();

$groupaction	=	array();

if($groupid !='-1')

{

	$group_row	=	$AdminDAO->getrows("groups","*"," pkgroupid = '$groupid' ");

	$groupname	=	$group_row[0]['groupname'];

//adding new group



	/**************************************GROUP SCREEN***************************************/

	$groupscreen_row	=	$AdminDAO->getrows("groupscreen","*"," fkgroupid = '$groupid' ");

	for($i=0;$i<sizeof($groupscreen_row);$i++)

	{

		$groupscreen[]		=	$groupscreen_row[$i]['fkscreenid'];

	}

	/**************************************GROUP FIELDS***************************************/

	$groupfield_row	=	$AdminDAO->getrows("groupfield","*"," fkgroupid = '$groupid' ");

	for($i=0;$i<sizeof($groupfield_row);$i++)

	{

		$groupfield[]		=	$groupfield_row[$i]['fkfieldid'];

	}

	/**************************************GROUP actions***************************************/

	$groupaction_row	=	$AdminDAO->getrows("groupaction","*"," fkgroupid = '$groupid' ");

	for($i=0;$i<sizeof($groupaction_row);$i++)

	{

		$groupaction[]		=	$groupaction_row[$i]['fkactionid'];

	}

	$groupscreen				=	array_unique($groupscreen);

	$_SESSION['groupfieldz']	=	$groupfield;

	$_SESSION['groupactionz']	=	$groupaction;

	$_SESSION['groupscreenz']	=	$groupscreen;

}

?>

<div id="groupsdiv">

<br />

<script language="javascript" src="../includes/js/common.js"></script>

<script language="javascript">

function selectcreen(id)

{

	var frm	=	document.groupsform;

	//frm.

}

function addform(id)

{

	//loading('Syetem is Saving The Data....');

	options	=	{	

					url : 'insertgroup.php?groupid='+id,

					type: 'POST',

					success: response

				}

	jQuery('#groupsform').ajaxSubmit(options);

}

	

function response(text)

{

		adminnotice(text,0,5000);

	<?php /*?>if(text=='')

	{

		//alert(text);

		loading('System is saving data...');

		jQuery('#maindiv').load('managegroup.php?'+'<?php echo $qstring?>');

		document.getElementById('error').innerHTML		=	'Data Saved Successfully.';	

	}

	else

	{

		document.getElementById('error').innerHTML		=	text;	

	}<?php */?>

//	document.getElementById('error').style.display	=	'block';

}

</script>

<div id="error" class="notice" style="display:none"></div>

<div id="usergroupdiv">

<form  name="groupsform" id="groupsform" style="width:920px;" class="form">

<fieldset>

<legend>

    <?php

	if($groupid==-1)

	{

    	print"Add";

	}

	else

	{

		print"Edit";

	}

    ?>

     Group

     <?php

	 	print": $groupname";

	 ?>

     </legend>

<?php

/*if(sizeof($screens_row) > 4)

{*/

    $cols	= 3;	

    $width	=	"33";

	$colspan	= 3; 

/*}

else

{

    $cols	= 2;

    $width	=	'50';

	$colspan	=	2;

}*/

?>

<div style="float:right">

<?php /*//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition?>

<span class="buttons">

    <button type="button" class="positive" onclick="addform('<?php echo $groupid;?>');">

        <img src="../images/tick.png" alt=""/> 

        <?php

        if($groupid=='-1')

        {

            print"Save";

        }

        else

        {

            print"Update";

        }

        ?>

    </button>

     <a href="javascript:void(0);" onclick="hidediv('usergroupdiv');" class="negative">

        <img src="../images/cross.png" alt=""/>

        Cancel

    </a>

  </span>

   <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012*///add comment by ahsan 24/02/2012// 

	   	 buttons("insertgroup.php?groupid=$groupid",'groupsform','maindiv','managegroups.php',$place=1,$formtype)

	 //end edit?>

</div>  

<table cellpadding="0" cellspacing="2" width="100%" >

	<tbody>

	<tr >

		<td width="<?php print"$width";?>%">Group Name: <?php //add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php //add comment by ahsan 24/02/2012// }//end edit?></td>

		<td <?php if($colspan==3){print"colspan=2";} ?>>

        	<input name="groupname" id="groupname" type="text" value="<?php print"$groupname";?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}">

        </td>

	</tr>

	<tr >

	  <td height="30px" colspan="<?php print"$colspan";?>">

      	

      </td>

	  </tr>

	<?php

	$modules	=	$AdminDAO->getrows("module","*"," status<>0");

	foreach($modules as $module)

	{

		$modulename	=	$module['modulename'];

		$moduleid	=	$module['pkmoduleid'];

		?>

        <tr>

        <td colspan="3" style="background-color:#9CCCEF;padding:5px; font-weight:bolder;"><?php echo $modulename;?></td>

        <?php

		$screens_row		=	$AdminDAO->getrows("screen","*"," fkmoduleid = '$moduleid' ORDER BY displayorder ");

		for($s=0; $s <sizeof ($screens_row) ; $s++)

		{

			$sid		=	$screens_row[$s]['pkscreenid'];

			if($class=='even')

			{

				$class	=	'odd';	

			}

			else

			{

				$class	= 	'even';	

			}

		?>

			<?php

			if(($s % $cols == 0))

			{

			?>

			<tr class="<?php print"$class";?>">

			<td width="<?php print"$width";?>%">

			<?php

			}//

			else

			{

			?>

				<td>

			<?php

					

			}

		   ?>

		   <B>

			

				<input  onclick="toggleitem('screendiv_<?php print"$sid";?>')"  name="screens[]" id="screen_<?php print"$sid";?>" value="<?php print"$sid";?>" <?php if(@in_array($sid,$groupscreen)){print"checked=\"checked\"";} ?> type="checkbox"/> 

			

			<?php

				echo $screens_row[$s]['screenname'];

				?>

	

				</B>

				<img src="../images/max.GIF" width="12" height="12"  onclick="toggleitem('screendiv_<?php print"$sid";?>')" id="screendiv_<?php print"$sid";?>_img"/>

				<div id="screendiv_<?php print"$sid";?>" style="display:none">

				<table  width="100%" cellpadding="0" cellspacing="2">

					<tr >

					   

						<td>

							 <table width="100%">

							  <tr>

								 <td colspan="2"><strong>Fields</strong></td>

							  </tr>

							<?php

						  

							$fields_row	=	$AdminDAO->getrows("field",'*'," 1 AND fkscreenid = '$sid' ");

							for($f=0;$f < sizeof($fields_row); $f++)

							{

								$fid	=	$fields_row[$f]['pkfieldid'];

							?>

							  

									<tr >

										<td width="5%">

											<input id="fields_<?php echo $sid.'_'.$fid;?>" name="fields_<?php echo $sid.'_'.$fid;?>" <?php if(in_array($fid,$groupfield)){print" CHECKED=CHECKED ";}?> value="1" type="checkbox" />

										</td>

										<td >

											<?php

												echo $fields_row[$f]['fieldlabel'];

											?>

										</td>

									</tr>

							  

						   <?php

							}//for fields

						   ?>

						   </table>

					  </td> 

					  <td width="50%">

							 <table width="100%">

							  <tr>

								 <td colspan="2"><strong>Actions</strong></td>

							  </tr>

							<?php

						  //  $sid	=	$screens_row[$s]['pkscreenid'];

							$actions_row	=	$AdminDAO->getrows("action",'*'," 1 AND fkscreenid = '$sid' ");

							for($a=0;$a < sizeof($actions_row); $a++)

							{

								$aid	=	$actions_row[$a]['pkactionid'];

							?>

							  

									<tr >

										<td width="5%">

											<input id="actions_<?php echo $sid.'_'.$aid;?>" name="actions_<?php echo $sid.'_'.$aid;?>" <?php if(in_array($aid,$groupaction)){print" CHECKED=CHECKED ";}?>  value="1" type="checkbox" />

										</td>

										<td >

											<?php

												echo $actions_row[$a]['actionlabel'];

											?>

										</td>

									</tr>

							  

						   <?php

							}//for actions

						   ?>

						   </table>

					  </td> 

					</tr> 

				</table>

		  </div>

		   

		 <?php

			}//for screen

	}//foreach

	 ?>

	<tr >

	  <td  colspan="<?php print"$colspan"; ?>" align="center">

<!--            <input type="button" id='save' value="Save" onClick="addform('<?php //echo $groupid;?>')">

            <input name="cancel" id='cancel' type="button" value="Cancel" onclick="hidediv('groupsdiv')"/>-->

            <?php /*//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>

            <div class="buttons">

            <button type="button" class="positive" onclick="addform('<?php echo $groupid;?>');">

                <img src="../images/tick.png" alt=""/> 

                <?php

				if($groupid=='-1')

				{

					print"Save";

				}

				else

				{

					print"Update";

				}

				?>

            </button>

             <a href="javascript:void(0);" onclick="hidediv('usergroupdiv');" class="negative">

                <img src="../images/cross.png" alt=""/>

                Cancel

            </a>

          </div>

		   <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012*///add comment by ahsan 24/02/2012// 

	   		 buttons('insertgroup.php?groupid=$groupid','groupsform','maindiv','managegroups.php',$place=0,$formtype)

	 		?>

        </td>				

	  </tr>

	</tbody>

</table>

</fieldset>	

</form>

</div>

</div>

<?php //add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>

<script language="javascript">

	focusfield('groupname');

</script>

<?php //add comment by ahsan 24/02/2012// } //end edit?>