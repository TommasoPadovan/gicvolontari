<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 01/04/2017
 * Time: 15:38
 */
class GetConstraints {

    private $inTable = [];
    private $betweenBounds = [];
    private $db;

    public function __construct($table, $bounds) {
        $this->inTable = $table;
        $this->betweenBounds = $bounds;
        $this->db = new DbConnection();
    }

    public function addTableConstraint($tableConstraint) {
        array_push($this->inTable, $tableConstraint);
    }

    public function addBoundsConstraint($boundsConstraint) {
        array_push($this->betweenBounds, $boundsConstraint);
    }


    public function areOk(){
        foreach ($this->inTable as $value => $table) {
            if (count($this->db->select($table[0], [
                $table[1] => $value
            ] )) == 0 )
                return false;
        }

        foreach ($this->betweenBounds as $value => $bounds) {
            if ($value<$bounds[0] || $value>$bounds[1])
                return false;
        }

        return true;
    }

    public function getErrorContent() {
        if (isset($_SERVER['HTTP_REFERER']))
            $back = $_SERVER['HTTP_REFERER'];
        else
            $back = '../home.php';

        return <<<ERRORPAGE
    <h1>Elemento non Trovato</h1>
    <div class="row">
        <div class="col-sm-6">
            <img src="../img/something-wrong.jpg" width="100%" height="100%">
        </div>
        <div class="col-sm-6">
            <p>Oooops, l'elemento che hai selzionato non esiste più.</p>
            <p>Potrebbe esserci stato un problema con l'url della pagina, oppure l'hai modificato per sbaglio.</p>
            <p>Potrebbero averti passato un link ad un elemento che è stato cancellato nel frattempo.</p>
            <p>In ogni caso torna indietro e riprova.</p>
            <a href="$back" class="btn btn-default">Indietro</a>
        </div>
    </div>
ERRORPAGE;
    }

}