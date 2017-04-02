<?php
require_once('generalLayout.php');

class UnhautorizedException extends Exception {
	public function echoAlert() {

		try {
			$generalLayout = new GeneralLayout('', PermissionPage::PUBLICPAGE);
			$generalLayout->yieldElem('title', "Unauthorized");
			$generalLayout->yieldElem('content', <<<TAG
				<h1>Unauthorized</h1>
				<p>Non sei autorizzato a visitare questa pagina.</p>
				<p>Potresti essere qui perché la tua sessione è scaduta o perché hai sbagliato qualcosa con l'url</p>
				<p>Vai <a href="../home.php">alla pagina di login</a> e logga di nuovo o con un account dai permessi più elevati</p>
				<p>In ogni caso...</p>
				<img src="https://media.giphy.com/media/CN565gKqjU8da/giphy.gif">
TAG
			);
			echo $generalLayout->getPage();

		}
		catch (UnhautorizedException $e) {
			echo 'I cant believe how much Unauthorized you are';
		}
	}
}



class AttemptToModifySomeoneElseDataOnPersonalCommandException extends Exception {
	public function echoAlert() {
		echo "Stai cercando di accedere ad una parte di tabella per cui non hai i permessi, ACCESS DENIED";
	}
}