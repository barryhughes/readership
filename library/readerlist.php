<?php
class ReadershipReaderList {
	protected $totalReadersInSystem = 0;
	protected $readers = array();


	public function __construct($page = null, $resultsPerPage = 20) {
		$this->loadUserAccounts($page, $resultsPerPage);
		$this->convertToReaderObjects();
	}


	public function reload($offset = null, $limit = null) {
		$this->__construct($offset, $limit);
	}


	protected function loadUserAccounts($page = null, $resultsPerPage = 20) {
		$query = array('meta_key' => 'readership');

		// For result pagination
		if ($page !== null) {
			$query['offset'] = ($page * $resultsPerPage) - $resultsPerPage;
			$query['number'] = $resultsPerPage;
		}

		$userQuery = new WP_User_Query($query);
		$this->readers = (array) $userQuery->get_results();
		$this->totalReadersInSystem = $userQuery->total_users;
	}


	protected function convertToReaderObjects() {
		foreach ($this->readers as &$reader)
			$reader = new ReadershipReader($reader);
	}


	public function getReaders() {
		return $this->readers;
	}


	public function getReaderByUserID($userID) {
		$userID = (int) $userID;

		foreach ($this->readers as $reader) {
			$readerID = $reader->user()->ID;
			if ($reader->user()->ID == $userID)
				return $reader;
		}

		return false;
	}


	public function totalRegisteredReaders() {
		return $this->totalReadersInSystem;
	}


	public function getNonReaders() {
		$users = get_users();

		foreach ($users as $key => $user) {
			$readershipAssignment = get_user_meta($user->ID, 'readership');
			if (empty($readershipAssignment) === false)
				unset($users[$key]);
		}

		return $users;
	}
}