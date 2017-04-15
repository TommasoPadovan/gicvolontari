<?php

/**
 * Created by IntelliJ IDEA.
 * User: kurt
 * Date: 12/03/2017
 * Time: 14:51
 */
require_once('../lib/sqlLib.php');

class Commitments{

    private $db;

    public function __construct() {
        $this->db = new DbConnection();
    }


    public function getTurnsArray() {
        //fetching turn data
        $result = $this->db->prepare('
            SELECT c.month as month, c.year as year, c.day as day, t.task as task,  t.position as position
            FROM calendar AS c JOIN turni AS t
                ON c.id=t.day
            WHERE t.volunteer = :id
        ');
        $result->execute(array('id' => $_SESSION['id']));
        $myCommitments=array();
        foreach ($result as $row) {
            array_push(
                $myCommitments,
                array(
                    'month' => new Month( intval($row['month']), intval($row['year']) ),
                    'day' => $row['day'],
                    'task' => $row['task'],
                    'position' => $row['position']
                )
            );
        }

        return $myCommitments;
    }

    public function getMeetingsArray() {
        $allEvents = $this->db->prepare("
            SELECT *
            FROM eventsattendants AS ea JOIN events AS e ON ea.event = e.id
            WHERE ea.volunteer = :volunteerId
        ");
        $allEvents->execute([':volunteerId' => $_SESSION['id']]);


        $meetings=[];
        foreach ($allEvents as $e) {
            if ($e['type'] == "riunione")
                array_push($meetings, $e);
        }

        return $meetings;
    }

    public function getEventsArray() {
        $allEvents = $this->db->prepare("
            SELECT *
            FROM eventsattendants AS ea JOIN events AS e ON ea.event = e.id
            WHERE ea.volunteer = :volunteerId
        ");
        $allEvents->execute([':volunteerId' => $_SESSION['id']]);

        $events=[];
        foreach ($allEvents as $e) {
            if ($e['type'] == "evento")
                array_push($events, $e);
        }

        return $events;
    }

    public function isOverbooked($volunteerID, $eventID) {
        $maxAttendants = $this->db->select('events', [
            'id' => $eventID
        ]);
        $maxAttendants = $maxAttendants[0]['maxAttendants'];

        $myTimestamp = $this->db->select('eventsattendants', [
            'event' => $eventID,
            'volunteer' => $volunteerID
        ]);
        $myTimestamp = $myTimestamp[0]['timestamp'];

        $volunteerQueue = $this->db->prepare("
            SELECT *
            FROM eventsattendants
            WHERE timestamp < :timestamp
        ");
        $volunteerQueue->execute([':timestamp' => $myTimestamp]);

        return !count($volunteerQueue) < $maxAttendants;
    }


}