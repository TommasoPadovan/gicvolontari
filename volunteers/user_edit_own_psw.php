<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 15/03/2017
 * Time: 18:11
 */

require_once('../lib/generalLayout.php');
require_once('../lib/sqlLib.php');

$db = new DbConnection();

$user = $db->getUser($_SESSION['id']);

$Nome = $user['firstname'];
$Cognome = $user['lastname'];

$content = <<< HTML
<h1>Modifica password - $Nome $Cognome</h1>
<hr />
<form action='process_user_edit_own_psw.php' method="POST">
	<div id="editVolunteerForm">
		<div class="row">
			<div class="form-group col-sm-3">
				<label for="oldpsw">Inserisci la vecchia Password</label>
				<input type="password" class="form-control" id="oldpsw" name="oldpsw" >
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-3">
				<label for="newpsw">Inserisci la nuova Password</label>
				<input type="password" class="form-control" id="newpsw" name="newpsw" >
			</div>
			<div class="form-group col-sm-3">
				<label for="newpswr">Ripeti la nuova Password</label>
				<input type="password" class="form-control" id="newpswr" name="newpswr" >
			</div>
		</div>
		<button type="submit" class="btn btn-default">Submit</button>
</form>
<hr />
HTML
;

try {
    $generalLayout = new GeneralLayout(GeneralLayout::HOMEPATH."", PermissionPage::MORNING);
    $generalLayout->yieldElem('title', 'Modifica Password');
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e) {
    $e->echoAlert();
}
