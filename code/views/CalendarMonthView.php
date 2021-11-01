<?php

class CalendarMonthView extends CalendarAbstractWeekView {

	// Attributes

	private $monthClass;

	// Abstract Functions Implemented

	function init() {
		parent::init();
		$this->containerClass = 'monthView';
		$this->innerClass = 'month';
		$this->viewTitle = 'return date(\'F Y\', $date);';
		$this->monthClass = 'return strtolower(date(\'F\', $monthDate));';
	}

	function needsMonth() {return true;}
	function needsDay() {return false;}

	function prevLinkParams(Calendar $calendar) {
		$date = array($calendar->getYear(), $calendar->getMonth(), 1);
		$date = strtotime(implode('-', $date));
		$date = strtotime("-$this->number months", $date);
		return $this->getLinkParams($date);
	}

	function nextLinkParams(Calendar $calendar) {
		$date = array($calendar->getYear(), $calendar->getMonth(), 1);
		$date = strtotime(implode('-', $date));
		$date = strtotime("+$this->number months", $date);
		return $this->getLinkParams($date);
	}

	function viewLinkParamsAndTitle(Calendar $calendar) {
		$month = $calendar->getMonth();
		if(! $month) $month = 1;
		$year = $calendar->getYear();
		$date = strtotime("$year-$month-1");
		$params = $this->getLinkParams($date);
		$title = $this->getCustomisedTitle($month, $year);
		return array($params, $title);
	}

	function getLinkParams($date) {
		return array(
			'year' => date('Y', $date),
			'month' => date('n', $date)
		);
	}

	function title() {return $this->number == 1 ? 'month' : "$this->number months";}

	function DateTitle(Calendar $calendar) {
		return $this->getCustomisedTitle($calendar->getMonth(), $calendar->getYear());
	}

	function Weeks(Calendar $calendar) {
		$year = $calendar->getYear();
		$month = $calendar->getMonth();

		$nowYear = date('Y');
		$nowMonth = date('n');

		for($i = 0; $i < $this->number; $i++) {
			$weeksGroup = $this->MonthWeeks($month, $year);

			// 1) Single Values

			$monthDate = strtotime("$year-$month-1");
			$values['ExtraInnerClass'] = eval($this->monthClass) . " year$year";
			$values['IsNowYear'] = $year == $nowYear;
			$values['IsPastYear'] = $year < $nowYear;
			$values['IsNow'] = $values['IsNowYear'] && $month == $nowMonth;
			$values['IsPast'] = $values['IsPastYear'] || ($values['IsNowYear'] && $month < $nowMonth);

			$weeksGroups[] = array($weeksGroup, $values);

			if(++$month > 12) {
				$month = 1;
				$year++;
			}
		}

		return $weeksGroups;
	}

	// Private Functions

	protected function MonthWeeks($month, $year) {
		$firstDate = strtotime("$year-$month-1");
		$firstDateWeek = date('W', $firstDate);
		$firstDateWeekYear = $year;

		if($month == 1 && $firstDateWeek >= 52) {
			$firstDateWeekYear--;
		}

		$weekFirstDate = $this->getWeekStartDay($firstDateWeek, $firstDateWeekYear, true);
		$nextWeekFirstDate = strtotime('+1 week', $weekFirstDate);

		if(date('j', $nextWeekFirstDate) == 1) {
			$weekFirstDate = $nextWeekFirstDate;
		}

		while(date('Y', $weekFirstDate) < $year || (date('Y', $weekFirstDate) == $year && date('n', $weekFirstDate) <= $month)) {
			$weekMonday = $weekFirstDate;
			while(date('N', $weekMonday) != 1) {
				$weekMonday = strtotime('+1 day', $weekMonday);
			}
			$week = date('W', $weekMonday);
			$yearOfWeek = date('Y', $weekMonday);
			if($month == 1) {
				if($week == 1 && $yearOfWeek == $year - 1) {
					$yearOfWeek++;
				}
				else if($week >= 52 && $yearOfWeek == $year) {
					$yearOfWeek--;
				}
			}
			else if($month == 12 && $week == 1) {
				$yearOfWeek++;
			}

			$weeks[] = array('week' => $week, 'yearOfWeek' => $yearOfWeek, 'month' => $month, 'yearOfMonth' => $year);
			$weekFirstDate = strtotime('+1 week', $weekFirstDate);
		}

		return $weeks;
	}

	// Other Functions

	function getCustomisedTitle($month, $year) {
		$date = strtotime("$year-$month-1");
		$result = eval($this->viewTitle);
		if($this->number > 1) {
			$date = strtotime(($this->number - 1) . ' months', $date);
			$result .= $this->viewTitleDelimiter . eval($this->viewTitle);
		}
		return $result;
	}


}

