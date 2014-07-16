<?php
session_start();
/* Login Class */
/* Params : User, Password, Type */
class Login
{
	var $loginDAO	=	"";
	function Login($dao)
	{
		$this->loginDAO	=	$dao;
	}
	function userlogin($user,$pass,$type)
	{
		$userdata	=	$this->loginDAO->getrows("addressbook","*","username = '$user' AND password = '$pass'");
		$addressbookid	=	$userdata[0]['pkaddressbookid'];
		if(sizeof($userdata)>0)
		{
			$userdetails	=	$this->loginDAO->getrows("$type","*"," fkaddressbookid = '$addressbookid'");
			if($userdetails[0][$type."deleted"] == 1)
			{
				$response	=	"3"; //employee deleted
			}
			else if($userdetails[0]['loginallowed'] == 1)
			{
				$response	=	"4"; //employee blocked
			}
			else
			{
				$response					=	"1";
				$_SESSION['addressbookid']	=	$addressbookid;
				$_SESSION['name']			=	$userdata[0]['firstname']." ".$userdata[0]['lastname'];
				$_SESSION['groupid']		=	$userdetails[0]['fkgroupid'];
				//$_SESSION['storeid']		=	$userdetails[0]['fkstoreid'];
				$groups						=	$this->loginDAO->getrows("groups","*","pkgroupid = '$_SESSION[groupid]'");
				$_SESSION['groupname']		=	$groups[0]['groupname'];
			}
		}
		else
		{
			$response	=	"2"; //invalid username or password
		}
	return $response;
	}// end of function login
	function history($ip,$logintime,$addressbookid)
	{
			global $dbname_main;
			$fields			=	array('fkaddressbookid','logintime','ipaddress');
			$data			=	array($addressbookid,$logintime,$ip);
			$insertid		=	$this->loginDAO->insertrow("$dbname_main.loginhistory",$fields,$data);
			return $insertid;
	}
	function lastLogin($addressbookid)
	{
		$historylogs	=	$this->loginDAO->getrows("$dbname_main.loginhistory","FROM_UNIXTIME(logintime) as logintime","fkaddressbookid = '$addressbookid'", "logintime", "DESC");
		if(sizeof($historylogs)>0)
		{
			foreach($historylogs as $historylog)
			{
				$logintime[]	=	$historylog['logintime'];
			}
			return $logintime[1];
		}
		else
		{
			return 0;
		}
		
	}
	function getscreens($groupid)
	{
		$groupscreens	=	$this->loginDAO->getrows("groupscreen","*","fkgroupid = '$groupid'");
		if(sizeof($groupscreens)>0)
		{
			foreach($groupscreens	as $groupscreen)
			{
				$screenid[] =	$groupscreen['fkscreenid'];
			}
			return $screenid;
		}
		else
		{
			return 0;
		}
	}
	function userRights($screen,$groupid)//getting screen rights
	{
		$fields	=	$this->loginDAO->getrows("groupfield gf, field f","*"," f.fkscreenid = '$screen' AND gf.fkgroupid = '$groupid' AND f.pkfieldid = gf.fkfieldid","f.pkfieldid","ASC");
		for($i=0;$i<sizeof($fields);$i++)
		{
			$label[]	=	$fields[$i]['fieldlabel'];
			$field[]	=	$fields[$i]['fieldname'];
		}
		$actions	=	$this->loginDAO->getrows("groupaction ga, action a","*"," a.fkscreenid = '$screen' AND ga.fkgroupid = '$groupid' AND a.pkactionid = ga.fkactionid");
		for($i=0;$i<sizeof($actions);$i++)
		{
			$action[]	=	$actions[$i]['fkactionid'];
		}
		$fieldslabels['fields']		=	@array_unique($field);
		$fieldslabels['labels']		=	@array_unique($label);
		$fieldslabels['actions']	=	@array_unique($action);
		return $fieldslabels;
	}
	function loginprocess($username, $password, $usertype)
	{
		if($username == "" || $password == "")
		{
			return 2;
		}
		else
		{
			$result	=	$this->userlogin($username, $password, $usertype); //user authentication
			if($result == 1)
			{
				$ip						=	$_SERVER['REMOTE_ADDR'];
				$logintime				=	time();
				$addressbookid			=	$_SESSION['addressbookid'];
				$historyid				=	$this->history($ip,$logintime,$addressbookid); //inserting login history
				$lastlogintime			=	$this->lastlogin($addressbookid); //getting last login time
				$_SESSION['lastlogin']	=	$lastlogintime;
				$userscreens			=	$this->getscreens($_SESSION['groupid']);//fetching user screens
				$_SESSION['screenids']	=	$userscreens;
				for($s=0;$s<sizeof($userscreens);$s++)
				{
					$userscreenpriviliges	=	$this->userRights($userscreens[$s],$_SESSION['groupid']);
					$_SESSION['screens'][$userscreens[$s]]	=	$userscreenpriviliges;
				}
				
				return $result;
			}
			else
			{
				return $result;
			}
		}
	}// end of function loginManager
}// end of class Login
?>