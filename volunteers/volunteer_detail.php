<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 06/03/2017
 * Time: 11:49
 */

require_once('../lib/generalLayout.php');
require_once('../lib/permissionsMng.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');

$db = new DbConnection;

$user = $db->getUser($_GET['id']);


/**
 * /////////////////////////////////////
 * Count presenze turni serali
 * /////////////////////////////////////
 */
$statement = $db->prepare(<<<TAG
SELECT c.year AS year, c.month AS month, COUNT(c.day) as count
FROM turni AS t JOIN calendar AS c ON (t.day = c.id)
  JOIN users AS u ON (t.volunteer = u.id)
WHERE u.id = :id
GROUP BY c.year, c.month
ORDER BY c.year, c.month
TAG
);

$statement->execute([':id' => $_GET['id']]);

$allVolunteerTurns = $statement->fetchAll(PDO::FETCH_ASSOC);

$turnTableBody='';
foreach ($allVolunteerTurns as $row) {
    if ($row['year']==$_GET['year']) {
        $monthObj = new Month($row['month'], $row['year']);
        $turnCount = $row['count'];

        $turnTableBody .= "
            <tr>
                <td>{$monthObj->getYear()}</td>
                <td>{$monthObj->getMonthName()}</td>
                <td>$turnCount</td>
            </tr>
        ";
    }
}



/**
 * /////////////////////////////////////
 * Dettagli presenze turni serali
 * /////////////////////////////////////
 */

$allVolunteerTurnsDetail = $db->prepare(<<<TAG
SELECT c.year AS year, c.month AS month, c.day AS day, t.task AS task, t.position AS position
FROM turni AS t JOIN calendar AS c ON (t.day = c.id)
  JOIN users AS u ON (t.volunteer = u.id)
WHERE u.id = :id
ORDER BY c.year, c.month, c.day
TAG
);

$allVolunteerTurnsDetail->execute([':id' => $_GET['id']]);
$turnDetailTableBody = '';
foreach ($allVolunteerTurnsDetail as $row) {
    if ($row['year'] == $_GET['year']) {
        $monthObj = new Month($row['month'], $row['year']);

        $turnDetailTableBody .= "
            <tr>
                <td>{$row['day']} {$monthObj->getMonthName()} {$monthObj->getYear()}</td>
                <td>{$row['task']} posizione {$row['position']}</td>
            </tr>
        ";
    }
}


/**
 * /////////////////////////////////////
 * Dettagli presenze riunioni/eventi
 * /////////////////////////////////////
 */
$meetingsEventsDetail = $db->prepare(<<<QUERY
SELECT e.date AS date, e.type AS type, e.title AS title, e.location AS location, e.description AS description
FROM events AS e JOIN eventsattendants AS ea ON (e.id = ea.event)
WHERE ea.volunteer = :id
ORDER BY e.date
QUERY
);
$meetingsEventsDetail->execute([':id' => $_GET['id']]);
$eventTableBody = '';
foreach ($meetingsEventsDetail as $row) {
    $data = explode('-',$row['date']);
    if ($data[0] == $_GET['year']) {
        $monthObj = new Month($data[1], $data[0]);

        $eventTableBody .= "
            <tr>
                <td>{$data[2]} {$monthObj->getMonthName()} {$monthObj->getYear()}</td>
                <td>{$row['type']}</td>
                <td>{$row['title']}</td>
                <td>{$row['location']}</td>
                <td>{$row['description']}</td>
            </tr>
        ";
    }
}


$yearOptions='';
for($i=date("Y"); $i>1990; $i--)
    $yearOptions.="<option value='$i'>$i</option>";


$content = <<<HTML
<a class="pull-right" href="print_volunteer_detail.php?id={$_GET['id']}&year={$_GET['year']}"><img src="../img/print.png" width="30" height="30"></a>
<h1>Dettagli di {$user['firstname']} {$user['lastname']}</h1>

<h2>Riassunto presenze ai turni serali</h2>
<form method="GET" action="volunteer_detail.php">
    <input type="hidden" name="id" value="{$_GET['id']}">
    <div class="form-group row">
		<div class="select col-sm-2">
            <select class="form-control" name="year">
                $yearOptions
            </select>
        </div>
        <input class="btn btn-default col-sm-2" type="submit" value="Filtra per anno">
    </div>
</form>

<div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>Anno</th>
				<th>Mese</th>
				<th>Presenze</th>
			</tr>
		</thead>
		<tbody>
			$turnTableBody
		</tbody>
	</table>
</div>

<h2>Date e Ruoli nei turni serali</h2>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>Data</th>
				<th>Ruolo</th>
			</tr>
		</thead>
		<tbody>
			$turnDetailTableBody
		</tbody>
	</table>
</div>

<h2>Presenze a riunioni/corsi</h2>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>Data</th>
				<th>Tipo</th>
				<th>Titolo</th>
				<th>Luogo</th>
				<th>Descrizione</th>
			</tr>
		</thead>
		<tbody>
			$eventTableBody
		</tbody>
	</table>
</div>

<a href="volunteers.php" class="btn btn-default">Indietro</a>
HTML;





try {
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."volunteers/volunteers.php", PermissionPage::ADMIN);
    $generalLayout->yieldElem('title', "Dettagli Volontario");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
    exit;
}