<?php
class ReadershipSettings {
	protected static $supportedTypes = null;


	public static function getPublicPostTypes() {
		global $wp_post_types;
		$postTypes = array();

		foreach ($wp_post_types as $id => $config)
			if ($config->public and $config->show_ui) $postTypes[$id] = $config;

		return $postTypes;
	}


	public static function supportPostTypes(array $types) {
		$supportable = array_keys(self::getPublicPostTypes());
		$supportedTypes = array();

		foreach ($types as $type)
			if (in_array($type, $supportable))
				$supportedTypes[] = $type;

		update_option('readershipTypeSupport', $supportedTypes);
	}


	public static function getSupportedPostTypes() {
		if (self::$supportedTypes === null)
			self::$supportedTypes = (array) get_option('readershipTypeSupport');

		return self::$supportedTypes;
	}


	public static function isSupportedType($postType) {
		$supportedTypes = self::getSupportedPostTypes();
		return in_array($postType, $supportedTypes);
	}


	public static function defaultPackage($packageID = null) {
		if ($packageID === null)
			return get_option('readershipDefaultPackage');

		else update_option('readershipDefaultPackage', $packageID);
	}


	public static function autoSubscribe($boolToggle = null) {
		if ($boolToggle === null)
			return (bool) get_option('readershipAutoSubscribe');

		else update_option('readershipAutoSubscribe', (bool) $boolToggle);
	}


	public static function sessionMarker($boolToggle = null) {
		if ($boolToggle === null)
			return (bool) get_option('readershipSessionMarker');

		else update_option('readershipSessionMarker', (bool) $boolToggle);
	}
}