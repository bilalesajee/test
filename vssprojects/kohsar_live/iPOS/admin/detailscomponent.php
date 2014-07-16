<?php

include_once("../includes/security/adminsecurity.php");
/********************* function grid ******************************/
/* accepts parameters
	1. $labels 		=	Field Labels 
	2. $fields 		=	Database Fields
	3. $query			=	Database Query
	4. $limit 		=	Paging Limit
	5. $navbtn 		=	Button Image or Text values are a = add; e = edit; d=delete; custom= custom value
	6. $jsrc		=	JavaScript file path(s)
	7. $dest		= 	Destination Page
	8. $div			= 	Div of the grid
	9. $css 		=	Grid CSS path
	10.	$form		=	Name of the form
*******************************************************************/
function detail($labels='',$fields='',$navbtn='',$jsrc='',$dest='', $div='', $css='', $breadcrumb='')//added , $breadcrumb='' from main, edit by ahsan 17/02/2012
{
	global $AdminDAO;

	 //echo $_SERVER['QUERY_STRING'];
	//****************search***********************
	$id				=	$_GET['id'];
	$count					=	count($fieldsdata);
	$fieldsarray =	$data_array;
	// Checking Array Size for Fields and Labels
	$labelsize	=	sizeof($labels);
	$fieldsize	= 	sizeof($fields);
	if($labelsize!=$fieldsize)
	{
		echo "Label count does not match with database Fields.";
		exit;
	}//end if
	
	// Including CSS
		//echo $css;
	// Including JavaScript Files	
		//echo $jsrc;
	// Including Page Navigation	
		//echo $pagelinks;
	?>
	<!-- building table... -->
	<?php 
		if(sizeof($fields) == 0)
		{
			
			$msg="<li>Sorry! But no record exists!</li>";
			//exit;	
		}
		if($msg!='')
		{
			print"<div class=notice id='gridnotice'>
			$msg
			<div align=right> 
				<a href=javascript:void(0) onclick=jQuery('#gridnotice').fadeOut()>
					<img src='../images/min.GIF' />
    			</a>
				</div>
			</div>
			<script language='javascript'>jQuery('#gridnotice').fadeOut(4000);</script> 
			";	
		}
	?>
      <div id="tbldetails">
      <div class="breadcrumbs" id="breadcrumbs33"><?php if($breadcrumb!='')echo $breadcrumb;?></div><?php //line added from main, by ahsan 17/02/2012?>
      
       <div style="margin-top:-22px;" align="right">
           <a href="javascript:void(0)" onclick="hidediv('tbldetails')"> <img src="../images/x.jpeg" /></a>
        </div>
      <br />  
	<table border="0" cellspacing="0" cellpadding="1" width="100%">
		<thead>
		<tr height="20" valign="middle">
			<td width="896" colspan="5" align="left" style="padding-left:15px" class="navbar" valign="middle">
			<?php 
				echo $navbtn;
			?>	
			
			</td>
		</tr>
		<thead>
	</table>
<div class="topimage" style="height:6px;"><!-- --></div>
	<table width="100%" border="0" cellspacing="0" cellpadding="1" >
		<thead>
					
					
					<th  align="left" colspan="3">
							Details
					</th>
	
			
				</tr>
			</thead>
			<?php
				/**************** table data ******************/
			for($j=0;$j<count($fields);$j++)
			{
				if($j%2==0)
				{
					 $class="even";
				}
				else
				{
					 $class="odd";
				}
			
			?>
					<tr id="tr"  class="<?php echo $class;?>">
					
					
						<td>
							<?php 
							 	echo $labels[$j];
							?>
						</td>
						<td>
							<?php echo $fields[$j];?>
						</td>
									</tr>
			<?php	
				}//outer for
				/**************** end table data **************/
			?>	
	</table>
	<table border="0" cellspacing="0" cellpadding="1" width="100%">
		<thead>
		<tr height="20" valign="middle">
			<td width="896" colspan="5" align="left" style="padding-left:15px" class="navbar">
			<?php 
			echo $navbtn; 
			?>	
			
			</td>
		</tr>
		<thead>
	</table>
<div id="subsection"></div>
</div>
<?php
}//end function grid
?>