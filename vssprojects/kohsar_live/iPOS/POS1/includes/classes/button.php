<?php
class button
{
	//private variable $button_text
	var   $button_text = "";
	//private variable $label
	var $label;
	//private variable $url
	var $url;
	function makebutton($label,$url="#")
	{
		$this->button_text="<a class=\"ovalbutton\" href=\"$url\"><span>$label</span></a>";
		echo ($this->button_text);
	}
}
?>