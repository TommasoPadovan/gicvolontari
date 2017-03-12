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

$db = new DbConnection;

$user = $db->getUser($_GET['id']);

$statement = $db->prepare(<<<TAG
SELECT c.year AS year, c.month AS month, COUNT(c.day) as count
FROM turni AS t JOIN calendar AS c ON (t.day = c.id)
  JOIN users AS u ON (t.volunteer = u.id)
WHERE u.id = :id
GROUP BY c.year, c.month
TAG
);

$statement->execute([':id' => $_GET['id']]);

$allVolunteerTurns = $statement->fetchAll(PDO::FETCH_ASSOC);

$turnTableBody='';
foreach ($allVolunteerTurns as $row) {
    $year = $row['year'];
    $month = $row['month'];
    $turnCount = $row['count'];

    $turnTableBody.="
        <tr>
            <td>$year</td>
            <td>$month</td>
            <td>$turnCount</td>
        </tr>
    ";
}




$content = <<<HTML
<h1>Dettagli di {$user['firstname']} {$user['lastname']}</h1>
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