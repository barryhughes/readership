<?php
class ReadershipPackageAdmin {
	protected $messages = array();
	
	public $output = '';
	
	
	public function __construct() {
		$action = array_key_exists('action', $_GET) ? $_GET['action'] : '';
		$action = array_key_exists('do_once', $_GET) ? $_GET['do_once'] : $action;
		
		switch ($action) {
			case 'edit':
				$this->editPackage();
			break;
			
			case 'delete':
				$this->deletePackage();
			break;
			
			default:
				$this->packageList();
			break;
		}
	}
	
	
	protected function editPackage() {
		$this->savePackage();
		$packagesList = new ReadershipPackageList;
		
		$vars = array(
			'messages' => $this->messages,
			'package' => $packagesList->getPackage($_GET['id'])
		);
		
		$this->output = Readership::view('editpackage', $vars, false);
	}
	
	
	protected function packageList() {
		$this->savePackage();
		$this->listActions();
		$packagesList = new ReadershipPackageList;
		
		$vars = array(
			'messages' => $this->messages,
			'packages' => $packagesList->getPackages()
		);
		
		$this->output = Readership::view('packages', $vars, false);
	}

	
	protected function savePackage() {
		if (empty($_POST) or wp_verify_nonce($_POST['readership'], 'readership') === false)
			return;
			
		if (isset($_POST['newsave']) === false and isset($_POST['save']) === false)
			return;
				
		$id = array_key_exists('packageid', $_POST) ? (int) $_POST['packageid'] : 0;
		$packageName = trim($_POST['name']);
		$packageInterval = $this->createIntervalObject();
		$packageRenews = ($_POST['renew'] === 'on') ? true : false;
		
		if (empty($packageName)) {
			$this->messages[] = array(
				'warning' => __('Packages must be named.', 'readership'));
			return;
		}
		
		$packageList = new ReadershipPackageList;		
		$package = new ReadershipPackage;
		
		$package->id($id === 0 ? $packageList->getUnusedID() : $id);
		$package->name($packageName);
		$package->interval($packageInterval);
		$package->shouldRenew($packageRenews);
		
		if ($id === 0) {
			$packageList->addPackage($package);
			$message = __('Package created!', 'readership');
		}
		else {
			$packageList->updatePackage($package);
			$message = __('Package updated!', 'readership');
		}
		
		$packageList->update();
		$this->messages[] = array('success' => $message);
	}

	
	protected function createIntervalObject() {
		$value = absint($_POST['interval']);
		$measure = $_POST['intervalmeasure'];
		
		switch ($measure) {
			case 'years': $measure = 'Y'; break;
			case 'months': $measure = 'M'; break;
			case 'weeks': $measure = 'W'; break;
			default: $measure = 'D'; break;
		}
		
		$intervalDescription = "P$value$measure";
		return new DateInterval($intervalDescription);
	}
	
	
	protected function deletePackage() {
		if (empty($_GET) or wp_verify_nonce($_GET['_wpnonce'], 'deletepackage') === false)
			return;
			
		$packageList = new ReadershipPackageList;
		if ($packageList->removePackage($_GET['id'])) {
			$packageList->update();		
			$this->messages[] = array('success' => __('Package deleted!', 'readership'));
		}
		
		$this->packageList();
	}
	
	
	protected function listActions() {
		if (empty($_POST) or wp_verify_nonce($_POST['readership'], 'readership') === false or
			array_key_exists('doaction', $_POST) === false)
				return;
			
		if (empty($_POST['selected']) or is_array($_POST['selected']) === false)
			return;
			
		$packageList = new ReadershipPackageList();
			
		foreach ($_POST['selected'] as $packageToRemove) {
			$packageToRemove = (int) $packageToRemove;
			$packageList->removePackage($packageToRemove);
		}
		
		$packageList->update();
		$this->messages[] = array('success' => __('The selected package or packages were deleted.', 'readership'));
		return;
	}
}