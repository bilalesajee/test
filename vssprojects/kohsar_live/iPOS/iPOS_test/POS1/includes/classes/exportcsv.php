<?php
/*************************************exportcsv()****************************************/
//Who/When: Waqar 8 Oct 2010
//@return: CSV Export file
// $arr		 	=	data array
// $headcols	=	array of column names
// $filename	=	file name to export optional
// $type		=	xls or csv optional
function exportcsv($arr,$headcols,$filename='export',$type='csv')
{
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
	header("Content-Disposition: \"inline; filename=$filename.$type\"");
	$heads	=	implode("\"".$separator."\"",$headcols);
	$heads	=	"\"".$heads."\"";
	//header
	echo $heads."\r\n";
	//data
	for($i=0;$i<sizeof($arr);$i++)
	{
		$str	=	array();
		foreach($arr[$i] as $res)
		{
			$str[]	=	trim(html_entity_decode(html_entity_decode($res)));
		}
		$newstr	=	implode("\"".$separator."\"",$str);
		$endstr	=	"\"".$newstr."\"\r\n";
		echo $endstr;
	}
}
?>