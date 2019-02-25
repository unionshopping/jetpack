<?php
/**
 * Tiled Gallery block. Depends on the Photon module.
 *
 * @since 6.9.0
 *
 * @package Jetpack
 */

class Jetpack_Tiled_Gallery_Block {
	/**
	 * Register the block
	 */
	public static function register() {
		jetpack_register_block(
			'jetpack/tiled-gallery',
			array(
				'render_callback' => array( __CLASS__, 'render' ),
			)
		);
	}

	/**
	 * Tiled gallery block registration/dependency declaration.
	 *
	 * @param array  $attr    Array containing the block attributes.
	 * @param string $content String containing the block content.
	 *
	 * @return string
	 */
	public static function render( $attr, $content ) {
		$dependencies = array(
			'lodash',
			'wp-i18n',
			'wp-token-list',
		);
		Jetpack_Gutenberg::load_assets_as_required( 'tiled-gallery', $dependencies );

		$is_squareish_layout = self::is_squareish_layout( $attr );
		$srcset_step         = 300;

		if ( function_exists( 'jetpack_photon_url' )
			&& preg_match_all( '/<img [^>]+>/', $content, $images )
		) {
			/**
			 * This block processes all of the images that are found and builds $find and $replace.
			 *
			 * The original img is added to the $find array and the replacement is made and added
			 * to the $replace array. This is so that the same find and replace operations can be
			 * made on the entire $content.
			 */
			$find    = array();
			$replace = array();

			foreach ( $images[0] as $image_html ) {
				if (
					preg_match( '/data-width="([0-9]+)"/', $image_html, $img_height )
					&& preg_match( '/data-height="([0-9]+)"/', $image_html, $img_width )
					&& preg_match( '/src="([^"]+)"/', $image_html, $img_src )
				) {
					$orig_width   = $img_width[1];
					$orig_height  = $img_height[1];
					$orig_src     = $img_src[1];
					$srcset_parts = array();

					if ( $is_squareish_layout ) {
						$min_width = min( 600, $orig_width, $orig_height );
						$max_width = min( 2000, $orig_width, $orig_height );

						for ( $w = $min_width; $w <= $max_width; $w += $srcset_step ) {
							$photonized_src = jetpack_photon_url(
								$orig_src,
								array(
									'resize' => $w . ',' . $w,
									'strip'  => 'all',
								)
							);
							$srcset_parts[] = $photonized_src . ' ' . $w . 'w';
						}
					} else {
						$min_width = min( 600, $orig_width );
						$max_width = min( 2000, $orig_width );

						for ( $w = $min_width; $w <= $max_width; $w += $srcset_step ) {
							$photonized_src = jetpack_photon_url(
								$orig_src,
								array(
									'strip' => 'all',
									'w'     => $w,
								)
							);
							$srcset_parts[] = $photonized_src . ' ' . $w . 'w';
						}
					}

					if ( ! empty( $srcset_parts ) ) {
						$srcset = 'srcset="' . esc_attr( implode( ',', $srcset_parts ) ) . '"';

						$find[]    = $image_html;
						$replace[] = str_replace( '<img ', '<img ' . $srcset, $image_html );
					}
				}
			}

			if ( ! empty( $find ) ) {
				$content = str_replace( $find, $replace, $content );
			}
		}

		/**
		 * Filter the output of the Tiled Galleries content.
		 *
		 * @module tiled-gallery
		 *
		 * @since 6.9.0
		 *
		 * @param string $content Tiled Gallery block content.
		 */
		return apply_filters( 'jetpack_tiled_galleries_block_content', $content );
	}

	/**
	 * Determines whether a Tiled Gallery block uses square or circle images (1:1 ratio)
	 *
	 * Layouts are block styles and will be available as `is-style-[LAYOUT]` in the className
	 * attribute. The default (rectangular) will be omitted.
	 *
	 * @param  {Array} $attr Attributes key/value array.
	 * @return {boolean} True if layout is squareish, otherwise false.
	 */
	private static function is_squareish_layout( $attr ) {
		return isset( $attr['className'] )
			&& (
				'is-style-square' === $attr['className']
				|| 'is-style-circle' === $attr['className']
			);
	}
}

if (
	( defined( 'IS_WPCOM' ) && IS_WPCOM )
	|| class_exists( 'Jetpack_Photon' ) && Jetpack::is_module_active( 'photon' )
) {
	Jetpack_Tiled_Gallery_Block::register();
}
