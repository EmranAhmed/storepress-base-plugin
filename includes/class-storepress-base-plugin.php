<?php
	/**
	 * Plugin Class.
	 *
	 * @package    StorePress
	 * @subpackage Base
	 */


	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	/**
	 * Class Plugin.
	 */
class StorePress_Base_Plugin {

	/**
	 * Class Instance.
	 *
	 * @var StorePress_Base_Plugin
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->includes();
		$this->hooks();
		$this->init();

		/**
		 * Action to signal that Plugin has finished loading.
		 *
		 * @param StorePress_Base_Plugin $this Plugin Object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'storepress_base_plugin_loaded', $this );
	}

	/**
	 * Plugin Version.
	 *
	 * @return string
	 */
	public function version() {
		return esc_attr( STOREPRESS_BASE_PLUGIN_VERSION );
	}

	/**
	 * Set constant if not defined and prevent reassign
	 *
	 * @param string $name  Constant name.
	 * @param array  $value Constant value.
	 *
	 * @return void.
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Instance.
	 *
	 * @return StorePress_Base_Plugin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Includes.
	 *
	 * @return void
	 * @throws Exception When class files loading fails.
	 */
	public function includes() {
		$loader = include_once $this->plugin_path() . '/vendor/autoload.php';

		if ( ! $loader ) {
			throw new Exception( 'vendor/autoload.php missing please run `composer install`' );
		}
	}

	/**
	 * Initialize
	 */
	public function init() {

		// Setup BLocks.
		$this->get_blocks();

		// Set up cache management.
		// new Extension_Cache();.

		// Initialize REST API.
		// new Extension_REST_API();.

		// Set up email management.
		// new Extension_Email_Manager();.
	}

	/**
	 * Hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		// Register with hook.
		add_action( 'init', array( $this, 'language' ), 1 );
	}

	/**
	 * Language
	 */
	public function language() {
		load_plugin_textdomain( 'storepress-base-plugin', false, $this->plugin_path() . '/languages' );
	}

	/**
	 * Get Plugin basename directory name
	 */
	public function basename() {
		return basename( dirname( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin basename
	 */
	public function plugin_basename() {
		return plugin_basename( STOREPRESS_BASE_PLUGIN_FILE );
	}

	/**
	 * Get Plugin directory name
	 */
	public function plugin_dirname() {
		return dirname( plugin_basename( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin directory path
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin directory url
	 */
	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) );
	}

	/**
	 * Get Plugin image url
	 */
	public function images_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) . 'images' );
	}


	/**
	 * Get WordPress.org asset url
	 *
	 * @param string $file Asset file name.
	 *
	 * @return string WordPress.org file url
	 */
	public function org_assets_url( $file = '' ) {
		return 'https://ps.w.org/storepress-base-plugin/assets' . $file . '?ver=' . $this->version();
	}

	/**
	 * Get Asset URL
	 */
	public function assets_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) . 'assets' );
	}

	/**
	 * Get Asset path
	 */
	public function assets_path() {
		return $this->plugin_path() . '/assets';
	}

	/**
	 * Get Build URL
	 */
	public function build_url() {
		return untrailingslashit( plugin_dir_url( STOREPRESS_BASE_PLUGIN_FILE ) . 'build' );
	}

	/**
	 * Get Build path
	 */
	public function build_path() {
		return $this->plugin_path() . '/build';
	}

	/**
	 * Get Asset version
	 *
	 * @param string $file Asset file name.
	 *
	 * @return numeric asset file make time.
	 */
	public function assets_version( $file ) {
		return filemtime( $this->assets_path() . $file );
	}

	/**
	 * Get Include path
	 */
	public function include_path() {
		return untrailingslashit( plugin_dir_path( STOREPRESS_BASE_PLUGIN_FILE ) . 'includes' );
	}

	/**
	 * Get Blocks
	 */
	public function get_blocks() {
		return \StorePress\Base\Blocks::instance();
	}
}
