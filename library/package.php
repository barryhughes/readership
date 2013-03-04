<?php
class ReadershipPackage {
	protected $properties = array(
		'id' => 0,
		'name' => '',
		'interval' => null,
		'shouldRenew' => false
	);
	
	
	public function __construct(array $representation = null) {
		$this->properties = apply_filters('readershipPackageProperties', $this->properties);
		$config = array_merge($this->properties, (array) $representation);
		
		foreach ($this->properties as $key => $value)
			$this->properties[$key] = $config[$key];
	}
	
	
	public function id($id = null) {
		if ($id === null) return $this->properties['id'];
		else $this->properties['id'] = (int) $id;
	}
	
	
	public function name($name = null) {
		if ($name === null) return $this->properties['name'];
		else $this->properties['name'] = (string) $name;
	}
	
	
	public function interval(DateInterval $interval = null) {
		if ($interval === null) return $this->properties['interval'];
		else $this->properties['interval'] = $interval;
	}
	
	
	public function getReadableInterval() {
		$interval = $this->properties['interval'];
		if ($interval === null) return 'Not set';
		
		$dayCount = (int) $interval->format('%d');
		$monthCount = (int) $interval->format('%m');
		$yearCount = (int) $interval->format('%y');
		
		if ($dayCount > 0) 
			$pattern = __('%d days', 'readership');
		elseif ($monthCount > 0)
			$pattern = __('%m months', 'readership');
		elseif ($yearCount > 0)
			$pattern = __('%y years', 'readership');
		else
			return __('Does not expire', 'readership');
		
		return $interval->format($pattern);
	}
	
	
	public function doesNotExpire() {
		$interval = $this->properties['interval'];
		if ($interval === null) return false;
		
		if ($interval->format('%d%m%y') === '000')
			return true;
			
		return false;
	}
	
	
	public function getIntervalBreakdown() {
		$interval = $this->properties['interval'];
		if ($interval === null) return array(0, 'years');
			
		$dayCount = (int) $interval->format('%d');
		$monthCount = (int) $interval->format('%m');
		$yearCount = (int) $interval->format('%y');
		
		if ($dayCount > 0) 
			return array($dayCount, 'days');
		elseif ($monthCount > 0)
			return array($monthCount, 'months');
		else
			return array($yearCount, 'years');
	}
	
	
	public function shouldRenew($booleanFlag = null) {
		if ($booleanFlag === null) return $this->properties['shouldRenew'];
		else $this->properties['shouldRenew'] = (bool) $booleanFlag;
	}
	
	
	public function getRepresentation() {
		return apply_filters('readershipPackagePropertiesRepresentation', $this->properties);
	}
}