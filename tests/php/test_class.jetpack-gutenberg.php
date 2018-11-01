<?php

class WP_Test_Jetpack_Gutenberg extends WP_UnitTestCase {

	public function test_jetpack_register_block_registres_a_gutenberg_block() {

		if ( ! function_exists( function_exists( 'register_block_type' ) ) ) {
			$this->markTestSkipped( 'register_block_type not available' );
			return;
		}

		if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
			$this->markTestSkipped( 'WP_Block_Type_Registry not available' );
			return;
		}
//		$args = array(
//			'render_callback' => '__return_true'
//		);
//		jetpack_register_block( 'test', $args );

		// $test_block = WP_Block_Type_Registry::get_instance()->get_registered( 'jetpack/test' );
		// var_dump( $test_block );
		// $this->assertEquals( )
	}

}
