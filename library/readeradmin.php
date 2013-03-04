<?php
class ReadershipReaderAdmin {
	protected $messages = array();

	public $output = '';


	public function __construct() {
		switch (ReadershipAdmin::getAction('list')) {
			case 'edit': $this->editRecord();  break;
			default:     $this->listRecords(); break;
		}
	}


	protected function editRecord() {
		$this->saveReaderEdits();
		$this->handleUnsubscribes();

		$packageList = ReadershipRegister::packageList();

		$reader = new ReadershipReader($_GET['id']);
		$packageIDs = $reader->listAssignedPackages();
		$packages = array();
		$availablePackages = $reader->listUnassignedPackages();

		foreach ($packageIDs as $id => $data)
			$packages[] = $packageList->getPackage($id);

		$vars = array(
			'reader' => $reader,
			'packages' => $packages,
			'availablePackages' => $availablePackages,
			'messages' => $this->messages
		);

		$this->output = Readership::view(
			'editreader', $vars, false);
	}


	protected function saveReaderEdits() {
		if (empty($_POST) or wp_verify_nonce($_POST['readership'], 'readership') === false)
			return;

		$readerList = ReadershipRegister::readerList();
		$packageList = ReadershipRegister::packageList();
		$reader = $readerList->getReaderByUserID($_GET['id']);
		$invalidDateErrors = false;
		$assignErrors = false;

		// Starting date changes
		if (isset($_POST['startdate']))
			foreach ($_POST['startdate'] as $packageID => $revisedStartDate) {
				try {
					$date = new DateTime($revisedStartDate);
					$reader->assignToPackage($packageID, $date);
				} catch (Exception $e) {
					$invalidDateErrors = true;
				}
			}

		// Assigned package changes
		if (isset($_POST['assignables']))
			foreach ($_POST['assignables'] as $packageID) {
				if ($reader->assignToPackage($packageID) === false)
					$assignErrors = true;
			}

		if ($invalidDateErrors === true) $this->messages[] = array('warning' =>
			__('A problem occured while changing the dates. Please ensure you used the '
			.'correct date format.', 'readership'));

		if ($assignErrors === true) $this->messages[] = array('warning' =>
			__('The package could not be assigned.', 'readership'));

		elseif ($invalidDateErrors === false) $this->messages[] = array('success' =>
			__('All changes to this record have been processed.', 'readership'));
	}


	protected function handleUnsubscribes() {
		$wpNonceCheck = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';

		if (wp_verify_nonce($wpNonceCheck, 'unsubscribe') === false or
			isset($_GET['do_once']) === false)
				return;

		$unsubscribe = explode('-', $_GET['do_once']);
		if (count($unsubscribe) !== 2 or $unsubscribe[0] !== 'unsubscribe') return;

		$readerList = ReadershipRegister::readerList();
		$reader = $readerList->getReaderByUserID($_GET['id']);
		$success = $reader->removeFromPackage((int) $unsubscribe[1]);

		if ($success) $this->messages[] = array('success' =>
			__('The reader was unsubscribed from the selected package.', 'readership'));

		else $this->messages[] = array('warning' =>
			__('The unsubscribe operation failed.', 'readership'));
	}


	protected function listRecords() {
		$this->handleNewReaderRequests();
		$this->handleUnattachRequests();

		$showPage = isset($_GET['showpage']) ? absint($_GET['showpage']) : 1;
		$resultsPerPage = isset($_GET['resultset']) ? absint($_GET['resultset']) : 20;
		$readerList = new ReadershipReaderList($showPage, $resultsPerPage);
		$totalPages = absint(ceil($readerList->totalRegisteredReaders() / $resultsPerPage));
		$packageList = ReadershipRegister::packageList();

		$vars = array(
			'readers' => $readerList->getReaders(),
			'users' => $readerList->getNonReaders(),
			'allReaders' => $readerList->totalRegisteredReaders(),
			'packages' => $packageList->getPackages(),
			'messages' => $this->messages,
			'showPage' => $showPage,
			'resultsPerPage' => $resultsPerPage,
			'totalPages' => $totalPages
		);

		$this->output = Readership::view(
			'readers', $vars, false);
	}


	protected function handleNewReaderRequests() {
		if (empty($_POST) or wp_verify_nonce($_POST['readership'], 'readership') === false)
			return;

		if (!isset($_POST['pullaccounts'])) return;

		$readerList = ReadershipRegister::readerList();
		$assignables = false;
		$assignees = false;
		$success = true;

		$pullUsers = (isset($_POST['pullusers'])) ? $_POST['pullusers'] : array();
		$assignToList = (isset($_POST['assignables'])) ? $_POST['assignables'] : array();

		foreach ($pullUsers as $userID) {
			$assignees = true; // We have assignees!
			foreach ($assignToList as $assignable) {
				$assignables = true; // We have packages to assign!
				$reader = new ReadershipReader((int) $userID);
				if ($reader->hasLoaded)
					if ($reader->assignToPackage((int) $assignable) === false)
						$success = false;
			}
		}

		$readerList->reload();

		if ($success and $assignables and $assignees) $this->messages[] = array('success' =>
			__('New users assigned to subscription packages.', 'readership'));

		elseif ($success === false) $this->messages[] = array('warning' =>
			__('Errors while trying to assign users to subscription packages.', 'readership'));

		else $this->messages[] = array('warning' =>
			__('One or more users and packages must be selected!', 'readership'));
	}


	protected function handleUnattachRequests() {
		$readershipCheck = isset($_POST['readership']) ? $_POST['readership'] : '';
		$wpNonceCheck = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';

		if (wp_verify_nonce($readershipCheck, 'readership') === false and
			wp_verify_nonce($wpNonceCheck, 'deletepackage') )
				return;

		if (isset($_POST['doaction']) and $_POST['actions'] === 'trashselected')
			$unattach = (array) $_POST['selected'];

		if (isset($_GET['do_once']) and $_GET['do_once'] === 'unattach')
			$unattach = (array) $_GET['id'];

		if (empty($unattach)) return;
		else $readerList = ReadershipRegister::readerList();

		$success = true;

		foreach ($unattach as $userID) {
			$reader = $readerList->getReaderByUserID($userID);
			if ($reader->removeFromAllPackages() === false)
				$success = false;
		}

		ReadershipRegister::readerList()->reload();

		if ($success) $this->messages[] = array('success' =>
			__('Users successfully unsubscribed.', 'readership'));

		else $this->messages[] = array('warning' =>
			__('An error occured while unsubscribing users.', 'readership'));
	}
}