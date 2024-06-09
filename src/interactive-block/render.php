<?php
	/**
	 * Dynamic Block Template.
	 *
	 * @package    StorePress/Base
	 * @since 1.0.0
	 * @version 1.0.0
	 */

	namespace StorePress\Base;

	use WP_Block;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	/**
	 * The following variables are exposed to the file:
	 *
	 * @global   array    $attributes -  A clean associative array of block attributes.
	 * @global   WP_Block $block      - The block instance. All the block settings and attributes.
	 * @global   string   $content    - The block inner HTML (usually empty unless using inner blocks).
	 *
	 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
	 */

// Generate unique id for aria-controls.
$storepress_unique_id = wp_unique_id( 'p-' );
?>

<div
	<?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>
	data-wp-interactive="storepress"
	<?php echo wp_kses_data( wp_interactivity_data_wp_context( array( 'isOpen' => false ) ) ); ?>
	data-wp-watch="callbacks.logIsOpen">
	<button
		data-wp-on--click="actions.toggle"
		data-wp-bind--aria-expanded="context.isOpen"
		aria-controls="<?php echo esc_attr( $storepress_unique_id ); ?>">
		<?php esc_html_e( 'Toggle', 'storepress-base-plugin' ); ?>
	</button>

	<p id="<?php echo esc_attr( $storepress_unique_id ); ?>" data-wp-bind--hidden="!context.isOpen">
		<?php
			esc_html_e( 'Example Interactive - hello from an interactive block!', 'storepress-base-plugin' );
		?>
	</p>
</div>
