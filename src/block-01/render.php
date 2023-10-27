<?php
	/**
	 * Dynamic Block Template.
	 *
	 * @global   array  $attributes - A clean associative array of block attributes.
	 * @global   array  $block      - All the block settings and attributes.
	 * @global   string $content    - The block inner HTML (usually empty unless using inner blocks).
	 */
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?>>
	<?php echo $content; ?>
</div>
