<?php

$host = "cray";

$dbase = "s23_pradra01";

$user = "pradra01";

$pass = $user;

try {
	$db = new PDO("mysql:host=$host;dbname=$dbase", $user, $pass);
}
catch(PDOException $e){
	die("ERROR connecting to MYSQL ". $e->getMessage());
}



?>
