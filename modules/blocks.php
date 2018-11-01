<?php
/**
 * Load code specific to Gutenberg blocks which are not tied to a module.
 * This file is unusual, and is not an actual `module` as such.
 * It is included in ./module-extras.php
 *
 */

jetpack_register_block(
	'map',
	array(
		'render_callback' => 'jetpack_map_load_assets',
	)
);

function jetpack_map_load_assets( $attr, $content ) {
	$dependencies = array(
		'wp-element',
		'wp-i18n',
		'wp-api-fetch',
	);
	Jetpack_Gutenberg::load_assets_as_required( 'map', $dependencies );
	return $content;
}
