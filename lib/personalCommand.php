<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 15/03/2017
 * Time: 18:00
 */
require_once('command.php');


abstract class PersonalCommand extends Command {

    protected $onlyAuthorizedId;

    public function __construct($permission, $id){
        parent::__construct($permission);
        $this->onlyAuthorizedId = $id;
    }

    public function execute() {
        if (!$this->checkPermission()) {
            throw new UnhautorizedException();
        }
        if ($this->onlyAuthorizedId != $_SESSION['id']) {
            throw new AttemptToModifySomeoneElseDataOnPersonalCommandException();
        }
        $this->template();
    }

}