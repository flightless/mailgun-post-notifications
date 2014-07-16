<?php


namespace Mailgun_Post_Notifications;


class Plugin {
	const VERSION = '1.0';

	/** @var Plugin */
	private static $instance = NULL;

	private static $plugin_file = '';

	/** @var Admin_Page */
	private $admin_page = NULL;

	public function admin() {
		return $this->admin_page;
	}

	private function setup( $plugin_file ) {
		self::$plugin_file = $plugin_file;
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
		if ( is_admin() ) {
			$this->setup_admin_page();
		}

		if ( !is_admin() ) {

		}
	}


	private function setup_admin_page() {
		$this->admin_page = new Admin_Page();
		add_action( 'admin_menu', array( $this->admin_page, 'register' ), 11, 0 );
	}

	public static function init( $file ) {
		self::instance()->setup( $file );
	}

	public static function instance() {
		if ( !isset(self::$instance) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function autoload( $class ) {
		if (substr($class, 0, strlen(__NAMESPACE__)) != __NAMESPACE__) {
			//Only autoload libraries from this package
			return;
		}
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
		$path = dirname(self::$plugin_file) . DIRECTORY_SEPARATOR . $path . '.php';
		if (file_exists($path)) {
			require $path;
		}
	}
} 