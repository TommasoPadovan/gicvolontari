<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 13/03/2017
 * Time: 10:54
 */
class Time {

    private $h;
    private $m;
    private $s;

    public function __construct($internationalTime) {
        $splitTime = explode(':', trim($internationalTime));

        $this->h = $splitTime[0];
        $this->m = $splitTime[1];
        $this->s = $splitTime[2];
    }

    public function getSimpleTime() {
        return "{$this->h}:{$this->m}";
    }

    public function getEnglishTime() {
        $h = intval($this->h);
        $ampm = "AM";
        if ($h>12) {
            $h -= 12;
            $ampm = "PM";
        }
        return "$h:{$this->m} $ampm";
    }

}