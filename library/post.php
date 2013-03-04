<?php
class ReadershipPost {
	protected $id = 0;


	public function __construct($id = null) {
		global $post;

		if ($id === null and $post !== null)
			$id = $post->ID;

		$this->id = (int) $id;
	}


	public function packages(array $packages = null) {
		if ($packages === null) {
			return (array) get_post_meta($this->id, 'readershipSubscription', true);
		}
		else {
			$packageList = ReadershipRegister::packageList();
			$allocated = array();

			foreach ($packages as $package)
				if ($packageList->getPackage($package) !== false)
					$allocated[] = $package;

			update_post_meta($this->id, 'readershipSubscription', $allocated);
		}
	}


	public function isProtected() {
		$packages = $this->packages();
		return (empty($packages) or empty($packages[0])) ? false : true;
	}
}