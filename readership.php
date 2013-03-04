<?php
/*
Plugin Name: Readership
Version: 0.6.2
Description: Allows access to premium or restricted content to be controlled and managed.
Author: Barry Hughes
Author URI: http://freshlybakedwebsites.net/

	Readership: members only plugin for WordPress
    Copyright (C) 2012 Barry Hughes

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


class Readership
{
	const VERSION = '0.5.0';

	protected $classmap = array();
	protected $settings = array();

	public static $pluginDir;
	public static $pluginURL;
	public $users;


	public function __construct() {
		$this->initialize();
		$this->loadComponents();
	}


	protected function initialize() {
		self::$pluginDir = dirname(__FILE__);
		self::$pluginURL = WP_PLUGIN_URL.'/'.basename(dirname(__FILE__));
		$this->setupClassLoader();
	}


	protected function setupClassLoader() {
		$this->classmap = $this->loadConfigArray('classmap');
		spl_autoload_register(array($this, 'loader'));
	}


	protected function loader($class) {
		if (array_key_exists($class, $this->classmap)) {
			$path = self::$pluginDir.'/'.$this->classmap[$class];
			if (file_exists($path)) include $path;
		}
	}


	protected function loadComponents() {
		new ReadershipAdmin;
		new ReadershipControl;
		ReadershipReader::automaticSubscriptions();
		ReadershipReader::sessionMarking();
	}


	public function loadConfigArray($name) {
		return include self::$pluginDir."/config/$name.php";
	}


	public static function view($view, array $vars = null, $echo = true) {
		if ($vars !== null) extract($vars);
		if ($echo === false) ob_start();
		include Readership::$pluginDir."/views/$view.php";
		if ($echo === false) return ob_get_clean();
	}
}


new Readership;