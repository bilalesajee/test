<?php
/*************************************filter()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL resultset having filtered data
	function filter($input)
	{
		return(htmlentities(addslashes(trim($input," ")),ENT_QUOTES));
	}//end of filter

	//start code from store_filter.php, add by ahsan 15/02/2012
	function fdate($date)
	{
		$date			=	date("Y-m-d",$date);
		return $date;
	}
	//end add code here

	function numbers($num,$pr=2)
	{
		$number	=	round($num,$pr);
//		return number_format($number,$pr);
		return $number;//add comment and new line by ahsan 3/22/2012
	}
?>