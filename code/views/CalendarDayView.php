<?php

class CalendarDayView extends CalendarAbstractTimeView {
		
	// Abstract Functions Implemented
	
	function init() {
		parent::init();
		$this->containerClass = 'dayView';
		$this->innerClass = 'day';
		$this->viewTitle = 'return date(\'l jS F Y\', $date);';
	}
	
	function prevLinkParams(Calendar $calendar) {
		$date = array($calendar->getYear(), $calendar->getMonth(), $calendar->getDay());
		$date = strtotime(implode('-', $date));
		$date = strtotime("-$this->number days", $date);
		return $this->getLinkParams($date);
	}
	
	function nextLinkParams(Calendar $calendar) {
		$date = array($calendar->getYear(), $calendar->getMonth(), $calendar->getDay());
		$date = strtotime(implode('-', $date));
		$date = strtotime("+$this->number days", $date);
		return $this->getLinkParams($date);
	}
	
	function title() {return $this->number == 1 ? 'day' : "$this->number days";}
	
	function Dates(Calendar $calendar) {
		$year = $calendar->getYear();
		$month = $calendar->getMonth();
		$day = $calendar->getDay();
		
		for($i = 0; $i < $this->number; $i++) {
			if($i == 0) {
				$lastDate = strtotime("$year-$month-$day");
			}
			else {
				$lastDate = strtotime('+1 day', $lastDate);
			}
			$datesGroups[] = array($lastDate);
		}
		
		return $datesGroups;
	}
	
	function getCustomisedTitle($day, $month, $year) {
		$date = strtotime("$year-$month-$day");
		$result = eval($this->viewTitle);
		if($this->number > 1) {
			$date = strtotime(($this->number - 1) . ' days', $date);
			$result .= $this->viewTitleDelimiter . eval($this->viewTitle);
		}
		return $result;
	}
}
