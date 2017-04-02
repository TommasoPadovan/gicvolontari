<?php
require_once("permission.php");
require_once("exceptions.php");

session_start();

class GeneralLayout extends PermissionPage {

	const HOMEPATH = '/padoWeb/sitoLilt/';

	private $elems;

	private $pages;


	public function __construct($url, $permission=null) {
		parent::__construct($permission);

		if (!$this->checkPermission()) {
			throw new UnhautorizedException();
		}
			

		$this->pages = array(
			new Page(self::HOMEPATH.'volunteers/volunteers.php','Volontari', PermissionPage::ADMIN),
			new Page(self::HOMEPATH."turns/turns.php", 'Turni', PermissionPage::MORNING),
			new Page(self::HOMEPATH.'events/eventsandcourses.php', 'Eventi', PermissionPage::MORNING),
			new Page(self::HOMEPATH.'commitments/mycommittments.php', 'Miei Impegni', PermissionPage::MORNING)
		);
		$this->elems  = array(
			'title' => '',
			'nav' => self::generateNav($this->pages, $url, self::HOMEPATH),
			'content' => '',
			'scripts' => ''
		);

	}

	

	public function yieldElem($identifier, $what) {
		$this->elems[$identifier] = $what;
	}

	public function getPage() {
		$homePath = self::HOMEPATH;
		return <<<HTML
<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<title>{$this->elems['title']}</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="{$homePath}lib/myStyle.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  		{$this->elems['scripts']}


	</head>

	<body>
		<nav class="navbar navbar-default">
			{$this->elems['nav']}
		</nav>

		<div class="container">
			{$this->elems['content']}
		</div>

	</body>

</html>
HTML;
	}





	private static function generateNav($pages, $current_url, $homePath) {
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
//		return <<<END
//			<div class="container-fluid collapse navbar-collapse">
//				<ul class="nav navbar-nav">
//					<div class="navbar-header">
//						<a class="navbar-brand" href="{$homePath}home.php">Lilt Volontari</a>
//	    			</div>
//	    			$li
//				</ul>
//			</div>
//END;

		if (isset($_SESSION['name'])) $userName = $_SESSION['name'];
		else $userName = 'Profilo';

		return <<<HTML
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">GIC Volontari</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        $li
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="{$homePath}home.php"><span class="glyphicon glyphicon-user"></span> $userName</a></li>
      </ul>
    </div>
  </div>
HTML;

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