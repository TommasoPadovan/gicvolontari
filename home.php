<?php
require_once('lib/generalLayout.php');
require_once('lib/sqlLib.php');









$db = new DbConnection();






$welcome='';

if ( isset( $_SESSION['id'] ) ) {
	foreach ( $db->select('users', array('id' => $_SESSION['id'])) as $row) {
		$welcome = "Ciao {$row['lastname']}";
	}
	$content =  <<<HTML
<h1>Home</h1>
<hr />
<div>
	$welcome
</div>
<hr />
<form action='process_logout.php' method="POST">
	<button type="submit" class="btn btn-default" name="logout" value="logout">Logout</button>
</form>
HTML;

} else {
	
	$content = <<<HTML
<h1>Home</h1>
<hr />
<h2>Login</h2>
<form action='process_login.php' method="POST">
	<div class = 'row'>
		<div class="form-group col-sm-6">
			<label for="Email">Email</label>
			<input type="text" class="form-control" id="Email" placeholder="Email" name="Email">
		</div>
		<div class="form-group col-sm-6">
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
	echo "culo";
}

//setting the title
$generalLayout->yieldElem('title', "Lilt Home");

//setting the content
$generalLayout->yieldElem('content', $content);

echo $generalLayout->getPage();





?>