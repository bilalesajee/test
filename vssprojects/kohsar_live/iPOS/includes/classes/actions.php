<?php
	function printaction($title)
	{
		$btn	=	"&nbsp;|&nbsp;<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:printgrid('$title')\" title='Print'>
				<span class='printer'><b>Print</b></span>
			</a>&nbsp;";
	return $btn;
	}
?>