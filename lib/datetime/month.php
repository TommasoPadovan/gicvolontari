<?php
class Month {
	private $year;
	private $month;
	private $dayPerMonth;

	public function __construct($m,$y) {
		$this->year=$y;
		$this->month=$m;
		$this->dayPerMonth = array(
			1 => 31,
			2 => 28,
			3 => 31,
			4 => 30,
			5 => 31,
			6 => 30,
			7 => 31,
			8 => 31,
			9 => 30,
			10 => 31,
			11 => 30,
			12 => 31
		);
		if ($this->year % 4 == 0)
			$this->dayPerMonth[2]=29;
	}

	public static function getMonthFromInternational($internationalMonth) {
		$splitMonth = explode('-', $internationalMonth);
		return new Month(intval($splitMonth[1]), intval($splitMonth[0]));
	}

	public function getAllWorking() {
		$allWorking = array();
		for ($i=1; $i <= $this->dayPerMonth[$this->month] ; $i++) { 
			$date = "{$this->year}-{$this->month}-$i";
			$dayofweek = date('w', strtotime($date));
			if (1<=$dayofweek and $dayofweek<=5)
				array_push($allWorking, $i);
		}
		return $allWorking;
	}

	public function dayOfWeek($i) {
		$date = "{$this->year}-{$this->month}-$i";
		return date('w', strtotime($date));
	}

	public function dayOfWeekName($i) {
		switch ($this->dayOfWeek($i)) {
			case 0:
				return 'Domenica';
				break;
			case 1:
				return 'Lunedi\'';
				break;
			case 2:
				return 'Martedi\'';
				break;
			case 3:
				return 'Mercoledi\'';
				break;
			case 4:
				return 'Giovedi\'';
				break;
			case 5:
				return 'Venerdi\'';
				break;
			case 6:
				return 'Sabato';
				break;
		}
	}


	public function dayThisMonth() {
		return $this->dayPerMonth[$this->month];
	}

	public function getYear() {return $this->year;}

	public function getMonth() {return $this->month;}
	
	public function getMonthName() {
		switch ($this->month) {
			case 1:
				return 'Gennaio';
				break;
			case 2:
				return 'Febbraio';
				break;
			case 3:
				return 'Marzo';
				break;
			case 4:
				return 'Aprile';
				break;
			case 5:
				return 'Maggio';
				break;
			case 6:
				return 'Giungo';
				break;
			case 7:
				return 'Luglio';
				break;
			case 8:
				return 'Agosto';
				break;
			case 9:
				return 'Settembre';
				break;
			case 10:
				return 'Ottobre';
				break;
			case 11:
				return 'Novembre';
				break;
			case 12:
				return 'Dicembre';
				break;
		}
	}

	public function getDayName($i) {
		switch ($this->dayOfWeek($i)) {
			case 0:
				return 'Domenica';
				break;
			case 1:
				return 'Lunedì';
				break;
			case 2:
				return 'Martedì';
				break;
			case 3:
				return 'Mercoledì';
				break;
			case 4:
				return 'Giovedì';
				break;
			case 5:
				return 'Venerdì';
				break;
			case 6:
				return 'Sabato';
				break;

		}
	}


	public function isInFuture() {
		return ($this->year > date("Y") || ($this->year==date("Y") && $this->month >= date("m")) );
	}

	public function isNow() {
		return ($this->year == date("Y") && $this->month==date("m"));
	}



}
