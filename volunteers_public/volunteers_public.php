<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 07/06/2017
 * Time: 07:34
 */


require_once('../lib/generalLayout.php');
require_once('../lib/permission.php');
require_once('../lib/sqlLib.php');
require_once('../lib/datetime/month.php');

$db = new DbConnection;

//table of users in the db
$usersTable='';
$users = $db->query('SELECT * FROM users ORDER BY permessi, lastname, firstname');
foreach ($users as $row) {
    if ($row['id'] != 0)	//l'utente 0 è il placeholder per la riunione che va ad occupare i turni della sera
        $usersTable.=userRow($row, $db);
}


function userRow($row, $db) {
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $email = $row['email'];
    $phone = $row['phone'];
    switch ($row['permessi']) {
        case PermissionPage::ADMIN:
            $permission = "Amministratore";
            break;
        case PermissionPage::EVENING:
            $permission = "Sera";
            break;
        case PermissionPage::AFTERNOON:
            $permission = "Pomeriggio";
            break;
        case PermissionPage::MORNING:
            $permission = "Mattina";
            break;
        default:
            $permission = "No permessi";
            break;
    }


    return <<<EOR
    <tr>
        <td>$firstname</td>
        <td>$lastname</td>
        <td>$email</td>
        <td>$phone</td>
        <td>$permission</td>
    </tr>
EOR;

}


$content = <<<HTML
<h1>Lista Volontari</h1>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>Cognome</th>
				<th>Nome</th>
				<th>Email</th>
				<th>Telefono</th>
				<th>Ruolo</th>
			</tr>
		</thead>
		<tbody>
			$usersTable
		</tbody>
	</table>
</div>
HTML;

try {
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH.'volunteers_public/volunteers_public.php', PermissionPage::MORNING);
    $generalLayout->yieldElem('title', "Lista Volontari");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}














