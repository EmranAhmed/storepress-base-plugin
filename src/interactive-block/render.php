<?php
/**
 * Dynamic Block Template.
 *
 * @package    StorePress/Base
 * @since      1.0.0
 * @version    1.0.0
 */

declare( strict_types=1 );

namespace StorePress\Base;

use WP_Block;

defined( 'ABSPATH' ) || die( 'Keep Silent' );

/**
 * The following variables are exposed to the file:
 *
 * @var string[] $attributes -  A clean associative array of block attributes.
 * @var WP_Block $block      - The block instance. All the block settings and attributes.
 * @var string   $content    - The block inner HTML (usually empty unless using inner blocks).
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Generate unique id for aria-controls.
$storepress_unique_id = uniqid( 'p-' );


$storepress_classes = array(
	'class-01' => true,
);

$storepress_styles = array(
	'--css-variable' => $attributes['x'] . '%',
);

$storepress_context = $block->context;

$storepress_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => esc_attr(
			storepress_base_plugin()->get_blocks()
													->get_css_classes( $storepress_classes )
		),
		'style' => esc_attr(
			storepress_base_plugin()->get_blocks()
													->get_inline_styles( $storepress_styles )
		),
	)
);
$storepress_allowed_html       = storepress_base_plugin()->get_blocks()
														->get_kses_allowed_html();
?>

<div
<?php
echo wp_kses_data( $storepress_wrapper_attributes );
?>
data-wp-interactive="storepress"
	<?php
		echo wp_kses_data( wp_interactivity_data_wp_context( array( 'isOpen' => false ) ) );
	?>
	data-wp-watch="callbacks.logIsOpen">
	<h1>Z Name – hello from render.php</h1>
	<button data-wp-on--click="actions.toggle" data-wp-bind--aria-expanded="context.isOpen" aria-controls="
	<?php
	echo esc_attr( $storepress_unique_id );
	?>
	">
		<?php
		esc_html_e( 'Toggle', 'storepress-base-plugin' );
		?>
	</button>

	<div id="
	<?php
	echo esc_attr( $storepress_unique_id );
	?>
	" data-wp-bind--hidden="!context.isOpen">
		<?php
		echo wp_kses( $content, $storepress_allowed_html );
		?>
	</div>
</div>
