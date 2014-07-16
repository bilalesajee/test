<?php

ini_set("display_errors",0);

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

	11. $type		=	

	12. $optionsarray	=	

	13. $sortorder	=	Field name and field order e.g. brandname DESC

************ Custom work for this project**********************

	11. limit -1 hides the paging navigation

	12. $optionsarray if this is set then it will diplay the attributeoption names with productname on the basis of productattributeoptionname field name

	13. if some data have ('.jpg','.jpeg','.gif','.bmp','.png','.GIF','.JPG','.JPEG') image extentions this will add the image src tag and displays the image 

	14. if finds the field name with (description or comments) will add the view more option and enables the lightbox control to view details

*******************************************************************/

if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

	function advanceserach($searchOper,$searchField,$searchString,$compound)

	{

			//print"I am in advance search call";

			//print"$searchField --- $searchOper ----$searchString <br>";

			//$searchf	=	$searchField;

			

			//echo '<br> found: '.$searchOper;

			//break;

			

			

			//$searchOper)

			//{

				

				if($searchOper=='bw')

				{

					return(" LIKE '$searchString". "%' ");

					//return($searchOper);

					//break;

				}

				if($searchOper=='eq')

				{

					

					return(" = '$searchString'");

					//return($searchOper);

				//	break;

				}

				if($searchOper=='neq') 

				{

					return(" <> '$searchString'");

					//return($searchOper);

					//break;

				}

				if($searchOper=='lt') 

				{

					return(" < '$searchString'");

					//return($searchOper);

					//break;

				}

				if($searchOper=='le') 

				{

					return(" <= '$searchString'");

					//return($searchOper);

					break;

				}

				if($searchOper=='gt') 

				{

					return(" > '$searchString'");

					//return($searchOper);

					//break;

				}

				if($searchOper=='ge') 

				{

					return(" >= '$searchString'");

					//return($searchOper);

					//break;

				}

				if($searchOper=='ew') 

				{

					return("LIKE '%"."$searchString'");

					//return($searchOper);

					//break;

				}

				if($searchOper=='cn') 

				{

					

					$searchq		=	strip_tags($searchString);

					$list			=	explode(' ',$searchq);

					

					foreach($list as $val)// preparing the search condition

					{

						$condition.="%$val%";

					}

					$condition	=	str_replace('%%','%',$condition);

					return(" LIKE '$condition' ");

					//return($searchOper);

					//break;

				}

				//print"I am here";

			//}

	}// end of advancesearch

}//end edit

function grid($labels='',$fields='',$query='',$limit=10,$navbtn='',$jsrc='',$dest='', $div='', $css='', $form='frm1',$type='', $optionsarray='',$sortorder='',$tablename='',$totals='',$nm='')

{

	//start code add from main, by ahsan 17/02/2012

	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

		$replacestr	=	array("manage",".php");

		$printtitle	=	strtoupper(str_replace($replacestr,'',$dest));

		$navbtn.=printaction($printtitle);

		$navbtn.="&nbsp;|&nbsp;

	

		<a href='exportexcel.php?filename=$printtitle' title='Export To Excel'>

	

			<img src='../images/file_excel.png' height='14' width='16' border='0'>

	

		</a>

	

		";

	}

	//end code add



	global $AdminDAO,$Paging,$qs,$totals;

	//change for local copy only

	$gtotals='';

	/*if(!true){//for main //commented by ahsan 17/02/2012

		$nav2=$navbtn;

		if($form!='frmstock' && $form!='frm1demand')

		{

			$navbtn='';

		}

		if($navbtn=='' && $form=='stockdetails' || $form=='frmclosing' || $form=='frmcreditors' || $form=='frmquotes' || $form=='quoteitemfrm' || $form=='collectionfrm' || $form=='frmsinvoice' || $form=='manageusersfrm1' || $form=='frmcreditinvoices' || $form== 'frmconsignment' || $form== 'frm1cutomers' || $form=='returns' || $form=='itemsfrm' || $form== 'frmshipmentorder')

		{

			$navbtn=$nav2;

		}

	}//edit by Ahsan on 08/02/2012*/

	

	//echo $dbname_detail;

	// $_SESSION['qstring']	= $_SERVER['QUERY_STRING'];

	//****************search***********************



	$searchString	=	trim(stripslashes(filter($_REQUEST['searchString'])));

	$searchField 	=	$_REQUEST['searchField'];

	$search 		=	$_REQUEST['_search'];

	$searchOper	 	=	$_REQUEST['searchOper'];

	$searchoperator	=	$_REQUEST['searchOper'];
	 $loc	=	$_REQUEST['loc'];
	 $filtr	=	$_REQUEST['ctap'];
  
	$page			=	$_GET['page'];

	if(strpos($query,$_GET['field'])!=false )

	{

		$sort_index		=	$_GET['field'];

		if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

			$sort_index2		=	$sort_index;

		}//end edit

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

		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition

				 $qs	=	"&searchField=$searchField&_search=$search&searchOper=$searchOper";

		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

				$qs		=	"_search=true&searchField=$searchField&searchOper=$searchOper";

				//&searchString=$searchString

		}//end edit

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

	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

		if(($_REQUEST['totalsearchfields'])>0)

		{

			$it=0;

			for($adsr=1;$adsr<=$_REQUEST['totalsearchfields'];$adsr++)

			{

				$searchField	=	$_REQUEST['searchField'.$adsr];

				$searchOper		=	$_REQUEST['searchOper'.$adsr];

				$searchString	=	trim(trim($_REQUEST['searchString'.$adsr],','),'');

				$compound		=	$_REQUEST['compound'.$adsr];

				$condition='';

				$cond='';

			

				if($searchField!='' && $searchString!='')

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

										$cond.="%$val%";

									}

									$condition	=	str_replace('%%','%',$cond);

									$searchOper	=	" LIKE '$condition' ";

									break;

								}

							}//end of switch

						

							if($searchField!='')

							{

								$condition	=	"  $searchField $searchOper ";

								

							}

							//}

							if($condition!='' && $it==0)

							{

								 $query	.= " HAVING ".$condition;

							

							}

							else 

							{

								 $query.=" $compound $condition";

							}

							$it++;

				}

			}

		}

	}//end edit

	

	if($sort_index!='' && $sort_order!='')

	{

		if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

			if($sort_index=='addtime')			

			$sort_index	=	'addtimesort';

			else if($sort_index=='deadline')			

			$sort_index	=	'deadlinesort';

	

			$pos = strpos($sort_index,'date');

			if($pos === false) 

			{}else 

			{

				if($sort_index=='startdate' || $sort_index=='sortstartdate')

				$sort_index	=	'sortstartdate';

				else if($sort_index=='enddate' || $sort_index=='sortenddate')

				$sort_index	=	'sortenddate';

				else

				$sort_index	=	'sortingdate';

			}

		}//end edit

		$sort		=	" ORDER BY $sort_index $sort_order ";

		$sort_qs	=	"&field=$sort_index&order=$sort_order";

	}

	else

	{

		

		if($sortorder!='' )

		{

			$sort	=	"ORDER BY ".$sortorder; // takes field name and field order e.g. brandname DESC

		}

		else

		{

			//$sort=" ORDER BY 1 DESC";

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
 //$loc	=	$_REQUEST['loc'];
//	 $filtr	=	$_REQUEST['ctap'];
	if($loc!=''){
$qs="loc=$loc&ctap=$filtr";
	
	}	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition

		if($Paging->TotalResults > $Paging->ResultsPerPage)

		{ 

			//$pagelinks = $Paging->pageHTML('javascript: call_ajax_paging("'.$qs.$sort_qs.'&page=~~i~~","'.$dest.'","'.$div.'")');

			$pagelinks = $Paging->pageHTML('javascript: call_ajax_paging("'.$qs.$sort_qs.'&page=~~i~~","'.$dest.'","'.$div.'","'.$searchString.'")');

		}

	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

		$pagelinks = $Paging->pageHTML('javascript: call_ajax_paging("'.$qs.$sort_qs.'&page=~~i~~","'.$dest.'","'.$div.'","'.$searchString.'")'); 

	}//end edit

	if($pagelinks)

	{

		$pagelinks	=	"$pagelinks";

	}

	//$pagelinks="Next Page";

	/************** Paging End *****************/

	// Checking Array Size for Fields and Labels

	$labelsize	=	sizeof($labels);

	$fieldsize	= 	sizeof($fields);

	$hiddenfields	=	explode(',',$_COOKIE['datafields'.$printtitle]);//line added from main, by ahsan 17/02/2012

	

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

	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012

		$showhidecheck="<ul class='chkboxlist'>";

		

		for($i=0;$i<count($labels);$i++)

		{

			

			//if(trim($labels[$i]," ")!='ID')//hides ID field

			//{

				$val=$i+1;

				

				$showhidecheck.="<li>";

				if(!in_array($fields[$i],$hiddenfields))

				{

					$checked='checked="checked"';

				}

				else

				{

					$checked="";

				}

				

				$showhidecheck.='<input type="checkbox" name="'.$fields[$i].'" id="'.$fields[$i].'" value="'.$val.'" title="Uncheck to hide '.$labels[$i].' column." onclick="togglecol(this.id,this.checked)" '.$checked.' class="fieldchk"/>'.$labels[$i].'</li>';

				

			//}

		}

		$showhidecheck.="</ul>";

	}//end edit

	?>

	<!-- building table... -->

	<br />

	<?php 

		if(sizeof($fieldsarray) == 0)

		{

			

			$msg="<li>Sorry! But no record exists!</li>";

			//exit;	

		}

		if($msg!='')

		{

			print"<div class=notice id='gridnotice'>

			$msg

			<div align=right> 

				<a href=javascript:void(0) onclick=jQuery('#gridnotice').hide()>

					<img src='../images/min.GIF' />

    			</a>

				</div>

			</div>

			<script language='javascript'>jQuery('#gridnotice').fadeOut(4000);</script> 

			";	

		}

	?>

	<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>

		<style>

        

        .thover

        {

            background-color:#9AC2DA;

        }

        .thout

        {

            background-color:#0099FF;

        }

            .chkboxlist{list-style:none;}

        </style>

        <script language="javascript">

        function hidecoulmn(colid)

        {

            

            //$('input:checkbox[name='+colid+']').each(function () 

            //{ 

                //alert(colid);

                //this.checked = false; 

                if(colid!='')

                {

                    $('#gridtable<?php echo $form.''.$div;?>').toggleColumns([+$('#'+colid).val()]); 

                }

            //});

        }

        function showcheckbox(chosen,objectID)

        {

            if(chosen == "thout") 

            {

                document.getElementById(objectID).className="";

                document.getElementById(objectID+'span').style.display="none";

            }

            else 

            {

                document.getElementById(objectID).className="thover";

                document.getElementById(objectID+'span').style.display="block";

            }

        }

        function showfields(listid)

        {

            if(document.getElementById(''+listid+'chk').style.display=="none")

            {

                document.getElementById(''+listid+'chk').style.display="block";

            }

            else

            {

            

                document.getElementById(''+listid+'chk').style.display="none";

            }

            

        }

         

            var fields='';

            function togglecol(id,ev)

            {

                //selectSelecterChange(id); 

                if(ev==false)

                {

                    //alert(ev);//this will uncheck

                    //this blocks hides the columns and saves its values in cookies

                    $('input:checkbox[name='+id+']:checked').each(function () 

                    { 

                        

                        this.checked = false; 

                        

                    });

                    //fieldchk

                    var chks	=	document.getElementsByClassName('fieldchk');

                    for(var i=0;i<chks.length;i++)

                    {

                        if(chks[i].checked==false)//if unchecked

                        {

                            //alert(fields);

                            if(fields.search(chks[i].name)=='-1')//if not found in string

                            {

                                fields+=chks[i].name+',';

                            }

                        }

                    }

                }

                else

                {

                    $('input:checkbox[name='+id+']').each(function () {

                    this.checked = true;

                    });

                

                    var chks	=	document.getElementsByClassName('fieldchk');

                    for(var i=0;i<chks.length;i++)

                    {

                        if(chks[i].checked==true)//if checked

                        {

                            //alert(fields);

                            if(fields.search(chks[i].name)!='-1')//if found in string

                            {

                                fields	=	fields.replace(chks[i].name+',', "");//add to field string with comma

                                

                                

                            }

                        }

                    }

                }

        

                if(document.getElementById('th'+id).style.display=='block')

                {

                    document.getElementById('th'+id).style.display=='none';

                }

                else

                {

                    document.getElementById('th'+id).style.display=='block';

                }

                var jaf = [+$('#'+id).val()];

                jaf++;

                $('#gridtable<?php echo $form.''.$div;?>').toggleColumns(jaf); 

                $('#dummyfieldsdiv').load('dbgridfieldscookie.php?field='+fields+'&screen=<?php echo $printtitle;?>');

                

                

            }

        </script>

            <div style="display:block" id="dummyfieldsdiv"></div>

    <?php }//end edit?>

	<div id="subsection"></div>

    <?php

		if($div!='maindiv')

		{

		?>

        <div style="margin-top:-22px; margin-right:4px;margin-bottom:4px" align="right">

           <a href="javascript:void(0)" onclick="hidediv('<?php echo $div;?>')"> <img src="../images/x.jpeg" /></a>

        </div>	     

		<?php

		}

		?>

    <div class="select-bar">

		<form name="searchform<?php echo $div;?>" id="searchform<?php echo $div;?>" method="get" class="form">

			<table width="85%">

				<tr>

					<td width="6%">

						<select name="searchField<?php echo $div;?>"  id="searchField<?php echo $div;?>" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')">

						<?php

						

						for($i=0;$i<count($labels);$i++)

						{

							if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition

								if(trim($labels[$i]," ")!='ID')//hides ID field

								{

									if(trim($labels[$i]," ")!='Picture')//hides Picture field

									{	

							?> 

								

							  <option value="<?php echo $fields[$i];?>" <?php if($fields[$i]==$searchField){print"selected";}?>><?php echo $labels[$i]; ?></option>

							<?php

									}

								}//end of if

							}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012

								/*if(trim($labels[$i]," ")!='ID')//hides ID field

								{*/

									 if(trim($labels[$i]," ")!='Picture')//hides Picture field

									{	

							?> 

								

							  <option value="<?php echo $fields[$i];?>" <?php if($fields[$i]==$searchField){print"selected";}?>><?php echo $labels[$i]; ?></option>

							<?php

									}

								//}//end of if

							}//end edit

						}

						?>

						</select>

					</td>

					<td width="14%">

						<select name="searchOper<?php echo $div;?>"  id="searchOper<?php echo $div;?>" style="width:100px;" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')">

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

					<td width="17%">

						<?php $searchString = stripslashes($searchString);//line added from main, by ahsan 17/02/2012?>



						<input name="searchString<?php echo $div;?>" type="text"  value="<?php echo rawurldecode($searchString);?>"id="searchString<?php echo $div;?>" size="20" maxlength="100" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')" onfocus="this.select()"/>

					</td>

					<td width="63%">

						<input type="hidden" name="param" value="<?php echo $_GET['param'];?>" id="param"/>

						<input type="hidden" name="id<?php echo $div;?>" value="<?php echo $id;?>" id="id<?php echo $div;?>"/>

						

                        <span class="buttons">

                            <button type="button" class="positive" onclick="searchgrid('<?php print $div;?>','<?php print $dest;?>')"><img src="../images/find.png" alt=""/>Find</button>

                        </span>

						<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>

                           <span class="buttons">

                              <button type="button" class="positive" onclick="document.getElementById('advancesearch<?php echo $div;?>').style.display='block'"><img src="../images/find.png" alt=""/> Advance Search</button>

                          </span>	

                        <?php } //end edit?>

					</td>

				</tr>

			</table>

		</form>

        

    </div>

 

 	<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>

	<h3></h3>

	 <div id="advancesearch<?php echo $div;?>" style="display:<?php if($_REQUEST['totalsearchfields']<1 || $_REQUEST['filter']=='filter'){print'none';}else{print'block';}?>">

	 <fieldset>

	 <legend>Advance Search</legend>

			

			<form name="advancesearchform<?php echo $div;?>" id="advancesearchform<?php echo $div;?>" method="get" class="form">

			<?php

			//advance search code

			for($fl=1;$fl<sizeof($fields);$fl++)

			{

			?>

			

			<table width="85%">

				<tr>

					<td width="6%">

						<select name="searchField<?php echo $fl;?>"  id="searchField<?php echo $fl;?>" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')">

						<?php

						

						for($i=0;$i<count($labels);$i++)

						{

							/*if(trim($labels[$i]," ")!='ID')//hides ID field

							{*/

								if(trim($labels[$i]," ")!='Picture')//hides Picture field

								{	

						?> 

						 	

						  <option value="<?php echo $fields[$i];?>" <?php if($fields[$i]==$_REQUEST['searchField'.$fl]){print"selected";}?>><?php echo $labels[$i]; ?></option>

						<?php

								}

							//}//end of if

						}

						?>

						</select>

					</td>

					<td width="14%">

						<select name="searchOper<?php echo $fl;?>"  id="searchOper<?php echo $fl;?>" style="width:100px;" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')">

						  <option value="bw" <?php  if($_REQUEST['searchOper'.$fl]=='bw'){print"selected";}?>>begins with</option>

						  <option value="eq" <?php  if($_REQUEST['searchOper'.$fl]=='eq'){print"selected";}?>>equal</option>

						  <option value="ne" <?php  if($_REQUEST['searchOper'.$fl]=='ne'){print"selected";}?>>not equal</option>

						  <option value="lt" <?php  if($_REQUEST['searchOper'.$fl]=='lt'){print"selected";}?>>less</option>

						  <option value="le" <?php  if($_REQUEST['searchOper'.$fl]=='le'){print"selected";}?>>less or equal</option>

						  <option value="gt" <?php  if($_REQUEST['searchOper'.$fl]=='gt'){print"selected";}?>>greater</option>

						  <option value="ge" <?php  if($_REQUEST['searchOper'.$fl]=='ge'){print"selected";}?>>greater or equal</option>

						  <option value="ew" <?php  if($_REQUEST['searchOper'.$fl]=='ew'){print"selected";}?>>ends with</option>

						  <option value="cn" <?php  if($_REQUEST['searchOper'.$fl]=='cn' || $_REQUEST['searchOper'.$fl]==''){print"selected";}?>>contains</option>

						</select>

					</td>

					<td width="17%">

						

						<input name="searchString<?php echo $fl;?>" type="text"  value="<?php echo trim($_REQUEST['searchString'.$fl],',');?>" id="searchString<?php echo $fl;?>" size="20" maxlength="100" onkeydown="javascript:if(event.keycode==13){advancesearchgrid('<?php print $div;?>','<?php print $dest;?>','advancesearchform<?php echo $div;?>'); return false;}" onfocus="this.select()"/>

						

					</td>

					<td width="63%">

					<?php

				//print_r($_GET);

				if(sizeof($fields)>1 && $fl<(sizeof($fields)-1)) 

				{

					//echo $_REQUEST['compound'.$fl+1].'---';

					$compname=$fl+1;

				?>

						<input type="radio" id="compound<?php echo $fl+1;?>" name="compound<?php echo $compname;?>" value="AND" <?php  if($_REQUEST['compound'.$compname]==''){print"checked";}?>>AND

						<input type="radio" id="compound<?php echo $fl+1;?>" name="compound<?php echo $compname;?>" value="OR" <?php  if($_REQUEST['compound'.$compname]=='OR'){print"checked";}?> >OR

				<?php

					}	

                ?>        

					</td>

				</tr>

				

				

			</table>

		

			<?php

			//}//end of for advance search fields */

			?>

			</table>

		

			<?php

			}//end of for advance search fields 

			

			?>

			<span class="buttons">

                            <button type="button" class="positive" onclick="advancesearchgrid('<?php print $div;?>','<?php print $dest;?>','advancesearchform<?php echo $div;?>')"><img src="../images/find.png" alt=""/>Search</button>

              </span>

			    <span class="buttons">

                          <button type="button" class="positive" onclick="resetsearchform('<?php print $div;?>','<?php print $dest;?>')"><img src="" alt=""/>Reset</button>

              </span>	

			  <span class="buttons">

                            <button type="button" class="positive" onclick="document.getElementById('advancesearch<?php echo $div;?>').style.display='none'"><img src="../images/cross.png" alt=""/>Cancel</button>

              </span>

			<input type="hidden" id="totalsearchfields" name="totalsearchfields" value="<?php echo sizeof($fields)-1;?>" />

				<input type="hidden" name="param" value="<?php echo $_GET['param'];?>" id="param"/>

						<input type="hidden" name="id<?php echo $div;?>" value="<?php echo $id;?>" id="id<?php echo $div;?>"/>

			</form>

	   </fieldset>

	<br />

	</div>

	

	<?php

		//advanced data filters section added by Rizwan 03-12-2010

		global $filter;

		if(sizeof($filter)>0)

		{

		?>

		 <fieldset>

	 	<legend>Advanced Data Filters</legend>

		<form name="advancedatafilterfrm<?php echo $div;?>" id="advancedatafilterfrm<?php echo $div;?>" style="display:block">

		<table width="100%" align="left" >

			<tr>

				<?php 

				$compname=0;

				//for($g=0;$g<count($labels);$g++)

			//	{

				

				?>

				<td valign="middle">

					<?php

					$ab=1;

					//echo $_REQUEST['searchString'.$ab];

					

					 for($fl=0;$fl<sizeof($filter);$fl++)

					 {

						 $tablefilter		=	$filter[$fl][0];

						 $labelfilter		=	$filter[$fl][1];

						 $fieldfilter		=	$filter[$fl][2];

						 $aliasfilter		=	$filter[$fl][3];

						

						

						if($labelfilter)

						{

							$sqlfilter="select $fieldfilter from $tablefilter ";

							//$filterarray	=	$AdminDAO->queryresult($sqlfilter);	

							if($aliasfilter!='')

							{

								$fieldfilter=$aliasfilter;	

							}

						echo "<b>". $labelfilter."</b>";	

							$selectedindextext	=	 $fl+1;

					?>

					<input type="hidden" name="searchFieldName<?php echo $fl+1;?>" id="searchFieldName<?php echo $fl+1;?>" value="<?php echo $fieldfilter;?>" />

					<input type="text" name="searchFieldFilter<?php echo $fl+1;?>"  id="searchFieldFilter<?php echo $fl+1;?>"  value="<?php echo trim($_REQUEST['searchString'.$selectedindextext],',');?>" class="searchfieldfiltertextbox"/>

					<script language="javascript">

						 maketoken('searchFieldFilter<?php echo $fl+1;?>','tokenizerresult.php?qry=<?php echo $sqlfilter;?>&field=<?php echo $fieldfilter;?>','horizental','s');

					</script>

					<?php /*?><select name="searchFieldFilter<?php echo $fl+1;?>"  id="searchFieldFilter<?php echo $fl+1;?>"  style="width:130px;">

						<option value=""  >Select <?php echo $labelfilter;?></option>

							<?php

							//$fieldsarray[$j][$fields[$k]];

							for($flt=0;$flt<count($filterarray);$flt++)

							{

								$selstr=$fl+1;

							?> 

							  <option value="<?php echo htmlentities($filterarray[$flt][$fieldfilter],ENT_QUOTES);?>" <?php if($filterarray[$flt][$fieldfilter]==$_REQUEST['searchString'.$selstr]){print"selected";}?> ><?php  echo $filterarray[$flt][$fieldfilter];?></option>

							<?php

							}//for

							?>

							</select><?php */?>

								<?php

								

								if($fl<(sizeof($filter)-1))

								{

									print"</td><td>";

									if($fl==0)

									{

										 $compname=$fl+2;

									}

									else

									{

										 $compname=$fl+2;

									}

									//echo $_REQUEST['compound'.$compname];

								?>

									<input type="hidden" name="searchOperFilter<?php echo $fl+1;?>"  id="searchOperFilter<?php echo $fl+1;?>" value="cn">

									<input type="radio" id="compoundFilter<?php echo $fl+1;?>" name="compoundFilter<?php echo $compname;?>" value="AND" <?php  if($_REQUEST['compound'.$compname]=='' || $_REQUEST['compound'.$compname]=='AND'){print"checked";}?>>AND

									<input type="radio" id="compoundFilter<?php echo $fl+1;?>" name="compoundFilter<?php echo $compname;?>" value="OR" <?php  if($_REQUEST['compound'.$compname]=='OR'){print"checked";}?> >OR

							

							<?php

								}//if

							}//if

							else

							{

								print"&nbsp;";

							}//else

						

						}//if

							?>

					</td>

				<?php

				//}//for

				?>

				<td>

						<span class="buttons">

                            <button type="button" class="positive" onclick="advancesearchgrid('<?php print $div;?>','<?php print $dest;?>','advancedatafilterfrm<?php echo $div;?>','filter')"><img src="../images/find.png" alt=""/>Search</button>

              </span>

				</td>

			</tr>

		</table>

		

		<input type="hidden" id="totalsearchfieldsfilter" name="totalsearchfieldsfilter" value="<?php echo sizeof($filter);?>" />

		<input type="hidden" name="param" value="<?php echo $_GET['param'];?>" id="param"/>

		<?php /*?><input type="hidden" name="id<?php echo $div;?>" value="<?php echo $id;?>" id="id<?php echo $div;?>"/><?php */?>

		</form>

		</fieldset>

		<?php

		}//advanced data filters section ended

		?>

		<br />   

  <?php }//end edit?> 

	<table border="0" cellspacing="0" cellpadding="1" width="100%">

		<thead>

		<tr height="20" valign="middle">

			<td width="60%" align="left" style="padding-left:15px" class="navbar">

			<?php 

                    $totalrecords	=	$totalrows[0]['totalrows'];
              
			echo $navbtn; 

			?>			

            <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>

                <td align="right" class="navbar">

                    <?php 

                 if($limit!='-1'){echo "Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks;} ?>      

                </td>

            <?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>

                <span style="float:right">

                <?php /*if($limit!='-1'){echo "Total Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks;}*/ ?>            

                    <?php

                    if($limit!='-1')

                    {

                        //echo $totalrecords/$page;

                        if($pagelinks=='' && $totalrecords>10)

                        {

                            $pagelinks="<a href=javascript:resetpaging('$dest'); style='color:red;font-weight:bold'>Reset Page Mode</a>";

                        }

                        

                        echo "Total: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks;

                    } 

                    ?>

                    </span>

             <?php }//end edit?>

         </td>

		</tr>

		<thead>

	</table>

	<div id="loading" class="loading" style="display:none">Loading...</div>



	<form name="<?php echo $form?>"  method="post" id="<?php echo $form?>">

<div class="topimage" style="height:6px;"><!-- --></div>

	<table width="100%" border="0" cellspacing="0" cellpadding="1" <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>

id="gridtable<?php echo $form.''.$div;?>" <?php }//end edit?>>

			<thead>

	  <th height="25" align="left" style="display: table-cell;"><?php //added stryle attribute from main, by ahsan 17/02/2012?>

						<input type="checkbox" name="chkAll" value="checkbox" id="chkAll" onClick="checkAll(this,document.<?php echo $form?>.checks,'<?php echo $div;?>')"/>			

		</th>

					<?php 

					for($i=0;$i<$labelsize;$i++)

					{

						if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition

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

								?>

								<a href="javascript: void(0)" onclick="Javascript: call_ajax_sort('<?php echo $fields[$i];?>','<?php echo $sorder;?>','<?php echo $qs.'&page='.$page;?>','<?php echo $dest;?>','<?php echo $div;?>')">

									<img src="<?php echo IMGPATH.$img;?>" width="8" height="10" hspace="2" border="0" />

								</a>

								

							</th>

			

						<?php

							}//end of if

							else

							{

								$id_index	=	$i;

							}

					}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

						//	if(trim($labels[$i]," ")!='ID')//hides ID field

							//{

						?>

						

						<th  align="left" style="display: table-cell;" onmouseover="showcheckbox('thover', this.id)" onmouseout="showcheckbox('thout',this.id)" id="th<?php echo $fields[$i];?>">
							<?php 

								//Displaying the Grid heads

								echo $labels[$i];

								//changing sorting order and image

								if($sort_index2== $fields[$i] && $sort_order=='asc')

								{

									$sorder='desc';

									$img="active_sortdown.gif";

								}

								else

								{

									$sorder='asc';

									$img="active_sortup.gif";

								}

							?>

							<a href="javascript: void(0)" onclick="Javascript: call_ajax_sort('<?php echo $fields[$i];?>','<?php echo $sorder;?>','<?php echo $qs.'&page='.$page;?>','<?php echo $dest;?>','<?php echo $div;?>')">

								<img src="<?php echo IMGPATH.$img;?>" width="8" height="10" hspace="2" border="0" />						</a>

								<span id="th<?php echo $fields[$i];?>span" style="display:none; float:left; margin:2px;">

									<a href="javascript:void(0)" onmouseover="showfields('<?php echo $fields[$i];?>')">

									<img src="<?php echo IMGPATH;?>sq_down.png" width="12" height="12" border="0"/>

									</a>

									<span id="<?php echo $fields[$i];?>chk" style="display:block; position:absolute; background:#99CCFF; border:solid; border-color:#9AC2DA;">

										<?php echo $showhidecheck;?>

									</span>	

								</span>

												

							</th>

								

					<?php

							

							

						/*}//end of if

						else

						{*/

							$id_index	=	0;

						//}

					}//end edit

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

					 $sortedcolor = "#FFFFF4";

				}

				else

				{

					 $class="odd";

					 $sortedcolor = "#FFFFEA";

				}

				if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

					// for posted orders

					if($form=='countrylistfrm')

					{

						$slistid	=	$fieldsarray[$j][$fields[$id_index]];

						//echo $fieldsarray[$j][$fields[$k]];

						//fetching quantity and purchased quantity

						$slistquery	=	"SELECT s.quantity as qty1,sum(sd.quantity) as qty2 FROM shiplist s LEFT JOIN shiplistdetails sd ON fkshiplistid=pkshiplistid WHERE pkshiplistid='$slistid'";

						//echo $slistquery;

						$slistdata	=	$AdminDAO->queryresult($slistquery);

						$qty1		=	$slistdata[0]['qty1'];

						$qty2		=	$slistdata[0]['qty2'];

						if($qty2<$qty1)

						{

							$class	=	"pending";

						}

						else if($qty2==$qty1)

						{

							$class	=	"purchased";

						}

						else

						{

							$class	=	"morepurchased";

						}

					}//end posted orders				

				}//end edit

			?>

					<tr id="tr_<?php echo $fieldsarray[$j][$fields[$id_index]].'_'.$div;?>" onmousedown=

                    "highlight('<?php echo $fieldsarray[$j][$fields[$id_index]];?>','<?php print"$class"; ?>','row','<?php echo $div;?>')"  class="<?php echo $class;?>">

					<?php

						//if($sort_index == $fieldsarray[$j])

						

					?>

                    <td>

						<input onClick="highlight('<?php echo $fieldsarray[$j][$fields[$id_index]];?>','<?php print"$class"; ?>','chk','<?php echo $div;?>')" type="checkbox" name="checks" id="cb_<?php echo $fieldsarray[$j][$fields[$id_index]].'_'.$div;?>" value="<?php echo $fieldsarray[$j][$fields[$id_index]];?>"/>			

					</td>

					<?php

					$findme   	= array('.jpg','.jpeg','.gif','.bmp','.png','.GIF','.JPG','.JPEG');//extentions to find in values

					

					for($k=0;$k<$fieldsize;$k++)

					{

						if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition

								if($k != $id_index)

								{

									

		/*						}

								if($k!='0')

								{

		*/					?>

								<td <?php if($sort_index == $fields[$k]){print"bgcolor=\"$sortedcolor\"";}?>>

									<?php 

										$strvalue	=	$fieldsarray[$j][$fields[$k]];

										for($a=0;$a<=count($findme);$a++)

										{

											$pos = strpos($strvalue, $findme[$a]);

											if($pos!=false)

											{

												?>

													<img src="../productimage/<?php echo $fieldsarray[$j][$fields[$k]];?>" height="24" width="24"/>

												<?php

											}

										}

										

										if($fields[$k]=='defaultimage' &&  $fieldsarray[$j][$fields[$k]]=='')

										{

											?>

											<img src="../images/noimage.jpg" height="24" width="24"/>

											<?php

											$fieldsarray[$j][$fields[$k]]='';

										}

										if($fields[$k]=='comments' || $fields[$k]=='description')

										{

											$strlength	=	strlen($fieldsarray[$j][$fields[$k]]);

											echo $str	=	 substr($fieldsarray[$j][$fields[$k]],0,40);

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

												if($fields[$k]!='defaultimage' )

												{

													

													echo $fieldsarray[$j][$fields[$k]];// data for general td

													//echo $fields[$k];

													//print_r($totals);

													if(in_array($fields[$k],$totals))

													{

														//print"found<br>";

														$gtotals[$fields[$k]]+= $fieldsarray[$j][$fields[$k]];

													}

												}

										}

									?>

								</td>

							<?php

								}//end of if

						}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

								//	if($k != $id_index)

									//{

										

			/*						}

									if($k!='0')

									{

			*/					?>

									<td <?php if($sort_index2 == $fields[$k]){print"bgcolor=\"$sortedcolor\"";}?>>

										<?php 

											$strvalue	=	$fieldsarray[$j][$fields[$k]];

											for($a=0;$a<=count($findme);$a++)

											{

												$pos = strpos($strvalue, $findme[$a]);

												if($pos!=false)

												{

													?>

														<img src="../productimage/<?php echo $fieldsarray[$j][$fields[$k]];?>" height="24" width="24"/>

													<?php

												}

											}

											

											if($fields[$k]=='defaultimage' &&  $fieldsarray[$j][$fields[$k]]=='')

											{

												?>

												<img src="../images/noimage.jpg" height="24" width="24"/>

												<?php

												$fieldsarray[$j][$fields[$k]]='';

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

													if($fields[$k]!='defaultimage' )

													{

														echo $fieldsarray[$j][$fields[$k]];// data for general td

													}

											}

										?>

									</td>

								<?php

									//}//end of if

						}

					}//inner for

					?>

					

					</tr>

			<?php	

				}//outer for

				/**************** end table data **************/

			?>

			<?php

			if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition

				//this row will be incl`uded when $totals array have field names to sum up. This row shows the sums of the columns specified in the totals array.

				if(sizeof($totals)>0)

				{

				?>

				

					<tr height="20" valign="middle" bgcolor="#BFCEF7">

					 <?php

					 for($k=0;$k<$fieldsize;$k++)

					 {

						

					 ?>

						<td  align="left" title="Page Total of <?php echo $labels[$k];?>"><strong><?php echo $gtotals[$fields[$k]];?></strong></td>

						

						<?php

					}//for

						?>

				  </tr>

				<?php

				}//end of if $totals

			}//end edit

		?>	

	</table>

	<table border="0" cellspacing="0" cellpadding="1" width="100%">

		<thead>

		

		<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>

            <tr height="20" valign="middle">

                <td width="60%" align="left" style="padding-left:15px" class="navbar">

                <?php 

                echo $navbtn; 

                ?>			</td>

                <td  align="right" class="navbar">

                    <?php 

                    

                    if($limit!='-1'){echo "Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks;} ?>			</td>

            </tr>

         <?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>

            <tr height="20" valign="middle">

                        <td align="left" class="navbar" style="padding-left:5px;">

                        

                        <?php 

                        echo $navbtn; 

                        ?>

                        

                        <span style="float:right">

                        <?php /*if($limit!='-1'){echo "Total Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks;}*/ 

                        $pagelinks2	=	str_replace("id=\"pagelimit\"","id=\"pagelimit2\"",$pagelinks);

                        ?>

                        

                        <?php

                            if($limit!='-1')

                            {

                                //echo $totalrecords/$page;

                                if($pagelinks=='' && $totalrecords>10)

                                {

                                    $pagelinks="<a href=javascript:resetpaging('$dest'); style='color:red;font-weight:bold'>Reset Page Mode</a>";

                                }

                                echo "Total Records: $totalrecords&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$pagelinks2;

                            } 

                            ?>            

                        </span>

                        </td>

                      </tr>

           <?php }//end edit?>

		<thead>

	</table>

	</form>

    

<script language="javascript" type="text/javascript">

<?php

	$selected	=	"selected$div";

	print"var $selected='';";

	//print"alert($selected)";

	if($search!='')

	{

		echo "\n document.getElementById('searchString".$div."').focus()"; 

		echo "\n document.getElementById('searchString".$div."').select()"; 

	}

?>



	loading('Loading...');

	<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

		for($hf=0;$hf<sizeof($hiddenfields);$hf++)

		{

		?>

			hidecoulmn('<?php echo $hiddenfields[$hf];?>');

		<?php

		}

		?>

	<?php }//end edit?>

</script>	



<?php

//$_SESSION['qstring']=$qs.'&'.$sort_qs;

	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012

		$_SESSION['print_labels']=$labels;

		$_SESSION['print_fields']=$fields;

		$_SESSION['sql_query']=$query;

	}//end edit

}//end function grid

?>

<div id="itemname" style="display:none;"></div>