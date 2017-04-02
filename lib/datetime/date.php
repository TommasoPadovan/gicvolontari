<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 13/03/2017
 * Time: 10:46
 */
class Date {
    private $y;
    private $m;
    private $d;

    public function __construct($internationalDate) {
        $splitDate = explode('-', trim($internationalDate));

        $this->y = $splitDate[0];
        $this->m = $splitDate[1];
        $this->d = $splitDate[2];
    }

    public function getItalianDate() {
        return "{$this->d}/{$this->m}/{$this->y}";
    }

}