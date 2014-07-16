<?php
require_once("valid.php");
class form
{
	var $error	=	0;
	var $msg	=	"";
	function submitform($fields,$data,$table)
	{
		if(count($fields)!= count($data))
		{
			$this->msg		.=	"Fields and Data arrays don't have equal number of elements.";
			$this->error	=	1;
			//return//fields and data array are not equal in length
		}
		else
		{
			$v	=	new Validator();
			foreach($fields as $f)
			{
				list($label,$field,$checks)	=	explode(".",$f);
				$v->validate($label,$_REQUEST[$field],$checks);
			}//for
			if($v->error > 0)
			{
				$this->msg		=	$v->msg;
				$this->error	=	1;
			}//if
		}//else
		/***************************************INSERT DATA INTO TABLES*******************************************/
		if($this->error == 0)
		{
			for($i=0;$i<count($fields);$i++)
			{
				
				$res	=	explode(".",$fields[$i]);
				$field	=	$res[1];//only field name is required
				$pos 	= strpos($data[$i],",");
				if($pos===false)
				{
					$str	.= $data[$i]. "='". $_POST[$field]."',";
				}
				else
				{
					$var[$data[$i]]	=	$_POST[$field];
				}
			}//for
			$str	=	rtrim($str,",");
			$query	=	"INSERT INTO
									$table
								SET
									$str";
			//mysql_connect("localhost","root","");
			//mysql_select_db("form");
			if(!mysql_query($query))
			{
				$this->error	=	1;
				$this->msg		.=	"$query".mysql_error();					
			}//if
			else
			{
				$inserid	=	mysql_insert_id();
				//$q			=	"select * from $table LIMIT 1";
				//$result2	=	mysql_query($q) or die("CAn not run query...$q");
				
				/*$i 			=	0;
				while ($i <	mysql_num_fields($result2))
				{
					$meta = mysql_fetch_field($result2, $i);
					if (!$meta)
					{
						echo "No information available<br />\n";
   					}

					if($meta->primary_key == 1)
					{
						echo $primarykey	=	$meta->name;
					}
					$i++;
				}
				*/
				foreach($var as $k=>$v)
				{
					//print"$k...$v";
					list($f,$t,$fk)	=	explode(",",$k);
					if(is_array($v))
					{
						foreach($v as $v1)
						{
							$query	=	"INSERT INTO `$t` SET `$f` = '$v1', `$fk` = '$inserid'";
							if (!mysql_query($query))
							{
								$this->error	=	1;
								$this->msg		.=	"$query<br>".mysql_error();;
							}
						}//foreach
					}//if
					else
					{
						$query	=	"INSERT INTO `$t` SET `$f` = '$v1', `$fk` = '$inserid'";
							if (!mysql_query($query))
							{
								$this->error	=	1;
								$this->msg		.=	"$query<br>".mysql_error();;
							}
					}
				}//foreach
			}//else
		}//if
	}//submitform
}//class
/**************************************OLD SUBMIT*****************************
function submitform($fields,$data,$table)
{
	for($i=0;$i<count($fields);$i++)
	{
		$pos = strpos($data[$i],",");
		if($pos===false)
		{
			$str	.= $fields[$i]. "='". $_POST[$fields[$i]]."',";
		}
		else
		{
			$var[$data[$i]]	=	$_POST[$fields[$i]];
		}
	}
	$str	=	rtrim($str,",");
	echo $query	=	"INSERT INTO
								$table
							SET
								$str";
	print"<br>----------------<br>";
	mysql_connect("localhost","root","");
	mysql_select_db("form");
	mysql_query($query);
	$inserid	=	mysql_insert_id();

	foreach($var as $k=>$v)
	{
		list($f,$t)	=	explode(",",$k);
		
		foreach($v as $v1)
		{
			echo $query	=	"INSERT INTO `$t` SET `$f` = '$v1'";
			print"<br>";
		}
	}
}
*/?>