<?php
class ReadershipPackageList {
	protected $list = array();
	
	
	public function __construct() {
		$this->loadPackageList();
		//$this->orderList();
	}
	
	
	protected function loadPackageList() {
		$this->list = (array) get_option('readershipPackages', array());
		foreach ($this->list as &$package)
			$package = new ReadershipPackage($package);
	}
	
	
	public function addPackage(ReadershipPackage $package) {
		$this->list[$package->id()] = $package;
	}
	
	
	public function updatePackage(ReadershipPackage $package) {
		$this->addPackage($package);
	}
	
	
	public function removePackage($id) {
		if (array_key_exists($id, $this->list)) {
			unset($this->list[$id]);
			return true;
		}
		return false;
	}
	

	public function getUnusedID() {
		$ids = array_keys($this->list);
		if (empty($ids)) return 1;
		
		$maxID = (int) max($ids);
		$maxID++;
		
		for ($i = 1; $i <= $maxID; $i++)
			if (isset($this->list[$i]) === false)
				return $i;
		
		return $i;
	}
	
	
	public function count() {
		return count($this->list);
	}
	
	
	public function getPackages() {
		return $this->list;
	}
	
	
	public function getPackage($id) {
		$id = (int) $id;
		
		if (isset($this->list[$id])) return $this->list[$id];
		return false;
	}
	
	
	public function update() {
		$representativeList = array();
		foreach ($this->list as $id => $package)
			$representativeList[$id] = $package->getRepresentation();
		
		update_option('readershipPackages', $representativeList);
	}
}