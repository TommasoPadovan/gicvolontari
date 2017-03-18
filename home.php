<?php
require_once('lib/generalLayout.php');
require_once('lib/sqlLib.php');

$db = new DbConnection();


$welcome='';

if ( isset( $_SESSION['id'] ) ) {
	foreach ( $db->select('users', array('id' => $_SESSION['id'])) as $row) {
		$welcome = "Ciao {$row['firstname']} {$row['lastname']}";
	}
	$content =  <<<HTML
<h1>Home</h1>
<hr />
<div>
	<p>$welcome</p>
</div>
<hr />
<div class="row">
	<div class="col-sm-3">
		<form action='process_logout.php' method="POST">
			<button type="submit" class="btn btn-default btn-block" name="logout" value="logout">Logout</button>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-sm-3">
		<a class="btn btn-default btn-block" href="volunteers/user_edit_own_profile.php">Modifica il tuo profilo</a>
	</div>
</div>
<div class="row">
	<div class="col-sm-3">
		<a class="btn btn-default btn-block" href="volunteers/user_edit_own_psw.php">Modifica la tua password</a>
	</div>
</div>
HTML;

} else {
	
	$content = <<<HTML
<h1>Home</h1>
<hr />
<h2>Login</h2>
<form class="col-xs-12" action='process_login.php' method="POST">
	<div class = 'row'>
		<div class="form-group col-sm-6 col-xs-12">
			<label for="Email">Email</label>
			<input type="text" class="form-control" id="Email" placeholder="Email" name="Email">
		</div>
		<div class="form-group col-sm-6 col-xs-12">
			<label for="Password">Password</label>
			<input type="password" class="form-control" id="Password" placeholder="Password" name="Password">
		</div>
	</div>
	<button type="submit" class="btn btn-default" name="login" value="login">Submit</button>
</form>
HTML;
}


//general layout of one page
try {
	$generalLayout = new GeneralLayout("home.php", PermissionPage::PUBLICPAGE);
}
catch(UnhautorizedException $e) {
	$e->echoAlert();
}

//setting the title
$generalLayout->yieldElem('title', "Lilt Home");

//setting the content
$generalLayout->yieldElem('content', $content);

echo $generalLayout->getPage();