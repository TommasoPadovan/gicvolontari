<?php
/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 02/03/2017
 * Time: 23:16
 */


require_once ('permission.php');


class PermissionString {

    /**
     * @var String array cointaining permission types indexed on string outputs
     */
    private $options;

    public function __construct(array $arrayOptions){
        $this->options = $arrayOptions;
    }

    public function out() {
        if (PermissionPage::getCurrentPermission() == null)
            return "";

        $lowerPermissionStrings = array();
        foreach($this->options as $permission => $str) {
            if (PermissionPage::getCurrentPermission() <= $permission)
                array_push($lowerPermissionStrings, $permission);
        }

        if (count($lowerPermissionStrings) > 0)
            return $this->options[max($lowerPermissionStrings)];
        return "";
    }
}