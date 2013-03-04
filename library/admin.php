<?php
class ReadershipAdmin {
	protected $menuItems = array();
	protected $subPages = array();


	public function __construct() {
		$this->subPageManagement();
		add_action('admin_init', array($this, 'registerAssets'));
		add_action('admin_print_styles-post.php', array($this, 'enqueueAssets'));
		add_action('admin_menu', array($this, 'menuEntries'));
	}


	protected function subPageManagement() {
		$this->menuItems = (array) apply_filters('readershipAdminMenu', array(
			__('Readers', 'readership') => array('subpage' => 'readers'),
			__('Packages', 'readership') => array('subpage' => 'packages'),
			__('Settings &amp; Tools', 'readership') => array('subpage' => 'tools-settings')
		));
		$this->subPages = (array) apply_filters('readershipSubPages', array(
			'readers' => array($this, 'readersSubPage'),
			'packages' => array($this, 'packagesSubPage'),
			'tools-settings' => array($this, 'settingsSubPage')
		));
	}


	public function menuEntries() {
		$label = __('Readership', 'readership');
		$handle = add_users_page($label, $label, 'list_users', 'readership', array($this, 'readersPage'));
		add_action('admin_print_styles-'.$handle, array($this, 'enqueueAssets'));
	}


	public function registerAssets() {
		wp_register_style('readershipAdminStyle',
			Readership::$pluginURL.'/assets/admin.css');

		wp_register_script('readershipAdminScript',
			Readership::$pluginURL.'/assets/admin.js');
	}


	public function enqueueAssets() {
		wp_enqueue_style('readershipAdminStyle');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('readershipAdminScript');
	}


	public function readersPage() {
		$menu = $this->formMenu();
		$page = $this->loadSubPage($menu->highlighted);
		if (isset($_GET['do_once'])) unset($_GET['do_once']);

		$action = get_admin_url(null, $GLOBALS['pagenow'].'?'.http_build_query($_GET));
		return Readership::view('wrapper', array(
			'menu' => $menu, 'page' => $page, 'formAction' => $action));
	}


	protected function loadSubPage($current) {
		$current = array_key_exists('subpage', $_GET)
			? $_GET['subpage'] : '';

		if (array_key_exists($current, $this->subPages))
			return call_user_func($this->subPages[$current]);

		$firstSubPage = array_shift($this->subPages);
		return call_user_func($firstSubPage);
	}


	protected function formMenu() {
		global $pagenow;

		// Complete the links in the menuItems array
		foreach ($this->menuItems as &$link) {
			$query = array('page' => $_GET['page']);
			$query = array_merge($query, $link);
			$link = get_admin_url(null, "$pagenow?".http_build_query($query));
		}

		return (object) array(
			'items' => $this->menuItems,
			'highlighted' => $this->currentMenuItem()
		);
	}


	protected function currentMenuItem() {
		if (array_key_exists('subpage', $_GET))
			foreach ($this->menuItems as $key => $url) {
				$lookingFor = "subpage=".$_GET['subpage'];
				if (strpos($url, $lookingFor) > 0)
					return $url;
			}

		$firstMenuItem = array_values(array_slice($this->menuItems, 0, 1));
		return $firstMenuItem[0];
	}


	public function readersSubPage() {
		$controller = new ReadershipReaderAdmin;
		return $controller->output;
	}


	public function packagesSubPage() {
		$controller = new ReadershipPackageAdmin;
		return $controller->output;
	}


	public function settingsSubPage() {
		$controller = new ReadershipSettingsAdmin;
		return $controller->output;
	}


	public static function getActionLink(array $parameters) {
		$query = array();
		$query['page'] = $_GET['page'];
		if (array_key_exists('subpage', $_GET)) $query['subpage'] = $_GET['subpage'];
		$query = array_merge($query, $parameters);

		return get_admin_url(null, $GLOBALS['pagenow'].'?'.http_build_query($query));
	}


	public static function getAction($default = '') {
		return array_key_exists('action', $_GET) ? $_GET['action'] : $default;
	}
}