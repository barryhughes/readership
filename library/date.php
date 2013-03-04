<?php
class ReadershipDate {
	/**
	 * Accurate at date level (hours, minutes, seconds are not
	 * assessed).
	 */
	public static function isTodayOrEarlier(DateTime $reference) {
		$today = self::getYearMonthDay(new DateTime);
		$reference = self::getYearMonthDay($reference);

		$differentYear = false;
		$differentMonth = false;
		
		if ($reference['year'] > $today['year'])
			return false;
		
		if ($reference['year'] < $today['year'])
			$differentYear = true;
		
		elseif ($reference['month'] > $today['month'])
			return false;
		
		elseif ($reference['month'] < $today['month'])
			$differentMonth = true;
		
		if ($reference['day'] <= $today['day'] or $differentMonth or $differentYear)
			return true;
			
		return false;
	}
	
	
	/**
	 * Accurate at date level (hours, minutes, seconds are not
	 * assessed).
	 */
	public static function isInTheFuture(DateTime $reference) {
		$today = self::getYearMonthDay(new DateTime);
		$reference = self::getYearMonthDay($reference);
		
		if ($reference['year'] > $today['year'])
			return true;
			
		elseif ($reference['year'] < $today['year'])
			return false;
			
		if ($reference['month'] > $today['month'])
			return true;
			
		elseif ($reference['month'] < $today['month'])
			return false;
			
		if ($reference['day'] > $today['day'])
			return true;
			
		return false;
	}
	
	
	public static function getYearMonthDay(DateTime $date) {
		return array(
			'year' => (int) $date->format('Y'),
			'month' => (int) $date->format('n'),
			'day' => (int) $date->format('j')
		);
	}
	
	
	public static function daysLeft(DateTime $future) {
		$today = new DateTime;
		$range = $future->diff($today);
		
		return (int) $range->days;
	}
}