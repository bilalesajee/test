<?php

include("config_autoget.php");

$rowstoupdate = array();

$dbh_server = new mysqli($server, $server_user, $server_pwd);

//$tableArraye = array('main.barcode' => 0, 'main_kohsar.pricechange' => 0, 'main_kohsar.pricechangehistory' => 0 );
$tableArraye = $_GET['dataRequest'];

$tableArray = json_decode($tableArraye, true);

foreach($tableArray as $table => $pkid)
{
    $tname = substr($table, strpos($table, '.') + 1) ;
    
    $query_ser_pch = "SELECT * from $table where pk{$tname}id  >  $pkid  limit 30";
    
    $result = $dbh_server->query($query_ser_pch);

    while ($row = $result->fetch_assoc())
    {
        $data[$table][] = $row;
    }
}

echo json_encode($data);



//////////////////////////////////////////////////////////////////
?>
