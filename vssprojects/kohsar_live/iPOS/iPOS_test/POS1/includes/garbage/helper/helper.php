<?php
class Helper
{
	public function dump($var,$exit=0)
	{
		print"<pre>";	
		print_r($var);
		print"</pre>";
		if($exit!=0)
		{
			exit;
		}
	}//dump
}//helper
?>