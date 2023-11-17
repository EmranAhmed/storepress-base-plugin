<?php
	/**
	 * Plugin Class.
	 *
	 * @package    StorePress
	 * @subpackage Base
	 */

	namespace StorePress\Base;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	/**
	 * Class Plugin.
	 */
class Plugin {

	/**
	 * Class Instance.
	 *
	 * @var Plugin
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
		 * @param Plugin $this Plugin Object.
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
	 * Plugin File.
	 *
	 * @return string
	 */
	public function get_plugin_file() {
		return STOREPRESS_BASE_PLUGIN_FILE;
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
	 * @return Plugin
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
	 * @return bool
	 * @throws \Exception When class files loading fails.
	 */
	public function includes() {

		if ( file_exists( $this->vendor_path() . '/autoload.php' ) ) {
			include_once $this->vendor_path() . '/autoload.php';

			return true;
		}

		throw new \Exception( 'vendor/autoload.php missing please run `composer install`' );
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
		return basename( dirname( $this->get_plugin_file() ) );
	}

	/**
	 * Get Plugin basename
	 */
	public function plugin_basename() {
		return plugin_basename( $this->get_plugin_file() );
	}

	/**
	 * Get Plugin directory name
	 */
	public function plugin_dirname() {
		return dirname( plugin_basename( $this->get_plugin_file() ) );
	}

	/**
	 * Get Plugin directory path
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( $this->get_plugin_file() ) );
	}

	/**
	 * Get Plugin directory url
	 */
	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( $this->get_plugin_file() ) );
	}

	/**
	 * Get Plugin image url
	 */
	public function images_url() {
		return untrailingslashit( plugin_dir_url( $this->get_plugin_file() ) . 'images' );
	}

	/**
	 * Get Asset URL
	 */
	public function assets_url() {
		return untrailingslashit( plugin_dir_url( $this->get_plugin_file() ) . 'assets' );
	}

	/**
	 * Get Asset path
	 */
	public function assets_path() {
		return $this->plugin_path() . '/assets';
	}

	/**
	 * Get Vendor path
	 */
	public function vendor_path() {
		return $this->plugin_path() . '/vendor';
	}

	/**
	 * Get Vendor URL
	 */
	public function vendor_url() {
		return untrailingslashit( plugin_dir_url( $this->get_plugin_file() ) . 'vendor' );
	}

	/**
	 * Get Build URL
	 */
	public function build_url() {
		return untrailingslashit( plugin_dir_url( $this->get_plugin_file() ) . 'build' );
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
		return untrailingslashit( plugin_dir_path( $this->get_plugin_file() ) . 'includes' );
	}

	/**
	 * Get Blocks
	 */
	public function get_blocks() {

		if ( ! class_exists( '\StorePress\Base\Blocks' ) ) {
			return false;
		}

		return \StorePress\Base\Blocks::instance();
	}
}
