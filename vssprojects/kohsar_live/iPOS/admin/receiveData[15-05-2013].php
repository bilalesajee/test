<?php

include("config_autoget.php");
date_default_timezone_set('Asia/karachi');
set_time_limit(0);
include("logclass.php");
$log = new CLog('logfiles\log-'.date("m-d-Y-h-i", time()).'.csv',';');

$rowstoupdate = array();

$dbh_server = new mysqli($server, $server_user, $server_pwd);

if($dbh_server->connect_errno > 0){

	$log->error(' Db Not Connected',$dbh_server->connect_error);

}else{

	$log->info(' Db is Connected','Step1');	

	}
	
$tableArray = array('main.barcode','main.addressbook','main.consignment','main.consignmentdetail','main.product','main.currency', 'main.bank', 'main.cctype', 'main.employee','main.supplier');

	foreach ($tableArray as $table)
	{
    $tname = substr($table, strpos($table, '.') + 1);

    $query_ser_pch = "SELECT max(pk{$tname}id) maxid from $table ";

    
    
    
	if(!$result = $dbh_server->query($query_ser_pch)){

	$log->error('Error did Not Got Max Id of Tab '.$tname,$dbh_server->error);
 
    
	}else{
       
	   $row = $result->fetch_assoc();
	   $offset_closing=$row['maxid'];
       $log->info('Got  Max Id of TAb '.$tname.' : '.$offset_closing ,'Step2');
       $tables[$table] = (int) $row['maxid'];
	 
	}
	
	
   
    
}

    $dataRequest = json_encode($tables);

    $url = ('http://main.esajee.com/sendData.php?dataRequest=' . $dataRequest);

    $recDataE = file_get_contents($url);

    if($recDataE == ''){

	$log->error('Did not got any data from server','Step3');
 
    
	}else{
       
	     $log->info('Got data from server','Step3');
     
	}
	
	
	$recData = json_decode($recDataE, true);
	
	

    function quote(&$str)
{
    $str = trim($str);
    if ($str != '')
    {
        $str = str_replace("'", "''", ($str));
        return "'$str'";
    }
    else
    {
        return "''";
    }
}

// prepare data
foreach ($recData as $table => $data)
{
    
    $dname = substr($table, 0, strpos($table, '.'));

    $dbN = str_replace($dname, $dname . '', $table);

    $query = "INSERT INTO {$dbN} values ";

    foreach ($data as $key => $row)
    {

        $row = array_map('quote', $row);

        $query .= '(';
        $query .= implode(',', $row);
        $query .= ')';

        if (isset($data[$key + 1]))
        {
            $query .= ',';
        }
    }


   
if(!$result_insert=$dbh_server->query($query)){

	$log->error('No data Inserted ',$dbh_server->error);
 
    
	}else{
		
       $log->info('Data Inserted on server ','Step4');
       
	}
    $query = '';
}
?>
