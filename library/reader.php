<?php
class ReadershipReader {
	protected $packageList;
	protected $user;
	protected $meta = array();

	public $hasLoaded = false;


	/**
	 * Builds the reader object. This is essentially a wrapper around the
	 * relevant WP_User object: when constructed, $user can be either a
	 * user ID or an established user object.
	 *
	 * @param mixed $user
	 * @return ReadershipReader
	 */
	public function __construct($user) {
		if (is_numeric($user))
			$this->user = get_userdata((int) $user);

		elseif (is_object($user))
			$this->user = $user;

		if (is_object($this->user))
			$this->hasLoaded = true;

		if ($this->hasLoaded)
			$this->meta = $this->formMetaStructure();

		$this->packageList = ReadershipRegister::packageList();
	}


	protected function formMetaStructure() {
		$meta = (array) get_user_meta($this->user->ID, 'readership');

		if (empty($meta)) $meta = array('activeSubscriptions' => array());
		else $meta = $meta[0];

		return $meta;
	}


	public function assignToPackage($packageID, DateTime $starts = null) {
		$packageID = (int) $packageID;

		if ($this->packageList->getPackage($packageID) === false)
			return false;

		if ($starts === null)
			$starts = new DateTime;

		$this->meta['activeSubscriptions'][$packageID] = $starts;
		return update_user_meta($this->user->ID, 'readership', $this->meta);
	}


	public function listAssignedPackages() {
		return (isset($this->meta['activeSubscriptions']))
			? (array) $this->meta['activeSubscriptions']
			: array();
	}


	public function listUnassignedPackages() {
		$currentlyAssigned = $this->listAssignedPackages();
		$unassigned = array();

		foreach ($this->packageList->getPackages() as $package)
			if (isset($currentlyAssigned[$package->id()]) === false)
				$unassigned[] = $package;

		return $unassigned;
	}


	public function removeFromPackage($packageID) {
		$packageID = (int) $packageID;

		if ($this->packageList->getPackage($packageID) === false)
			return false;

		if (isset($this->meta['activeSubscriptions'][$packageID])) {
			unset($this->meta['activeSubscriptions'][$packageID]);
			return update_user_meta($this->user->ID, 'readership', $this->meta);
		}
	}


	public function removeFromAllPackages() {
		return delete_user_meta($this->user->ID, 'readership');
	}


	public function packageIsCurrent($packageID) {
		$packageID = (int) $packageID;
		$package = $this->packageList->getPackage($packageID);
		if ($package === false) return false;

		if (isset($this->meta['activeSubscriptions'][$packageID])) {
			if ($package->doesNotExpire()) return true;
			if ($this->hasStarted($packageID) === false) return false;

			$packageSub = $this->meta['activeSubscriptions'][$packageID];
			$packageSub->add($package->interval());

			if (ReadershipDate::isInTheFuture($packageSub))
				return true;
		}
		return false;
	}


	public function packageStartDate($packageID) {
		$packageStart = $this->meta['activeSubscriptions'][$packageID];
		return $packageStart;
	}


	protected function hasStarted($packageID) {
		$packageStart = $this->meta['activeSubscriptions'][$packageID];
		return (ReadershipDate::isInTheFuture($packageStart)) ? false : true;
	}


	/**
	 * If the package does not exist (or the user is not assigned to it)
	 * then bool false is returned. If the user is assigned then the number
	 * of days remaining will be returned.
	 *
	 * If the subscription has expired 0 is returned; if the package imposes
	 * no time limit (it never expires) then -1 is returned.
	 *
	 * @param int $packageID
	 * @return mixed bool | int
	 */
	public function timeRemainingOnPackage($packageID) {
		$packageID = (int) $packageID;
		$package = $this->packageList->getPackage($packageID);
		if ($package === false) return false;

		if (isset($this->meta['activeSubscriptions'][$packageID])) {
			if ($package->doesNotExpire()) return -1;

			$packageSub = clone $this->meta['activeSubscriptions'][$packageID];
			$packageSub->add($package->interval());

			if (ReadershipDate::isInTheFuture($packageSub))
				return ReadershipDate::daysLeft($packageSub);

			else return 0;
		}
		return false;
	}


	public function timeUntilPackageStarts($packageID) {
		$packageID = (int) $packageID;
		$package = $this->packageList->getPackage($packageID);
		if ($package === false) return false;

		if (isset($this->meta['activeSubscriptions'][$packageID])) {
			$packageSub = $this->meta['activeSubscriptions'][$packageID];
			$packageSub->diff($package->interval());

			if (ReadershipDate::isInTheFuture($packageSub))
				return ReadershipDate::daysLeft($packageSub) + 1;

			else return 0;
		}
		return false;
	}


	public function package($packageID) {
		return $this->packageList->getPackage((int) $packageID);
	}


	public function user() {
		return $this->user;
	}


	public static function automaticSubscriptions() {
		if (ReadershipSettings::autoSubscribe() === false) return;
		add_action('user_register', array(__CLASS__, 'autoAssignPackage'));
	}


	public static function autoAssignPackage($userID) {
		$newReader = new ReadershipReader($userID);

		if ($newReader->user()->has_cap('read'))
			$newReader->assignToPackage(ReadershipSettings::defaultPackage());
	}


	public static function sessionMarking() {
		if (isset($_SESSION) === false) session_start();
		add_action('init', array(__CLASS__, 'markSession'));
	}


	public static function markSession() {
		$reader = new ReadershipReader(wp_get_current_user());
		$_SESSION['readershipSubscription'] = $reader->listAssignedPackages();
	}
}