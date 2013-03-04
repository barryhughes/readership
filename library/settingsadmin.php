<?php
class ReadershipSettingsAdmin {
	protected $messages = array();
	protected $packageList;

	public $output = '';


	public function __construct() {
		$this->packageList = ReadershipRegister::packageList();
		$this->saveUpdates();

		$vars = array(
			'postTypes' => ReadershipSettings::getPublicPostTypes(),
			'packages' => $this->packageList->getPackages(),
			'defaultPackage' => ReadershipSettings::defaultPackage(),
			'autoSubscribe' => ReadershipSettings::autoSubscribe(),
			'sessionMarker' => ReadershipSettings::sessionMarker(),
			'messages' => $this->messages
		);

		$this->output = Readership::view(
			'settings', $vars, false);
	}


	protected function saveUpdates() {
		if (empty($_POST) or wp_verify_nonce($_POST['readership'], 'readership') === false or
			isset($_POST['save']) === false)
				return;

		$this->updatePostTypes();
		$this->updateAutoSubscribe();
		$this->updateSessionMarker();

		$this->messages[] = array('success' =>
			__('All changes have been processed.', 'readership'));
	}


	protected function updatePostTypes() {
		ReadershipSettings::supportPostTypes((array) $_POST['posttypes']);
	}


	protected function updateAutoSubscribe() {
		if ($this->packageList->getPackage($_POST['autosubpackage']) !== false)
		    ReadershipSettings::defaultPackage((int) $_POST['autosubpackage']);

		else
			ReadershipSettings::defaultPackage(null);

		$autoSubscribe = (bool) $_POST['autosubscribe'];
		ReadershipSettings::autoSubscribe($autoSubscribe);
	}


	protected function updateSessionMarker() {
		$sessionMarker = (bool) $_POST['sessionmarker'];
		ReadershipSettings::sessionMarker($sessionMarker);
	}
}