<?php
session_start();
ini_set("display_errors",0);
include_once("includes/security/adminsecurity.php");
/********************* function grid ******************************/


function printgrid($title)
{
		//$navbtn='';
	global $AdminDAO;
	//echo $dbname_detail;
	// $_SESSION['qstring']	= $_SERVER['QUERY_STRING'];
	//****************search***********************
	//echo $query;
	/*****************************************************************************************/
	$labels	=	$_SESSION['print_labels'];
	$fields	=	$_SESSION['print_fields'];
	$query	=	$_SESSION['sql_query'];
	$fieldsarray =	$AdminDAO->queryresult($query);
	/***************************************PAGING STUFF**************************************
	
	
	$totalrows		=	$AdminDAO->queryresult('SELECT FOUND_ROWS() as totalrows');
	
	/****************************************************************************************/
	

	//$pagelinks="Next Page";
	/************** Paging End *****************/
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
	<br />
	<?php 
		if(sizeof($fieldsarray) == 0)
		{
			print"<script language=javascript>notice('Sorry! But no record exists.',0,5000);</script>";
		}//print_r($labels);
	?>
    <style>
	table, td
	{
		border-color: #000;
		border-style: solid;
	}
	table
	{
		font-family:Verdana, Geneva, sans-serif;
		font-size:11px;
		border-width: 0 0 1px 1px;
		border-spacing: 0;
		border-collapse: collapse;
	}
	
	td
	{
		margin: 0;
		padding: 4px;
		border-width: 1px 1px 0 0;
	}

	</style>
	<table border="0" cellspacing="0" cellpadding="0" width="80%">
		<thead>
		<tr height="20" valign="middle" bgcolor="#FFFFFF" style="color:#000; font-weight:bold;">
			<td width="50%" align="left" style="padding-left:15px; border:1px solid #fff;">
			<?php 
				echo str_replace('_',' ',$title);
			?>
			</td>
            <td style="border:1px solid #fff;" align="right">
   			<?php 
			$totalrecords	=	sizeof($fieldsarray);
			 echo "Total Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;" 
			 ?>     
            </td>
		</tr>
		<thead>
	</table>
	
	<form name="<?php echo $form?>"  method="post" id="<?php echo $form?>">
	<table border="0" cellpadding="0" cellspacing="0">
			<thead>
					<td height="25" align="left" style="font-weight:bold;color:#fff;background-color:#000;">Serial #</td>
					<?php 
					for($i=0;$i<$labelsize;$i++)
					{
						if(trim($labels[$i]," ")!='ID')//hides ID field
						{
					?>
					
					<td  align="left" style="font-weight:bold;color:#fff;background-color:#000;">
						<?php 
							//Displaying the Grid heads
							echo $labels[$i];
							//changing sorting order and image
							
							//$img="";
						?>
						
						
					</td>
	
				<?php
					}//end of if
					else
					{
						$id_index	=	$i;
					}
				}//end for
				?>
				</tr>
			
			<?php
				
				/**************** table data ******************/
			for($j=0;$j<count($fieldsarray);$j++)
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
					<tr id="tr_<?php echo $fieldsarray[$j][$fields[$id_index]].'_'.$div;?>" onmousedown=
                    "highlight('<?php echo $fieldsarray[$j][$fields[$id_index]];?>','<?php print"$class"; ?>','row','<?php echo $div;?>')"  class="<?php echo $class;?>">
					<td><?php echo $j+1;?></td>
					<?php
					$findme   	= array('.jpg','.jpeg','.gif','.bmp','.png','.GIF','.JPG','.JPEG');//extentions to find in values
					
					for($k=0;$k<$fieldsize;$k++)
					{
						if($k != $id_index)
						{
							
/*						}
						if($k!='0')
						{
*/					?>
						<td>
							<?php 
							 	//$fields[$k];
								$strvalue	=	$fieldsarray[$j][$fields[$k]];
								
								for($a=0;$a<=count($findme);$a++)
								{
									$pos = strpos($strvalue, $findme[$a]);
									if($pos!=false)
									{
										?>
											<img src="../productimage/<?php echo $fieldsarray[$j][$fields[$k]];?>" height="48" width="48"/>
										<?php
										$fieldsarray[$j][$fields[$k]]='';
									}
									
								}
								
								if($fields[$k]=='comments' || $fields[$k]=='description')
								{
									$strlength	=	strlen($fieldsarray[$j][$fields[$k]]);
									echo $str	=	 substr($fieldsarray[$j][$fields[$k]],0,80);
									 $strcount	=	count($str);
								if($strlength>80)
								{
								?>
									... <a href="javascript: void(0)" title="Click to View Details" class="basic more" onclick="javascript: loaddetail('basicModalContent<?php echo $fieldsarray[$j][$fields[$id_index]];?><?php echo $div;?>')">View More</a>
							<?php
								}//end of strlength
							?>
								<!-- this div contains the full description of data for modal window -->
								<div id="basicModalContent<?php echo $fieldsarray[$j][$fields[$id_index]];?><?php echo $div;?>" style='display:none'>
									<span style="padding:20px;">
										<h3>
											&nbsp;Details
										</h3>
									<?php
										echo $fieldsarray[$j][$fields[$k]]; //details for comments and descrption in modal window
										
									?>
									</span>
								</div>
								<?php
								}
								else
								{
										$vals	=	$fieldsarray[$j][$fields[$k]];
										if(is_numeric($vals))
										{
											echo "<div align=right>$vals</div>";
										}
										else
										{
											$vals	=	trim($vals,' ');
											if($vals!="")
											{
												echo $vals;// data for general td
											}
											else
											{
												echo "&nbsp;";
											}
										}
										foreach($optionsarray as $optionname)
										{
										//	echo $optionname['barcode'];
											//echo $fieldsarray[$j][$fields[$k]].'=='.$optionname['barcode'].'<br>';
											if($fieldsarray[$j][$fields[$k]]==$optionname['barcode'])
											{
												
													//$optionname=array_unique($optionname);
													$optname[]=$optionname['attributeoptionname'];
													$optname	=	array_unique($optname);
											}
										}
										if($fields[$k]=='productattributeoptionname')//check for attribute option name wity productname
										{
											//print" $optname ";
											foreach($optname as $op)
											{
												echo rtrim($va	=	 '&nbsp;'.$op.',',',');
											}
											$optname='';
										}
										
								}
							?>
						</td>
					<?php
						}//end of if
					}//inner for
					?>
					
					</tr>
			<?php	
				}//outer for
				/**************** end table data **************/
			?>	
	</table>
</form>
    
<script language="javascript" type="text/javascript">
<?php
	$selected	=	"selected$div";
	print"var $selected='';";
	//print"alert($selected)";
?>
	window.print();
	//loading('Loading...');
</script>	

<?php
//echo $query;
//$_SESSION['qstring']=$qs.'&'.$sort_qs;
}//end function grid
$title	=	$_GET['title'];
printgrid($title);
?>