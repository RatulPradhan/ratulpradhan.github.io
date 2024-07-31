<!DOCTYPE html>
<html>
<head>
	<title>TEST PHP - MySQL</title>
</head>
<body>

<h1>TEST PHP - MySQL</h1>
<?php

include_once("db_connect.php");

$str = "SELECT * FROM titan1";

$res = $db->query($str);

if ($res != FALSE) {
	printf("titan1 has %d rows and %d columns\n", 
	$res->rowCount(), $res->columnCount());

	while ($row = $res->fetch()) {
		$id = $row['id'];
		$name = $row['name'];

		printf("<P>$id $name</P>\n");
	}
}
else {
	printf("error with query?\n");
}

?>

</body>
</html>