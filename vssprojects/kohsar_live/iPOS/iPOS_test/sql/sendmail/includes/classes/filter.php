<?php
/*************************************filter()****************************************/
	//@params: NONE
	//Who/When: Umar / Waqar 18 Jan 2009
	//@return: MYSQL resultset having filtered data
	function filter($input)
	{
		return(htmlentities(addslashes(trim($input," ")),ENT_QUOTES));
	}//end of filter
	function numbers($num,$pr=2)
	{
		$number	=	round($num,$pr);
		return number_format($number,$pr);
	}
?>