<?php

/*-----------------------------------------------------------------------------------*/
/*	Call to action box
 *      Create your own kleo_call_to_action() to override in a child theme.
 */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('kleo_call_to_action')) {
    function kleo_call_to_action( $atts, $content = null ) {
        extract(shortcode_atts(array(
            'bg'   => '',
        ), $atts));
        $output ='';
        $output .= '<div id="call-to-actions">';
        $output .= '<div class="row map-bg" '.($bg !='' ? ' style="background: url(\''.esc_attr($bg).'\') no-repeat center center"' : '').'>';
        $output .= do_shortcode($content);
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;

    }
    add_shortcode('kleo_call_to_action', 'kleo_call_to_action');
}

/*-----------------------------------------------------------------------------------*/
/*	Rounded image
/*-----------------------------------------------------------------------------------*/

if (!function_exists('kleo_img_rounded')) {
	function kleo_img_rounded( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'src' => '',
	    ), $atts));

            $output = '<div class="circle-image">';
            $output .= '  <a href="'.$src.'" class="imagelink" data-rel="prettyPhoto[gallery1]">
                <span class="overlay"></span>
                <span class="read"><i class="icon-'.apply_filters('kleo_img_rounded_icon','heart').'"></i></span>
                <img src="'.$src.'" alt="">
              </a>
            </div>';
            
           return $output;
        }
	add_shortcode('kleo_img_rounded', 'kleo_img_rounded');
}


?>
