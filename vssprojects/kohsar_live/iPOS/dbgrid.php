<?php
session_start();
ini_set("display_errors",0);
include_once("includes/security/adminsecurity.php");
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
************ Custom work for this project**********************
	11. limit -1 hides the paging navigation
	12. $optionsarray if this is set then it will diplay the attributeoption names with productname on the basis of productattributeoptionname field name
	13. if some data have ('.jpg','.jpeg','.gif','.bmp','.png','.GIF','.JPG','.JPEG') image extentions this will add the image src tag and displays the image 
	14. if finds the field name with (description or comments) will add the view more option and enables the lightbox control to view details
*******************************************************************/
?>
<script language="javascript">
	function printgrid(title)
	{
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=600,left=100,top=25';
		window.open('printgrid.php?title='+title,display); 
	}

</script>
<?php
function grid($labels='',$fields='',$query='',$limit=10,$navbtn='',$jsrc='',$dest='', $div='', $css='', $form='frm1',$type='', $optionsarray='',$orderby='')
{
		//$navbtn='';
	global $AdminDAO,$Paging,$qs;
	//echo $dbname_detail;
	// $_SESSION['qstring']	= $_SERVER['QUERY_STRING'];
	//****************search***********************

	$searchString	=	trim(stripslashes(filter($_REQUEST['searchString'])));
	$searchField 	=	$_REQUEST['searchField'];
	$search 		=	$_REQUEST['_search'];
	$searchOper	 	=	$_REQUEST['searchOper'];
	$searchoperator	=	$_REQUEST['searchOper'];
	$page			=	$_GET['page'];
	if(strpos($query,$_GET['field'])!=false )
	{
		$sort_index		=	$_GET['field'];
		$sort_order		=	$_GET['order'];
	}
	$id				=	$_GET['id'];
	$param			=	$_REQUEST['param'];
	if($search=='true')
	{
		if($searchString=='')
		{
			$msg="<li>Please Enter the text to search.</li>";
		}
	}
	if($searchString != '')
	{
		 $qs	=	"&searchString=$searchString&searchField=$searchField&_search=$search&searchOper=$searchOper";
	}
	if ($id !="")
	{
		$qs	.="&id=$id";
	}
	if ($param != '')
	{
		$qs.="&param=$param";
	}
	$condition		=	"";
	if($search!='false')
	{
		switch($searchOper)
		{
			case 'bw': 
			{
				$searchOper	=	" LIKE '$searchString". "%' ";
				break;
			}
			case 'eq': 
			{
				$searchOper	=	" = '$searchString'";
				break;
			}
			case 'ne': 
			{
				$searchOper	=	" <> '$searchString'";
				break;
			}
			case 'lt': 
			{
				$searchOper	=	" < '$searchString'";
				break;
			}
			case 'le': 
			{
				$searchOper	=	" <= '$searchString'";
				break;
			}
			case 'gt': 
			{
				$searchOper	=	" > '$searchString'";
				break;
			}
			case 'ge': 
			{
				$searchOper	=	" >= '$searchString'";
				break;
			}
			case 'ew': 
			{
				$searchOper	=	"LIKE '%"."$searchString'";
				break;
			}
			case 'cn': 
			{
				
				$searchq		=	strip_tags($searchString);
				$list			=	explode(' ',$searchq);
				
				foreach($list as $val)// preparing the search condition
				{
					$condition.="%$val%";
				}
				$condition	=	str_replace('%%','%',$condition);
				$searchOper	=	" LIKE '$condition' ";
				break;
			}
		}
	
			if($searchField!='')
			{
				$condition	=	"  $searchField $searchOper ";
				
			}
		}
	if($condition!='')
	{
		$query	.= " HAVING ".$condition;
	}
	
	//echo $query;
	
	if($sort_index!='' && $sort_order!='')
	{
		$sort		=	" ORDER BY $sort_index $sort_order ";
		$sort_qs	=	"&field=$sort_index&order=$sort_order";
	}
	else
	{	
		
		if($orderby!='')
		{
			$sort	=	"ORDER BY ".$orderby; // takes field name and field order e.g. brandname DESC
		}
		else
		{
			//$sort=" ORDER BY 1 DESC";
			$sort=" ORDER BY $fields[2] ASC";
		}
	}
	
	
	/*if($form=='frmstock')
	{
		$sort="";
	}
	*/
	
	
	/************** Paging Start ****************/
	if($page=="")
	{
		$page=1;
	}
	$pagelimit				=	$_GET['pagelimit'];
	if($pagelimit)
	{
		if($_SESSION['pagelimit']!=$pagelimit)
		{
			$_SESSION['pagelimit']	=	$pagelimit;
			$Paging->ResultsPerPage =	$_SESSION['pagelimit'];
		}
		else
		{
			$Paging->ResultsPerPage =	$pagelimit;
		}
	}
	else if($_SESSION['pagelimit'])
	{
		$Paging->ResultsPerPage =	$_SESSION['pagelimit'];
	}
	else
	{
		$Paging->ResultsPerPage =	15; 
	}
	$Paging->LinksPerPage	=	5;

	$page  = $Paging->getCurrentPage();
	if($page > 1)
	{
		$start = ($page-1) * $Paging->ResultsPerPage;
	}
	else 
	{
		$start = 0;
	}
	$end   = $Paging->ResultsPerPage;
	
	$records=" LIMIT $start , $end ";
	$query.= " $sort $records ";
	/****************************MODIFY for getting Number of Results*************************/
	//$query	=	str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS',$query);
	// pattern, replacement, string, limit
	$query	= preg_replace('/SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query, 1); // outputs '123def abcdef 
	/*****************************************************************************************/
	$fieldsarray =	$AdminDAO->queryresult($query,$type);
	/***************************************PAGING STUFF**************************************
	if($searchString=='')
	{
		$fieldsdata =	$AdminDAO->queryresult($query,$type);
	//echo	$fieldsdata	=	$AdminDAO->getrowsunbuffred($query);
	//echo count($fieldsdata);
	}
	//$fieldsdata	=	$AdminDAO->getrowsunbuffred($query);
	//exit;
	
	
	$count					=	count($fieldsdata);
	*/
	
	$totalrows		=	$AdminDAO->queryresult('SELECT FOUND_ROWS() as totalrows');
	$Paging->TotalResults 	=	$totalrows[0][totalrows];
	/****************************************************************************************/
	
	
	if($Paging->TotalResults > $Paging->ResultsPerPage)
	{ 
		$pagelinks = $Paging->pageHTML('call_ajax_paging("'.$qs.$sort_qs.'&page=~~i~~","'.$dest.'","'.$div.'")');
	}
	if($pagelinks)
	{
		
		$pagelinks	=	"$pagelinks";
	}
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
	<div id="subsection"></div>
    <div class="select-bar">
		<form name="searchform<?php echo $div;?>" id="searchform<?php echo $div;?>" method="get">
			<table width="62%" id="noclass">
				<tr>
					<td width="9%">
						<select name="searchField<?php echo $div;?>"  id="searchField<?php echo $div;?>">
						<?php
						
						for($i=0;$i<count($labels);$i++)
						{
							if(trim($labels[$i]," ")!='ID')//hides ID field
							{
						?> 
						 	
						  <option value="<?php echo $fields[$i];?>" <?php if($fields[$i]==$searchField){print"selected";}?>><?php echo $labels[$i]; ?></option>
						<?php
							}//end of if
						}
						?>
						</select>
					</td>
					<td width="14%">
						<select name="searchOper<?php echo $div;?>"  id="searchOper<?php echo $div;?>">
						  <option value="bw" <?php  if($searchoperator=='bw'){print"selected";}?>>begins with</option>
						  <option value="eq" <?php  if($searchoperator=='eq'){print"selected";}?>>equal</option>
						  <option value="ne" <?php  if($searchoperator=='ne'){print"selected";}?>>not equal</option>
						  <option value="lt" <?php  if($searchoperator=='lt'){print"selected";}?>>less</option>
						  <option value="le" <?php  if($searchoperator=='le'){print"selected";}?>>less or equal</option>
						  <option value="gt" <?php  if($searchoperator=='gt'){print"selected";}?>>greater</option>
						  <option value="ge" <?php  if($searchoperator=='ge'){print"selected";}?>>greater or equal</option>
						  <option value="ew" <?php  if($searchoperator=='ew'){print"selected";}?>>ends with</option>
						  <option value="cn" <?php  if($searchoperator=='cn' || $searchoperator==''){print"selected";}?>>contains</option>
						</select>
					</td>
					<td width="13%">
						
						<input name="searchString<?php echo $div;?>" type="text"  value="<?php echo $searchString;?>"id="searchString<?php echo $div;?>" size="20" maxlength="100" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')" />
					</td>
					<td>
						<input type="hidden" name="id<?php echo $div;?>" value="<?php echo $id;?>" id="id<?php echo $div;?>"/>
                        <input type="hidden" name="param" value="<?php echo $_GET['param'];?>" id="param"/>
                        <span  class="buttons">
                        <button type="button" id="sbut" onclick="searchgrid('<?php print $div;?>','<?php print $dest;?>');">
                            <img src="images/find.png" alt=""/><span style="font-size:12px;">Find</span>
                        </button>
                        </span>
					</td>
				</tr>
			</table>
		</form>	 
	</div>
	<table border="0" cellspacing="0" cellpadding="1" width="100%">
		<thead>
		<tr height="20" valign="middle" bgcolor="#eee">
			<td width="72%" align="left" style="padding-left:15px; border:none;">
			<?php 
			echo $navbtn; 
			?>
			</td>
            <td style="border:none;" align="right">
   			<?php 
			$totalrecords	=	$totalrows[0]['totalrows'];
			 if($limit!='-1'){echo "Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks;} ?>     
            </td>
		</tr>
		<thead>
	</table>
	<div id="loading" class="loading" style="display:none">Loading...</div>
	<form name="<?php echo $form?>"  method="post" id="<?php echo $form?>">
	<table class="pos">
			<thead>
					<th height="25" align="left" >
						<input type="checkbox" name="chkAll" value="checkbox" id="chkAll" onClick="checkAll(this,document.<?php echo $form?>.checks,'<?php echo $div;?>')"/>			
					</th>
					<?php 
					for($i=0;$i<$labelsize;$i++)
					{
						if(trim($labels[$i]," ")!='ID')//hides ID field
						{
					?>
					
					<th  align="left">
						<?php 
							//Displaying the Grid heads
							echo $labels[$i];
							//changing sorting order and image
							if($sort_index== $fields[$i] && $sort_order=='asc')
							{
								$sorder='desc';
								$img="active_sortdown.gif";
							}
							else
							{
								$sorder='asc';
								$img="active_sortup.gif";
							}
							//$img="";
						?>
						<a href="javascript: void(0)" onclick="Javascript: call_ajax_sort('<?php echo $fields[$i];?>','<?php echo $sorder;?>','<?php echo $qs.'&page='.$page;?>','<?php echo $dest;?>','<?php echo $div;?>')">
							<img src="includes/images/<?php echo $img;?>" width="8" height="10" hspace="2" border="0" />
						</a>
						
					</th>
	
				<?php
					}//end of if
					else
					{
						$id_index	=	$i;
					}
				}//end for
				?>
				</tr>
			</thead>
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
					<td>
						<input onClick="highlight('<?php echo $fieldsarray[$j][$fields[$id_index]];?>','<?php print"$class"; ?>','chk','<?php echo $div;?>')" type="checkbox" name="checks" id="cb_<?php echo $fieldsarray[$j][$fields[$id_index]].'_'.$div;?>" value="<?php echo $fieldsarray[$j][$fields[$id_index]];?>"/>			
					</td>
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
											echo $vals;// data for general td
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
	<table border="0" cellspacing="0" cellpadding="1" width="100%">
		<thead>
		<tr height="20" valign="middle" bgcolor="#eee">
			<td width="72%" align="left" style="padding-left:15px; border:none;">
			<?php 
			echo $navbtn; 
			?>
			</td>
            <td style="border:none;" align="right">
   			<?php if($limit!='-1'){echo "Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks;} ?>     
            </td>
		</tr>
		<thead>
	</table>
	</form>
    
<script language="javascript" type="text/javascript">
<?php
	$selected	=	"selected$div";
	print"var $selected='';";
	//print"alert($selected)";
?>

	loading('Loading...');
</script>	

<?php
//echo $query;
//$_SESSION['qstring']=$qs.'&'.$sort_qs;
//setting paramaters for printing

$_SESSION['sql_query']=$query;
$_SESSION['print_labels']=$labels;
$_SESSION['print_fields']=$fields;
}//end function grid
?>