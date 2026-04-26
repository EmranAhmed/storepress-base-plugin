<?php
	/**
	 * Deactivation Feedback Integration.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	namespace StorePress\Base\Integrations;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractDeactivationFeedback;
	use StorePress\AdminUtils\Traits\SingletonTrait;
	use StorePress\Base\Services\Settings;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Handles the deactivation feedback dialog for the plugin.
	 *
	 * @name DeactivationFeedback
	 */
class DeactivationFeedback extends AbstractDeactivationFeedback {

	use SingletonTrait;
	use PluginUtilityTrait;

	/**
	 * Get deactivation title.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function title(): string {
		$name = $this->get_plugin_name();
		/* translators: %s: Plugin name. */
		return sprintf( esc_html__( 'QUICK FEEDBACK from %s', 'storepress-base-plugin' ), esc_html( $name ) );
	}

	/**
	 * Returns the dialog sub-title text.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function sub_title(): string {
		return esc_html__( 'May we have a little info about why you are deactivating?', 'storepress-base-plugin' );
	}

	/**
	 * Set API URL to send feedback.
	 *
	 * @return string
	 * @since 1.0.0
	 * @example https://example.com/wp-json/__NAMESPACE__/v1/deactivate
	 */
	public function api_url(): string {
		return 'https://example.com/storepress-admin-utils/wp-json/feedback/v1/deactivate';
	}

	/**
	 * Get saved settings data.
	 *
	 * @return array<string, mixed>
	 * @since 1.0.0
	 */
	public function options(): array {
		return $this->get_container()->get( Settings::class )->get_options();
	}

	/**
	 * Returns the dialog action buttons configuration.
	 *
	 * @return array<int, array<string, mixed>>
	 * @since 1.0.0
	 */
	public function get_buttons(): array {

		return array(
			array(
				'type'       => 'button',
				'label'      => esc_html__( 'Send feedback & Deactivate', 'storepress-base-plugin' ),
				'attributes' => array(
					'disabled'        => true,
					'type'            => 'submit',
					'data-action'     => 'submit',
					'data-label'      => esc_html__( 'Send feedback & Deactivate', 'storepress-base-plugin' ),
					'data-processing' => esc_html__( 'Deactivate...', 'storepress-base-plugin' ),
					'class'           => array( 'button', 'button-primary' ),
				),
				'spinner'    => true,
			),
			array(
				'type'       => 'link',
				'label'      => esc_html__( 'Skip & Deactivate', 'storepress-base-plugin' ),
				'attributes' => array(
					'href'  => '#',
					'class' => array( 'skip-deactivate' ),
				),
			),
		);
	}

	/**
	 * Returns the deactivation reason options shown in the dialog.
	 *
	 * @return array<string, array<string, mixed>>
	 * @since 1.0.0
	 */
	public function get_reasons(): array {
		$current_user = wp_get_current_user();
		$name         = $this->get_plugin_name();

		return array(
			'temporary_deactivation'        => array(
				'title' => esc_html__( 'It\'s a temporary deactivation.', 'storepress-base-plugin' ),
			),

			'dont_know_about'               => array(
				'title'   => esc_html__( 'I couldn\'t understand how to make it work.', 'storepress-base-plugin' ),
				/* translators: %s: Plugin name. */
				'message' => sprintf( esc_html__( 'Its Plugin %s.', 'storepress-base-plugin' ), esc_html( $name ) ),
			),

			'no_longer_needed'              => array(
				'title' => esc_html__( 'I no longer need the plugin.', 'storepress-base-plugin' ),
			),

			'found_a_better_plugin'         => array(
				'title' => esc_html__( 'I found a better plugin.', 'storepress-base-plugin' ),
				'input' => array(
					'placeholder' => esc_html__( 'Please let us know which one', 'storepress-base-plugin' ),
				),
			),

			'broke_site_layout'             => array(
				'title'   => __( 'The plugin <strong>broke my layout</strong> or some functionality.', 'storepress-base-plugin' ),
				'message' => __( '<a target="_blank" href="https://getwooplugins.com/tickets/">Please open a support ticket</a>, we will fix it immediately.', 'storepress-base-plugin' ),
			),

			'plugin_setup_help'             => array(
				'title'   => __( 'I need someone to <strong>setup this plugin.</strong>', 'storepress-base-plugin' ),
				'input'   => array(
					'placeholder' => esc_html__( 'Your email address.', 'storepress-base-plugin' ),
					'value'       => sanitize_email( $current_user->user_email ),
				),
				'message' => __( 'Please provide your email address to contact with you <br>and help you to set up and configure this plugin.', 'storepress-base-plugin' ),
			),

			'plugin_config_too_complicated' => array(
				'title'   => __( 'The plugin is <strong>too complicated to configure.</strong>', 'storepress-base-plugin' ),
				'message' => __( '<a target="_blank" href="https://getwooplugins.com/documentation/woocommerce-variation-swatches/">Have you checked our documentation?</a>.', 'storepress-base-plugin' ),
			),

			'need_specific_feature'         => array(
				'title' => esc_html__( 'I need specific feature that you don\'t support.', 'storepress-base-plugin' ),

				'input' => array(
					'placeholder' => esc_html__( 'Please share with us.', 'storepress-base-plugin' ),
				),
			),

			'other'                         => array(
				'title' => esc_html__( 'Other', 'storepress-base-plugin' ),
				'input' => array(
					'placeholder' => esc_html__( 'Please share the reason', 'storepress-base-plugin' ),
				),
			),
		);
	}
}
