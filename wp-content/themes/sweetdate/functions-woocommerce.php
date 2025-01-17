<?php
/**
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.3
 */

// Disable WooCommerce styles
if ( !defined( 'WOOCOMMERCE_USE_CSS' ) ) { define( 'WOOCOMMERCE_USE_CSS', false ); }

// Load WooCommerce custom stylsheet
if ( ! is_admin() ) { add_action( 'wp_enqueue_scripts', 'kleo_load_woocommerce_css', 20 ); }

if ( ! function_exists( 'kleo_load_woocommerce_css' ) ) {
    function kleo_load_woocommerce_css () {
        wp_register_style( 'woocommerce', get_template_directory_uri() . '/woocommerce/assets/css/woocommerce.css' );
        wp_enqueue_style( 'woocommerce' );
    }
}

//de-register PrettyPhoto - we will use our own
//DISABLE WOOCOMMERCE PRETTY PHOTO STYLE
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );

function my_deregister_styles() {
	//wp_deregister_style( 'woocommerce_prettyPhoto_css' );
}

/*-----------------------------------------------------------------------------------*/
/* Hook Woocommerce on activation */
/*-----------------------------------------------------------------------------------*/

global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) add_action('init', 'kleo_install_theme', 1);

/*-----------------------------------------------------------------------------------*/
/* Install */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'kleo_install_theme' ) ) 
{
    function kleo_install_theme() {
		update_option( 'woocommerce_thumbnail_image_width', '300' );
		update_option( 'woocommerce_thumbnail_image_height', '300' );
		update_option( 'woocommerce_single_image_width', '600' ); // Single
		update_option( 'woocommerce_single_image_height', '600' ); // Single
		update_option( 'woocommerce_catalog_image_width', '400' ); // Catalog
		update_option( 'woocommerce_catalog_image_height', '400' ); // Catalog
    }
}


if ( ! function_exists( 'checked_environment' ) ) 
{
    // Check WooCommerce is installed first
    add_action('plugins_loaded', 'checked_environment');

    function checked_environment() 
    {
        if (!class_exists('woocommerce')) wp_die('WooCommerce must be installed');
    }
}

// Remove WC sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// WooCommerce layout overrides
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'kleo_woocommerce_before_content' ) ) 
{
    // WooCommerce layout overrides
    add_action( 'woocommerce_before_main_content', 'kleo_woocommerce_before_content', 10 );
    function kleo_woocommerce_before_content() 
    {
        get_template_part('page-parts/general-before-wrap');
    }
}

if ( ! function_exists( 'kleo_woocommerce_after_content' ) ) 
{
    // WooCommerce layout overrides
    add_action( 'woocommerce_after_main_content', 'kleo_woocommerce_after_content', 20 );
    function kleo_woocommerce_after_content() 
    {
        get_template_part('page-parts/woocommerce-after-wrap');
    }
}

//Remove breadcrumb
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

// Change columns in related products output to 3
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product_summary', 'kleo_woocommerce_output_related_products', 20 );

if ( ! function_exists( 'kleo_woocommerce_output_related_products' ) ) 
{
    function kleo_woocommerce_output_related_products() 
    {
       woocommerce_related_products( 3, 3 ); // 3 products, 3 columns
    }
}

// Change columns in upsell products output to 3
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

add_action( 'woocommerce_after_single_product_summary', 'kleo_woocommerce_upsell_display', 20 );

if ( ! function_exists( 'kleo_woocommerce_upsell_display' ) ) 
{
    function kleo_woocommerce_upsell_display() 
    {
       woocommerce_upsell_display( 3, 3 ); // 3 products, 3 columns
    }
}

if ( ! function_exists( 'loop_columns' ) ) 
{
    // Change columns in product loop to 3
    function loop_columns() 
    {
        return 3;
    }

    add_filter( 'loop_shop_columns', 'loop_columns' );
}


//remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 11 );

// Display 12 products per page
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ) );


if ( ! function_exists( 'kleo_star_sidebar' ) ) 
{
    // Adjust the star rating in the sidebar
    add_filter( 'woocommerce_star_rating_size_sidebar', 'kleo_star_sidebar' );

    function kleo_star_sidebar() {
    return 12;
    }
}

if ( ! function_exists( 'kleo_star_reviews' ) ) 
{
    // Adjust the star rating in the recent reviews
    add_filter( 'woocommerce_star_rating_size_recent_reviews', 'kleo_star_reviews' );

    function kleo_star_reviews() 
    {
        return 12;
    }
}


add_action( 'woocommerce_after_shop_loop_item', 'kleo_product_list_button', 16);
function kleo_product_list_button()
{
	global $product, $avia_config;
    $output = '';
	if ($product->product_type == 'bundle' )
    {
		$product = new WC_Product_Bundle($product->id);
	}

	if($product->product_type == 'simple')
	{
		$output = "<a class='view_details_button' href='".get_permalink($product->id)."'>".__('Details','kleo_framework')."</a>";
	}

	echo $output;
}



?>
