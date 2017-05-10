<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 08/05/2017
 * Time: 10:20
 */
class JS {
    public static function alert($msg) {
        $encodedMsg = json_encode($msg);
        echo("<script>
                alert($encodedMsg);
             </script>");
    }

    public static function alertAndRedirect($msg, $to) {
        $encodedMsg = json_encode($msg);
        echo("<script>
                alert($encodedMsg);
                window.location='$to';
            </script>");
    }
}