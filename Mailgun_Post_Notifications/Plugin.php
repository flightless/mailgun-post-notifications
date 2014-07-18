<?php


namespace Mailgun_Post_Notifications;


class Plugin {
	const VERSION = '1.0';

	/** @var Plugin */
	private static $instance = NULL;

	private static $plugin_file = '';

	/** @var Admin_Page */
	private $admin_page = NULL;

	/** @var Notifier */
	private $notifier = NULL;

	public function admin() {
		if ( !isset($this->admin_page) ) {
			$this->admin_page = new Admin_Page();
		}
		return $this->admin_page;
	}

	public function notifier() {
		if ( !isset($this->notifier) ) {
			$this->notifier = new Notifier();
		}
		return $this->notifier;
	}

	private function setup( $plugin_file ) {
		self::$plugin_file = $plugin_file;
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
		if ( is_admin() ) {
			$this->setup_admin_page();
		}

		$this->setup_notification_listener();
	}


	private function setup_admin_page() {
		add_action( 'admin_menu', array( $this->admin(), 'register' ), 11, 0 );
	}

	private function setup_notification_listener() {
		add_action( 'save_post', array( $this->notifier(), 'listen_for_saved_post' ), 10, 2 );
		add_action( 'shutdown', array( $this->notifier(), 'send_notifications' ), 0, 0 );
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
		$path = self::path() . DIRECTORY_SEPARATOR . $path . '.php';
		if (file_exists($path)) {
			require $path;
		}
	}


	/**
	 * Get the absolute system path to the plugin directory, or a file therein
	 * @static
	 * @param string $path
	 * @return string
	 */
	public static function path( $path = '' ) {
		$base = dirname(self::$plugin_file);
		if ( $path ) {
			return trailingslashit($base).$path;
		} else {
			return untrailingslashit($base);
		}
	}

	/**
	 * Get the absolute URL to the plugin directory, or a file therein
	 * @static
	 * @param string $path
	 * @return string
	 */
	public static function url( $path = '' ) {
		return plugins_url($path, self::$plugin_file);
	}
} 