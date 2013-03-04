<?php
class ReadershipControl {
	protected $packageList;
	protected $messages = array();


	public function __construct() {
		$this->packageList = ReadershipRegister::packageList();
		$this->setupEditorControls();
		$this->enforceAccessPolicy();
	}


	protected function setupEditorControls() {
		add_action('add_meta_boxes', array($this, 'postMetabox'));
		add_action('save_post', array($this, 'updatePostSetting'));
	}


	public function postMetabox() {
		foreach (ReadershipSettings::getSupportedPostTypes() as $type)
			add_meta_box('readershipContentControl', __('Readership Control', 'readership'),
				array($this, 'renderMetaBox'), $type,
				apply_filters('readershipMetaboxPosition', 'side'),
				apply_filters('readershipMetaboxPriority', 'high')
			);
	}


	public function renderMetaBox() {
		$post = new ReadershipPost;

		$vars = array(
			'packages' => $this->packageList->getPackages(),
			'post' => $post,
			'allocatedPackages' => $post->packages(),
			'messages' => $this->messages
		);

		Readership::view('postmetabox', $vars);
	}


	public function updatePostSetting($postID) {
		if (defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return;

		if (!wp_verify_nonce($_POST['subscriptionSettings'], 'readershipPostMeta')) return;
		elseif (!current_user_can('edit_post', $postID)) return;

		$post = new ReadershipPost($postID);
		$post->packages((array) $_POST['postreadershippackages']);
	}


	protected function enforceAccessPolicy() {
		add_filter('the_content', array($this, 'vetContent'));
		#add_action('wp_enqueue_scripts', array($this, 'enqueue_requested_scripts'));
	}


	public function vetContent($content) {
		$post = new ReadershipPost($GLOBALS['post']->ID);
		$user = new ReadershipReader(wp_get_current_user());

		$status = $post->isProtected();

		if ($status === false)
			return $content;

		if (is_user_logged_in() === false and $status)
			return $this->censoredContent();

		foreach ($post->packages() as $package)
			if ($user->packageIsCurrent($package))
				return $content;

		return $this->censoredContent();
	}


	protected function censoredContent() {
		return apply_filters('readershipCensoredPost',
			__('You must be logged in and have an active subscription to '
			.'view this post.', 'readership'));
	}
}