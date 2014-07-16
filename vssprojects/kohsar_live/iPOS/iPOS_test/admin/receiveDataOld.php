<?php

////////////////////////////////////////////////////////////////		  
$server = 'localhost';
$server_user = 'root';
$server_pwd = 'pak@apps';
////////////////////////////////////////////////////////////////
$rowstoupdate = array();

$dbh_server = new mysqli($server, $server_user, $server_pwd);

$tableArray = array('main.barcode');

foreach ($tableArray as $table)
{
    $tname = substr($table, strpos($table, '.') + 1);

    $query_ser_pch = "SELECT max(pk{$tname}id) maxid from $table ";

    $result = $dbh_server->query($query_ser_pch);
    $row = $result->fetch_assoc();

    $tables[$table] = (int) $row['maxid'];
}

$dataRequest = json_encode($tables);

$url = ('https://main.esajee.com/admin/sendData.php?dataRequest=' . $dataRequest);

$recDataE = file_get_contents($url);

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
        return "null";
    }
}

// prepare data
foreach ($recData as $table => $data)
{
    //

    $dname = substr($table, 0, strpos($table, '.'));

    $dbN = str_replace($dname, $dname . '_test', $table);

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


    $dbh_server->query($query);

    $query = '';
}
/*

  $list = array (
  array('aaa', 'bbb', 'ccc', 'dddd'),
  array('123', '456', '789'),
  array('"aaa"', '"bbb"')
  );

  $fp = fopen('file.csv', 'w');

  foreach ($list as $fields) {
  fputcsv($fp, $fields);
  }

  fclose($fp);
  exit();

  $url = ('http://localhost:100/kohsar/admin/example.csv');
  $path = 'example2.csv';

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $data = curl_exec($ch);

  curl_close($ch);

  file_put_contents($path, $data);

  exit();
 */
?>
