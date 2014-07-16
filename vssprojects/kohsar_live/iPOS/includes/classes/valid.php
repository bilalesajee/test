<?php
//$email	=	"";
//$salary	=	"";
//$msg	= "Please make sure that:<br>";
//$v		=	new Validator();	

	//$v->validate(Label, fieldvalue,comma separated labels [e, em, n, r],[Minimum range, Maximum Range]);
	//$msg	.=	 $v->validate('User Email', $email, 'e,em');
	//$msg	.=	 $v->validate('Salary', $salary, 'e,n,r',100.00,150);
	
//	print "<font color='#FF0000'>$msg</font>";
//
class Validator
{
	public $msg	=	"<ul>";
	public $error	=	"";
	function validate($v)
	{
		//print_r($v);
	//	exit;
		foreach ($v as $x)
		{
			list($fieldname,$fieldvalue,$checks)	=	explode(",",$x);
			//print"$fieldname,$fieldvalue,$checks<br>";
			$checks	=	explode('.',$checks);
			foreach($checks as $check)
			{//foreach
			
				$check	=	strtolower($check);
				
				switch(strtolower($check))//empty
				{
					case 'e':
					{
						if ($this->isempty($fieldvalue))
						{
							$this->msg		.=	"<li>'<b>$fieldname</b>' is not filled!</li>";
							$this->error	=	'1';
						}
						break;
					}//e
					case 'em':
					{
						if (!$this->isemail($fieldvalue))
						{
							//print"I here....<br>";
							$this->msg	.=	"<li>'<b>$fieldname</b>' does not contain a valid Email!</li>";
							$this->error	=	'1';
						}
						break;		
					}
					case 'n'://number
					{
						if (!$this->isnumber($fieldvalue))
						{
							$this->msg	.=	"<li>'<b>$fieldname</b>' does not contain numeric values only!</li>";
							$this->error	=	'1';
						}
						break;		
					}
					/*
					case 'r'://in range
					{
						if (!$this->isinrange($fieldvalue, $min,$max))
						{
							$this->msg	.=	"<li>'$fieldname' is not between $min and $max!</li>";
							$this->error	=	'1';
						}
						break;		
					}
					*/
					default:
					{
						//$this->msg	.=	"<li>All is correct</li>";
						$this->error	=	0;
						//return ('1');
					} 
				}//switch
			}//foreach
			//echo "$this->msg";
			//exit;
		}//foreach
		$msg	.= "</ul>";
		return ($this->error);
	}//validate
	function isemail($email) 
	{
		return (bool)eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}
	function isempty($txt)
	{
		$txt	=	trim($txt);//removes extra spaces from start and end
		if($txt =='')
		{
			return true;//yes it is empty
		}
		else
		{
			return false;//not empty
		}
	}
	function isnumber($num)
	{
		return (bool)is_numeric($num);
	}
	function isinrange($num, $min,$max)
	{
		return (($num >= $min &&  $num <= $max)?1:0);
	}
	/*function islong($txt,$len)
	{
		return ($len > strlen($txt) ? 0 : 1);
	}
	*/
}//class
?>