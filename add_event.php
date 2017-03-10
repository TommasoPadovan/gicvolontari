<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 09/03/2017
 * Time: 17:18
 */
require_once('lib/generalLayout.php');
require_once('lib/permission.php');
require_once('lib/sqlLib.php');


$db = new DbConnection();

$id=null;

$pageTitle = 'Nuovo Evento';
$type = '';
$title = '';
$date = '';
$timeStart = '';
$timeEnd = '';
$location = '';
$description = '';
$requirements = '';
$minAttendants = '';
$maxAttendants = '';

if (isset($_GET['id'])) {
    $selectedEvent=$db->select('events', ['id' => $_GET['id']])[0];

    $id = $_GET['id'];

    $type = $selectedEvent['type'];
    $title = $selectedEvent['title'];
    $date = $selectedEvent['date'];
    $timeStart = $selectedEvent['timeStart'];
    $timeEnd = $selectedEvent['timeEnd'];
    $location = $selectedEvent['location'];
    $description = $selectedEvent['description'];
    $requirements = $selectedEvent['requirements'];
    $minAttendants = $selectedEvent['minAttendants'];
    $maxAttendants = $selectedEvent['maxAttendants'];

    $pageTitle="Modifica evento: $title";
}

$content = <<<HTML
    <h1>$pageTitle</h1>
    <form action='edit_event.php' method="POST">
        <input type="hidden" name="id" value="$id">
	    <div class="row">
            <div class="form-group col-sm-6">
				<label for="title">Titolo</label>
				<input type="text" class="form-control" id="title" placeholder="Titolo" name="title" value="$title">
			</div>
			<div class="form-group col-sm-2">
				<label for="type">Tipo</label>
                <select class="form-control" name="type" id="type">
                    <option value="riunione">Riunione</option>
                    <option value="evento">Evento</option>
                </select>
			</div>
			<div class="form-group col-sm-4">
				<label for="location">Luogo</label>
				<input type="text" class="form-control" id="location" placeholder="Luogo" name="location" value="$location">
			</div>
		</div><div class="row">
			<div class="form-group col-sm-4">
				<label for="date">Data</label>
				<input type="date" class="form-control" id="date" placeholder="Data" name="date" value="$date">
			</div>
			<div class="form-group col-sm-2">
				<label for="timeStart">Ora Inizio</label>
				<input type="time" class="form-control" id="timeStart" placeholder="Ora Inizio" name="timeStart" value="$timeStart">
			</div>
			<div class="form-group col-sm-2">
				<label for="timeEnd">Ora Fine</label>
				<input type="time" class="form-control" id="timeEnd" placeholder="Ora Fine" name="timeEnd" value="$timeEnd">
			</div>
			<div class="form-group col-sm-2">
				<label for="minAttendants">Minimo Partecipanti</label>
				<input type="number" class="form-control" id="minAttendants" placeholder="Minimo Partecipanti" name="minAttendants" value="$minAttendants">
			</div>
			<div class="form-group col-sm-2">
				<label for="maxAttendants">Massimo Partecipanti</label>
				<input type="number" class="form-control" id="maxAttendants" placeholder="Massimo Partecipanti" name="maxAttendants" value="$maxAttendants">
			</div>
		</div><div class="row">
				<div class="form-group col-sm-6">
				<label for="description">Descrizione</label>
				<textarea class="form-control" id="description" placeholder="Descrizione" name="description" rows="10">$description</textarea>
			</div>
			<div class="form-group col-sm-6">
				<label for="requirements">Requisiti</label>
				<textarea class="form-control" id="requirements" placeholder="Requisiti" name="requirements" rows="10">$requirements</textarea>
			</div>
           <button type="submit" class="btn btn-default">Submit</button>
		</div>
	</form>
HTML;





try {
    $generalLayout = new GeneralLayout("add_event.php", PermissionPage::AFTERNOON);
    $generalLayout->yieldElem('title', "Aggiungi / Modifica Evento");
    $generalLayout->yieldElem('content', $content);
    echo $generalLayout->getPage();
}
catch (UnhautorizedException $e){
    $e->echoAlert();
    exit;
}