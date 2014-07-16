<?php
/*************************************exportcsv()****************************************/
//Who/When: Waqar 8 Oct 2010
//@return: CSV Export file
// $arr		 	=	data array
// $headcols	=	array of column names
// $filename	=	file name to export optional
// $type		=	xls or csv optional

function exportcsv($arr,$headcols,$fields,$filename='export',$type='csv')
{
	//print_r($fields);
	if($type=='csv')
	{
		$separator	=	",";
		header("Content-type: application/csv");
	}
	else
	{
		$separator	=	"\t";
		header("Content-Type: application/vnd.ms-excel"); 
	}
	//echo $type;
	header("Content-Disposition:\"inline; filename=$filename.$type \"");
	//$heads	=	implode("\"".$separator."\"",$headcols);
	for($l=0;$l<count($headcols);$l++)
	{
		$head=$headcols[$l];
		if($head!='ID' && $head!='')
		{
			$str123[]	=	trim(html_entity_decode(html_entity_decode($head)));
		}
	}
	$newstr123	=	implode("\"".$separator."\"",$str123);
	$heads		=	"\"".$newstr123."\"\r\n";	
	echo $heads;
	for($i=0;$i<sizeof($arr);$i++)
	{
		$str	=	array();
		$arr2	=	$arr[$i][$fields[$i]];
		//foreach($arr2 as $res)
		for($b=0;$b<count($fields);$b++)
		{
			if($fields[$b]!='' && $headcols[$b]!='ID')
			{
				$val=	$arr[$i][$fields[$b]];
				$str[]	=	trim(html_entity_decode(html_entity_decode($val)));
			}
		}
		$newstr	=	implode("\"".$separator."\"",$str);
		$endstr	=	"\"".$newstr."\"\r\n";
		echo $endstr;
		//print $arr[$i][$fields[$i]].'<br>';
	}
}
?>