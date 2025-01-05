<?php
/**
 * Common Methods for Classes.
 *
 * @package      StorePress/Base
 * @since        1.0.0
 * @version      1.0.0
 */

declare( strict_types=1 );

namespace StorePress\Base;

defined( 'ABSPATH' ) || die( 'Keep Silent' );

trait Common {

	/**
	 * Prints human-readable information about a variable.
	 *
	 * Some server environments block some debugging functions. This function provides a safe way to
	 * turn an expression into a printable, readable form without calling blocked functions.
	 *
	 * @param mixed $expression   The expression to be printed.
	 * @param bool  $return_value Optional. Default false. Set to true to return the human-readable string.
	 *
	 * @return string|bool False if expression could not be printed. True if the expression was printed.
	 *     If $return is true, a string representation will be returned.
	 * @since 1.0.0
	 */
	public function print_r( $expression, bool $return_value = false ) {
		$alternatives = array(
			array(
				'func' => 'print_r',
				'args' => array( $expression, true ),
			),
			array(
				'func' => 'var_export',
				'args' => array( $expression, true ),
			),
			array(
				'func' => 'json_encode',
				'args' => array( $expression ),
			),
			array(
				'func' => 'serialize',
				'args' => array( $expression ),
			),
		);

		foreach ( $alternatives as $alternative ) {
			if ( function_exists( $alternative['func'] ) ) {
				$res = $alternative['func']( ...$alternative['args'] );
				if ( $return_value ) {
					return $res; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				echo $res; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				return true;
			}
		}

		return false;
	}

	/**
	 * Get data if set, otherwise return a default value or null. Prevents notices when data is not set.
	 *
	 * @param mixed       $variable      Variable.
	 * @param string|null $default_value Default value.
	 *
	 * @return mixed
	 * @since  1.0.0
	 */
	public function get_var( $variable, string $default_value = null ) {
		return true === isset( $variable ) ? $variable : $default_value;
	}

	/**
	 * Create HTML Attributes from given array
	 *
	 * @param array<string, mixed> $attributes Attribute array.
	 * @param string[]             $exclude    Exclude attribute. Default array.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_html_attributes( array $attributes, array $exclude = array() ): string {
		$attrs = array();

		foreach ( $attributes as $attribute_name => $attribute_value ) {
			// Exclude attribute.
			if ( in_array( $attribute_name, $exclude, true ) ) {
				continue;
			}

			// Skip if attribute value is blank.
			if ( is_string( $attribute_value ) && $this->is_empty_string( $attribute_value ) ) {
				continue;
			}

			// Skip if attribute value is null.
			if ( is_null( $attribute_value ) ) {
				continue;
			}

			// Skip if attribute value is boolean false.
			if ( false === $attribute_value ) {
				continue;
			}

			// If attribute is class and value is array.
			if ( is_array( $attribute_value ) ) {
				if ( 'class' === $attribute_name ) {
					$attribute_value = $this->get_css_classes( $attribute_value );
				} else {
					$attribute_value = wp_json_encode( $attribute_value );
				}
			}

			// If attribute is boolean true only use attribute name.
			if ( true === $attribute_value ) {
				$attrs[] = sprintf( '%s', esc_attr( $attribute_name ) );
				continue;
			}

			$attrs[] = sprintf( '%s="%s"', esc_attr( $attribute_name ), esc_attr( $attribute_value ) );
		}

		return implode( ' ', array_unique( $attrs ) );
	}


	/**
	 * Generate Inline Style from array
	 *
	 * @param array<string, mixed> $inline_styles_array Inline style as array.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_inline_styles( array $inline_styles_array = array() ): string {
		$styles = array();

		foreach ( $inline_styles_array as $property => $value ) {
			if ( is_null( $value ) ) {
				continue;
			}
			if ( is_bool( $value ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				continue;
			}

			if ( is_string( $value ) && $this->is_empty_string( $value ) ) {
				continue;
			}

			$styles[] = sprintf( '%s: %s;', esc_attr( $property ), esc_attr( $value ) );
		}

		return implode( ' ', array_unique( $styles ) );
	}

	/**
	 * Array to css class.
	 *
	 * @param array<int|string, ?mixed> $classes_array css classes array.
	 *
	 * @return string
	 * @since  1.0.0
	 * @example
	 * <code>
	 *   ['class-a', 'class-b']
	 *   // or
	 *   ['class-a'=>true, 'class-b'=>false, 'class-c'=>'', 'class-e'=>null, 'class-d'=>'hello']
	 * </code>
	 */
	public function get_css_classes( array $classes_array = array() ): string {
		$classes = array();
		foreach ( $classes_array as $class_name => $should_include ) {
			// Is class assign by numeric array. Like: ['class-a', 'class-b'].
			if ( is_int( $class_name ) ) {
				if ( ! is_string( $should_include ) ) {
					continue;
				}

				if ( $this->is_empty_string( $should_include ) ) {
					continue;
				}

				$classes[] = $should_include;
				continue;
			}

			if ( false === $should_include ) {
				continue;
			}

			if ( is_string( $should_include ) && $this->is_empty_string( $should_include ) ) {
				continue;
			}

			if ( is_null( $should_include ) ) {
				continue;
			}

			if ( is_array( $should_include ) && $this->is_empty_array( $should_include ) ) {
				continue;
			}

			// Is class assign by associative array.
			// Like: ['class-a'=>true, 'class-b'=>false, class-c'=>'', 'class-d'=>'hello', 'class-x'=>null, 'class-y'=>array()].
			$classes[] = $class_name;
		}

		return implode( ' ', array_unique( $classes ) );
	}

	/**
	 * Check is string is empty.
	 *
	 * @param string $check_value Check value.
	 *
	 * @return bool
	 */
	public function is_empty_string( string $check_value = '' ): bool {
		return 0 === strlen( trim( $check_value ) );
	}

	/**
	 * Check is array is all empty values.
	 *
	 * @param array<int|string, ?mixed> $items Check array.
	 *
	 * @return bool
	 */
	public function is_array_each_empty_value( array $items = array() ): bool {
		$checked = array_map(
			function ( $value ) {
				if ( is_array( $value )
					&& ! $this->is_array_each_empty_value( $value )
				) {
					return true;
				}

				if ( is_string( $value ) && ! $this->is_empty_string( $value ) ) {
					return true;
				}

				if ( true === $value ) {
					return true;
				}

				return false;
			},
			$items
		);

		return ! in_array( true, array_unique( $checked ), true );
	}

	/**
	 * Check numeric array is empty.
	 *
	 * @param array<int|string, ?mixed> $items Check array.
	 *
	 * @return bool
	 */
	public function is_empty_array( array $items = array() ): bool {
		return 0 === count( $items );
	}

	/**
	 * Converts a bool to a 'yes' or 'no'.
	 *
	 * @param mixed $check_value Bool to convert. If a string is passed it will first be converted to a bool.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function boolean_to_string( $check_value ): string {
		$value = $this->string_to_boolean( $check_value );

		return true === $value ? 'yes' : 'no';
	}

	/**
	 * Converts a string (e.g. 'yes' or 'no') to a bool.
	 * Recognizing words like Yes, No, Off, On, both string and native types of true and false,
	 * and is not case-sensitive when validating strings.
	 *
	 * @param mixed $check_value String to convert. If a bool is passed it will be returned as-is.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function string_to_boolean( $check_value ): bool {
		return filter_var( $check_value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Checks whether a given array is a list.
	 * An array is considered a list if its keys consist of consecutive numbers from `0 to count($array)-1`
	 *
	 * @param array<int|string, ?mixed> $items Check array.
	 *
	 * @return bool
	 * @example
	 *             <code>
	 *             array_is_list([]); // true
	 *             array_is_list(['apple', 2, 3]); // true
	 *             array_is_list([0 => 'apple', 'orange']); // true
	 *
	 *             // The array does not start at 0
	 *              array_is_list([1 => 'apple', 'orange']); // false
	 *
	 *              // The keys are not in the correct order
	 *              array_is_list([1 => 'apple', 0 => 'orange']); // false
	 *
	 *              // Non-integer keys
	 *              array_is_list([0 => 'apple', 'foo' => 'bar']); // false
	 *
	 *              // Non-consecutive keys
	 *              array_is_list([0 => 'apple', 2 => 'bar']); // false
	 *         </code>
	 */
	public function array_is_list( array $items ): bool {
		if ( function_exists( 'array_is_list' ) ) {
			return array_is_list( $items );
		}

		return array_values( $items ) === $items;
	}
}
