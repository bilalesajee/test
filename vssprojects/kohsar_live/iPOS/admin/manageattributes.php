<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
//*************delete************************
$deltype	=	"attribute";
include_once("delete.php");

$id				=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
if($oper=='del')
{
	$id="";	
}

if($id=='')
{
	if($_REQUEST['param']!='undefined')
	{
		$id	=	$_REQUEST['param'];	
	}

}
//echo $id."pakkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk";
/************* DUMMY SET ***************/
$labels = array("ID","Attribute Name", "Display Position");
$fields = array("pkattributeid","attributename","attributeposition");
$dest 	= 	'manageattributes.php';
$div	=	'sugrid';
$form 	= 	"formatt";	
define(IMGPATH,'../images/');
if($id!='-1')
{
	
	//echo $id.'))))))))))';
	$from=" , productattribute  ";
	$where=" AND fkproductid='$id' AND fkattributeid=pkattributeid";
}
    $query 	= 	"SELECT 
				pkattributeid,
				attributename,
				attributeposition
			FROM
				attribute $from
			WHERE
				attributedeleted<>1
			
			$where
		
			
			";
$sortorder	=	"attributename ASC"; // takes field name and field order e.g. brandname DESC
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
	$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addattributes.php','susection','$div','$id')\" title='Add Attribute'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addattributes.php','susection','$div','$id') title=\"Edit Attribute\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$id') title=\"Delete Attributes\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
			<a href=\"javascript: showpage(1,document.$form.checks,'loadattributes.php','susection','$div')\" title=\"Manage Options\"><b>Options</b></a>&nbsp; |
			<a href=\"javascript: void(0) \" title=\"Arrange Attributes\" onclick=\"showdiv('deleteitemreason')\"><b>Arrange Attributes</b></a>&nbsp;
			";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addattributes.php','susection','$div','$id','$formtype')\" title='Add Attribute'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addattributes.php','susection','$div','$id','$formtype') title=\"Edit Attribute\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$id') title=\"Delete Attributes\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
			<a href=\"javascript: showpage(1,document.$form.checks,'loadattributes.php','susection','$div')\" title=\"Manage Options\"><b>Options</b></a>&nbsp; |
			<a href=\"javascript: void(0) \" title=\"Arrange Attributes\" onclick=\"showdiv('deleteitemreason')\"><b>Arrange Attributes</b></a>&nbsp;
			";
}//end edit
			
/********** END DUMMY SET ***************/


?><head>
<script type="text/javascript" src="../includes/js/jquery.tablednd_0_5.js"></script>
</head>
<div id="susection"></div>
<div id='<?php echo $div;?>'>
<div id="deleteitemreason" class="delreasonbox">
	
    
    
	  <form id="arrangeatt" name="arrangeatt" method="post" action="arrangeattributeposition.php">
	    <table width="297" align="center" style="border:none">
	      <tr>
	        <td><b> Arrange Attributes Order</b></td>
          </tr>
	      <tr>
	        <td align="center">
            <table width="286" border="0" bgcolor="#99CCCC" cellpadding="4" cellspacing="4" id="table-1">
	           <tr class="nodrag nodrop">
	            <th width="31">Position</th>
	            <th width="239">Attribute Name</th>
              </tr>
			  <?php
			  $attarray	=	$AdminDAO->getrows("attribute","*"," 1 order by attributeposition ASC");
			  for($i=0;$i<count($attarray);$i++)
			  {
			  ?>
              <tr>
	            <td width="31"><?php echo $attarray[$i]['attributeposition'];?>
                <input type="hidden" name="position[]" id="position[]" value="<?php echo $attarray[$i]['pkattributeid'];?>"/></td>
	            <td width="239"><?php echo $attarray[$i]['attributename'];?></td>
              </tr>
	          <?php
			  }
			  ?>
              
            </table>
            </td>
          </tr>
	      <tr>
	        <td align="center"><input type="button" name="button" id="button" value="Save" onclick="arrangeposition()"/>
&nbsp;
<input type="button" name="button2" id="button2" value="Cancel" onclick="javascript:jQuery('#deleteitemreason').fadeOut()"/></td>
          </tr>
        </table>
  </form>

</div>
<div class="breadcrumbs" id="breadcrumbs">Attributes</div>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?>
</div>
<br />
<br />

<script language="javascript">
function showdiv(divid)
{
	document.getElementById(divid).style.display='block';	
}

$(document).ready(function() {
    // Initialise the table
    $("#table-1").tableDnD();
});
function arrangeposition()
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'arrangeattributeposition.php',
					type: 'POST',
					success: response
				}
	jQuery('#arrangeatt').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Attributes postion has been arranged.',0,5000);
		jQuery('#sugrid').load('manageattributes.php?'+'<?php echo $qs?>');		
		jQuery('#deleteitemreason').fadeOut();
	}
	else
	{
		adminnotice(text,0,20000);	
	}
	//hideform();
}

</script>