<?php
session_start();
class GeneralLayout {

	private $template = <<<HTML
<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<title>@yield('title')</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="lib/myStyle.css">


	</head>

	<body>
		<nav class="navbar navbar-default">
			@yield('nav')
		</nav>

		<div class="container">
			@yield('content')
		</div>

	</body>

</html>
HTML;

	public function __construct($url) {
		$pages = array(
			new Page('volunteers.php','Aggiungi Volontario',1),
			new Page('events.php', 'Gestione Eventi', 1),
			new Page('turns.php', 'Turni',2),
			new Page('mycommittments.php', 'Miei Impegni', 2)
		);

		$this->yieldElem('nav', self::generateNav($pages, $url));
	}

	

	public function yieldElem($identifier, $what) {
		$this->template = str_replace("@yield('$identifier')", $what, $this->template);
	}

	public function pprint() {
		echo $this->template;
	}










	private static function generateNav($pages, $current_url) {
		$li='';
		foreach ($pages as $page) {
			if ( isset( $_SESSION['permessi'] ) && $_SESSION['permessi']!=0 ) {
				if ( $_SESSION['permessi'] <= $page->permessi ) {
					if ($current_url==$page->url) $active="active";
					else $active='';
					$li.="<li class='$active'><a href=\"".$page->url."\">".$page->title."</a></li>\n";
				}
			}
		}
		return <<<END
			<div class="container-fluid">
				<ul class="nav navbar-nav">
					<div class="navbar-header">
						<a class="navbar-brand" href="home.php">Lilt Volontari</a>
	    			</div>
	    			$li
				</ul>
			</div>
END;
	}

}










class Page {
	public $url;
	public $title;
	public $permessi;

	public function __construct($u,$t,$p) {
		$this->url = $u;
		$this->title = $t;
		$this->permessi = $p;
	}

}









?>