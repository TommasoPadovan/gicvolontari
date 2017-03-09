<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 07/03/2017
 * Time: 21:05
 */
require_once('lib/generalLayout.php');
require_once('lib/sqlLib.php');
require_once('lib/datetime/month.php');
require_once('lib/permissionString.php');

$db = new DbConnection();


function content(DbConnection $db) {
    $aux = <<<EOF
	<h1>Turni</h1>
EOF;
    $aux.= generateTable($db);
    return $aux;
}


function generateTable(DbConnection $db) {
    $yearMonths = $db->query("SELECT DISTINCT year, month FROM calendar");
    $allMonths=array();
    foreach ($yearMonths as $row)
        array_push($allMonths, new Month( intval($row['month']), intval($row['year']) ));

    $now = new Month(date('m'), date("Y"));



    return "";
}