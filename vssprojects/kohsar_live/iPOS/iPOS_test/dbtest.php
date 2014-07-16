<?php

require 'includes/security/adminsecurity.php';

$query = " SHOW VARIABLES LIKE '%cache%'; ";
$queryrs = $AdminDAO->queryresult($query);

echo '<pre>';
print_r($queryrs);
echo '</pre>';
?>
