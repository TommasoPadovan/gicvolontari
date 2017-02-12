<?php
require_once('lib/generalLayout.php');
require_once('lib/permissionsMng.php');
require_once('lib/sqlLib.php');

PermissionsMng::atMostAuthorizationLevel(1);

$conn=connect();

//general layout of one page
$generalLayout = new GeneralLayout("volunteers.php");

//setting the title
$generalLayout->yieldElem('title', "Volontari");


//table of users in the db
$usersTable='';
$users=queryThis("SELECT * FROM users", $conn);
for ($i=0; $i<mysql_num_rows($users) ; $i++) {
	$row = mysql_fetch_assoc($users);
	$usersTable.=userRow($row);
}


//setting the content of the page
$content = <<< HTML
<h1>Nuovo Volontario</h1>
<form action='volunteer_edit.php' method="POST">
	<div class="form-group">
		<label for="Nome">Nome</label>
		<input type="text" class="form-control" id="Nome" placeholder="Nome" name="Nome">
	</div>
	<div class="form-group">
		<label for="Cognome">Cognome</label>
		<input type="text" class="form-control" id="Cognome" placeholder="Cognome" name="Cognome">
	</div>
	<div class="form-group">
		<label for="Email">Email</label>
		<input type="email" class="form-control" id="Email" placeholder="Email" name="Email">
	</div>
	<div class="form-group">
		<label for="Posizione">Posizione</label>
		<div class="select">
			<select class="form-control" name="Posizione">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="Permessi">Permessi</label>
		<div class="select">
			<select class="form-control" name="Permessi">
				<option value="1">Amministratore</option>
				<option value="2">Utente</option>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label for="exampleInputPassword1">Password</label>
		<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="Password">
	</div>
	<button type="submit" class="btn btn-default">Submit</button>
</form>
<hr />

<h1>Lista Volontari</h1>
<div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>Firstname</th>
				<th>Lastname</th>
				<th>Email</th>
				<th>Posizione</th>
				<th>Permessi</th>
				<th>Azioni</th>
			</tr>
		</thead>
		<tbody>
			$usersTable
		</tbody>
	</table>
</div>

HTML;


$generalLayout->yieldElem('content', $content);


$generalLayout->pprint();






function userRow($row)  {

	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$email = $row['email'];
	$position = $row['position'];
	switch ($row['permessi']) {
		case 1:
			$permission = "Amministratore";
			break;
		case 2:
			$permission = "Utente";
			break;
		default:
			$permission = "No permessi";
			break;
	}
	$actions = "qua ci saranno dei magic link";

	$row = <<<EOF
		<tr>
			<td>$firstname</th>
			<td>$lastname</th>
			<td>$email</th>
			<td>$position</th>
			<td>$permission</td>
			<td>$actions</th>
		</tr>
EOF;
	return $row;
}

?>