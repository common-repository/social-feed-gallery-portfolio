<?php

/**
 *  Helper Class for Portfolio WP
 */

 class Portfolio_WP_Helper {
	
	/*
	*
	* Generate html attributes based on array
	*
	* @param array atributes
	*
	* @return string
	*
	*/
	public static function generate_attributes( $attributes ) {

		

		$return = '';
		foreach ( $attributes as $name => $value ) {

			if ( in_array( $name, array( 'alt', 'rel', 'title' ) ) ) {
				$value = str_replace('<script', '&lt;script', $value );
				$value = strip_tags( htmlspecialchars( $value ) );
				$value = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $value );
			}else{
				$value = esc_attr( $value );
			}

			$return .= ' ' . esc_attr( $name ) . '="' . $value . '"';
		}
		
		return $return;

	}

	public static function hover_effects_elements( $effect ) {

		$effects_with_title       = apply_filters( 'portfolio_effects_with_title', array( 'under', 'fluid-up', 'hide', 'quiet', 'reflex', 'curtain', 'lens', 'appear', 'crafty', 'seemo', 'comodo', 'pufrobo','lily','sadie','honey','layla','zoe','oscar','marley','ruby','roxy','bubba','dexter','sarah','chico','milo','julia','hera','winston','selena','terry','phoebe','apollo','steve','jazz','ming','lexi','duke','tilt_1' ,'tilt_3' ,'tilt_7' ) );
		$effects_with_description = apply_filters( 'portfolio_effects_with_description', array( 'under', 'fluid-up', 'hide', 'reflex', 'lens', 'crafty', 'pufrobo','lily','sadie','layla','zoe','oscar','marley','ruby','roxy','bubba','dexter','sarah','chico','milo','julia','selena','apollo','steve','jazz','ming','lexi','duke','tilt_1' ,'tilt_3' ,'tilt_7' ) );
		$effects_with_social      = apply_filters( 'portfolio_effects_with_social', array( 'under', 'comodo', 'seemo', 'appear', 'lens', 'curtain', 'reflex', 'catinelle', 'quiet', 'hide', 'pufrobo','lily','sadie','zoe','ruby','roxy','bubba','dexter','sarah','chico','julia','hera','winston','selena','terry','phoebe','ming','tilt_1', 'tilt_3' , 'tilt_7' ) );
		$effects_with_extra_scripts = apply_filters( 'portfolio_effects_with_scripts', array( 'tilt_1' ,'tilt_3' ,'tilt_7' ) );

		return array(
			'title'       => in_array( $effect, $effects_with_title ),
			'description' => in_array( $effect, $effects_with_description ),
			'social'      => in_array( $effect, $effects_with_social ),
			'scripts'     => in_array( $effect, $effects_with_extra_scripts )
		);

	}

	/**
	 * Callback to sort tabs/fields on priority.
	 *
	 * @since 1.0.1
	 *
	 * @return bool
	 */
	public static function sort_data_by_priority( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return -1;
		}
		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}
		return $a['priority'] < $b['priority'] ? -1 : 1;
	}


	public static function get_title( $item, $default_source ){

		$title = isset($item['title']) ? $item['title'] : '';

		if ( '' == $title && 'none' != $default_source ) {
			$title = self::get_image_info( $item['id'],$default_source );
		}

		return $title;

	}

	public static function get_description( $item, $default_source ){
		
		$description = isset($item['description']) ? $item['description'] : '';

		if ( '' == $description && 'none' != $default_source ) {
			$description = self::get_image_info( $item['id'],$default_source );
		}

		return $description;

	}

   
	
}