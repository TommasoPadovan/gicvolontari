<?php
function connect() {
	$c=mysql_connect("127.0.0.1","root","");
	mysql_select_db("liltvolontari");
	return $c;
}

function queryThis($query, $conn) {
	$result=mysql_query($query,$conn)
		or die("Query Fallita ".mysql_error($conn));
	return $result;
}

function getUserName($id, $conn) {
	$result=queryThis("SELECT * FROM Users WHERE id='$id'",$conn);
	$row=mysql_fetch_assoc($result);
	return ($row['firstname'].' '.$row['lastname']);
}
?>