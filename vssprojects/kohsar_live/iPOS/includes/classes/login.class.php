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
				if(strstr($_SERVER['REQUEST_URI'],'/admin/')==true){//added if condition by ahsan 23/02/2012, 
					$_SESSION['admin_section']	= 	"Admin logged in";//set admin section access session
				}else{
					$_SESSION['pos_section']	= 	"Pos logged in";//set POS level access session
				}
				$_SESSION['groupid']		=	$userdetails[0]['fkgroupid'];
				//linecommented in store_login.class.php, whereas commented in login.class.php. edit by ahsan 15/02/2012
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
	function history($ip,$logintime,$addressbookid,$logtype)
	{
	//	return;
			//$fields			=	array('fkaddressbookid','logintime','ipaddress');
			//$data			=	array($addressbookid,$logintime,$ip);
			
			 $Brw_info = serialize($_SERVER['HTTP_USER_AGENT']);
			 $reff_link = serialize($_SERVER["HTTP_REFERER"]);
		
			 	$fields			=	array('fkaddressbookid','logintime','ipaddress','referrallink','browserinfo','logtype','username','loc','loc_counter','sess_id');
			 $data			=	array($addressbookid,$logintime,$ip,$reff_link,$Brw_info,$logtype,$_SESSION['name'],3,$_SESSION['countername'],urlencode(session_id()));
				$insertid		=	$this->loginDAO->insertrow("main_kohsar.loginhistory",$fields,$data);
			
			return $insertid;
	}
	function lastLogin($addressbookid)
	{
		//comments added by ahsan 22/02/2012
		//if /admin/ is not in the REQUEST_URI, it means the file is accessed from POS.
		//$_SESSION['siteconfig']!=1 //store
		/*//add comment by ahsan 24/02/2012//if((strstr($_SERVER['REQUEST_URI'],'/admin/')==false) || ($_SESSION['siteconfig']!=1)){//edit by ahsan 22/02/2012
			$historylogs	=	$this->loginDAO->getrows("$dbname_detail.loginhistory","FROM_UNIXTIME(logintime) as logintime","fkaddressbookid = '$addressbookid'", "logintime", "DESC");
		}else{//run this code if $_SESSION['siteconfig']!=3, ie main*///add comment by ahsan 24/02/2012//
			$historylogs	=	$this->loginDAO->getrows("loginhistory","FROM_UNIXTIME(logintime) as logintime","fkaddressbookid = '$addressbookid'", "logintime", "DESC");
		//add comment by ahsan 24/02/2012//}//end edit
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
	function loginprocess($username, $password, $usertype , $logtype)
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
				
				$historyid				=	$this->history($ip,$logintime,$addressbookid,$logtype); //inserting login history
			    $_SESSION['historyid']	=	$historyid;  
				/*commented by jafer balti on 05-04-12
				//$historyid				=	$this->history($ip,$logintime,$addressbookid); //inserting login history
				//$lastlogintime			=	$this->lastlogin($addressbookid); //getting last login time
				//$_SESSION['lastlogin']	=	$lastlogintime;*/
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