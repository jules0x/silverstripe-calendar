<?php

class CalendarWeekView extends CalendarAbstractTimeView {
	
	// Attributes
	
	private $dayStart = 1;
	private $daysRemoved = array();
	
	// Abstract Functions Implemented
	
	function init() {
		parent::init();
		$this->containerClass = 'weekView';
		$this->innerClass = 'week';
		$this->viewTitle = 'return \'Week Of \' . date(\'l jS F Y\', $date);';
	}
	
	function prevLinkParams(Calendar $calendar) {
		$date = $this->getWeekStartDay($calendar->getDay(), $calendar->getMonth(), $calendar->getYear());
		$date = strtotime("-$this->number weeks", $date);
		return $this->getLinkParams($date);
	}
	
	function nextLinkParams(Calendar $calendar) {
		$date = $this->getWeekStartDay($calendar->getDay(), $calendar->getMonth(), $calendar->getYear());
		$date = strtotime("+$this->number weeks", $date);
		return $this->getLinkParams($date);
	}
	
	function title() {return $this->number == 1 ? 'week' : "$this->number weeks";}
	
	function Dates(Calendar $calendar) {
		$year = $calendar->getYear();
		$month = $calendar->getMonth();
		$day = $calendar->getDay();
		
		if(count($this->daysRemoved) == 7) {
			return $datesGroups;
		}
		
		$lastDate = $this->getWeekStartDay($day, $month, $year);
		
		while(date('N', $lastDate) != $this->dayStart) {
			$lastDate = strtotime('-1 day', $lastDate);
		}
		while(in_array(date('N', $lastDate), $this->daysRemoved)) {
			$lastDate = strtotime('+1 day', $lastDate);
		}
		
		for($i = 0; $i < $this->number; $i++) {
			$datesGroup = array();
			for($j = 0; $j < 7; $j++) {
				if(! in_array(date('N', $lastDate), $this->daysRemoved)) {
					$datesGroup[] = $lastDate;
				}
				$lastDate = strtotime('+1 day', $lastDate);
			}
			$datesGroups[] = $datesGroup;
		}
		
		return $datesGroups;
	}
	
	function getCustomisedTitle($day, $month, $year) {
		$date = $this->getWeekStartDay($day, $month, $year);
		$result = eval($this->viewTitle);
		if($this->number > 1) {
			$date = strtotime(($this->number - 1) . ' weeks', $date);
			$result .= $this->viewTitleDelimiter . eval($this->viewTitle);
		}
		return $result;
	}
	
	// Functions
	
	function startByMonday() {$this->dayStart = 1;}
	function startByTuesday() {$this->dayStart = 2;}
	function startByWednesday() {$this->dayStart = 3;}
	function startByThursday() {$this->dayStart = 4;}
	function startByFriday() {$this->dayStart = 5;}
	function startBySaturday() {$this->dayStart = 6;}
	function startBySunday() {$this->dayStart = 7;}
	
	function removeMonday() {$this->removeDay(1);}
	function removeTuesday() {$this->removeDay(2);}
	function removeWednesday() {$this->removeDay(3);}
	function removeThursday() {$this->removeDay(4);}
	function removeFriday() {$this->removeDay(5);}
	function removeSaturday() {$this->removeDay(6);}
	function removeSunday() {$this->removeDay(7);}
	
	// Private Functions
	
	private function getWeekStartDay($day, $month, $year) {
		$date = strtotime("$year-$month-$day");
		
		while(date('N', $date) > 1) { // It means that the 1st day of this week is not Monday
			$date = strtotime('-1 day', $date);
		}
		
		return $date;
	}
	
	private function getWeekEndDay($day, $month, $year) {
		$date = $this->getWeekStartDay($day, $month, $year);
		$date = strtotime('+6 days', $date);
		return $date;
	}
	
	private function removeDay($day) {
		if(! in_array($day, $this->daysRemoved)) {
			$this->daysRemoved[] = $day;
		}
	}
}
 
?>
