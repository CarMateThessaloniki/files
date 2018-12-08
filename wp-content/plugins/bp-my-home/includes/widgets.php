<?php

/**
 * BP My Home Default Widgets
 *
 * @package BP My Home
 * @subpackage Widgets
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Registers 2 new sidebars to house our widgets
 *
 * @uses register_sidebar() to register the sidebars
 */
function bpmh_register_sidebars() {
	
	register_sidebar( array(
		'name' => __( 'BPMH Left Column', 'bp-my-home' ),
		'id' => 'bpmh-left-sidebar',
		'description' => __( 'Left column of BP My Home user&#39;s page', 'bp-my-home' ),
		'before_widget' => '<div id="%1$s" class="dragbox widget %2$s"><div class="handle_div"><h2>&nbsp;</h2></div>',
		'after_widget' => '</div></div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2><div class="dragbox-content">',
	) );

	register_sidebar( array(
		'name' => __( 'BPMH Right Column', 'bp-my-home' ),
		'id' => 'bpmh-right-sidebar',
		'description' => __( 'Right column of BP My Home user&#39;s page', 'bp-my-home' ),
		'before_widget' => '<div id="%1$s" class="dragbox widget %2$s"><div class="handle_div"><h2>&nbsp;</h2></div>',
		'after_widget' => '</div></div>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2><div class="dragbox-content">',
	) );
}

add_action( 'bpmh_widgets_init', 'bpmh_register_sidebars' );


/**
 * Bookmark Widget
 *
 * @package BP My Home
 */
class BPMH_Widget_Bookmark extends WP_Widget {
	
	/**
	 * The constructor
	 */
	function __construct() {
		$widget_ops = array( 'classname' => 'bpmh-widget_bookmark', 'description' => __( 'Stores user&#39;s bookmarks') );
		parent::__construct( false, _x( '(BPMH) Bookmark', 'widget name', 'bp-my-home' ), $widget_ops );
		
		add_action( 'bpmh_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'bpmh_actions', array( $this, 'save_user_settings' ) );
	}
	
	/**
	 * Load some styles if the widget is activated
	 *
	 * @uses bp_my_home_is_activated_widget() to check the widget is activated
	 * @uses bp_is_current_component() to check we're on the my-home component
	 * @uses bp_my_home_get_slug() to get plugin's slug
	 * @uses bp_action_variables() to check for action variables
	 * @uses wp_enqueue_style() to enqueue the needed style
	 * @uses bp_my_home_get_css_url() to get the url of the css dir
	 * @uses bp_my_home_get_version() to get the version of the plugin
	 */
	function enqueue_scripts() {
		if( bp_my_home_is_activated_widget( $this->id_base ) && bp_is_current_component( bp_my_home_get_slug() ) && !bp_action_variables() ) {
			wp_enqueue_style( 'bpmh-bookmark-css', bp_my_home_get_css_url() . 'bpmh-bookmark.css', false, bp_my_home_get_version() );
		}
	}
	
	/**
	 * Registers the widget
	 *
	 * @uses register_widget()
	 */
	public static function register_widget() {
		register_widget( 'BPMH_Widget_Bookmark' );
	}

	/**
	 * Displays the content of the widget
	 *
	 * @param array $args 
	 * @param array $instance 
	 * @uses bp_my_home_get_user_settings_link() to get user's my settings link
	 * @uses is_admin() to check we're not in backend
	 * @uses is_network_admin() to check we're not in network backend
	 * @return string html the content of the widget
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Bookmarks', 'bp-my-home' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		
		$widget_hash = trailingslashit( base64_encode( get_class( $this ) ) );
		
		$config_link = trailingslashit( bp_my_home_get_user_settings_link() . 'widget' ) . $widget_hash;
		
		if ( $title )
			echo $before_title .'<span class="configure" ><a href="'.$config_link.'">'.__('Configure', 'bp-my-home') .'</a></span>' . $title . $after_title;
		
		if( !is_admin() && !is_network_admin() )
			$this->user_content( $instance );
			
		else
			echo '<p>' . __( 'Content is only available in user&#39;s page', 'bp-my-home' ) . '</p>';
		
		echo $after_widget;
	}
	
	/**
	 * Updates the title of the widget
	 *
	 * @param array $new_instance 
	 * @param array $old_instance 
	 * @return array the instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the form in the admin of Widgets
	 *
	 * @param array $instance 
	 * @uses wp_parse_args() to merge args with defaults
	 * @uses esc_attr() to sanitize the title
	 * @return string html the form
	 */
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = esc_attr( $instance['title'] );
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}
	
	/**
	 * Displays the user's private content
	 *
	 * @param array $instance 
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses get_user_meta() to get user's bookmark list
	 * @return string html of the private content
	 */
	function user_content( $instance ) {
		$user_id = bp_loggedin_user_id();
		
		$bpmh_bookmarks_list = get_user_meta( $user_id, 'bpmh_bkmks_list', true );
		
		if( !empty( $bpmh_bookmarks_list ) && is_array( $bpmh_bookmarks_list ) && count( $bpmh_bookmarks_list ) > 0 ) {
			?>
			<ul id="bpmh-bookmark-list">
			
				<?php foreach( $bpmh_bookmarks_list as $bkmk ):?>
				
					<li>
						<a href="<?php echo $bkmk['url'];?>" title="<?php echo sprintf( __( 'Go to : %s', 'bpmh-bkmks' ), $bkmk['alias'] );?>"><?php echo $bkmk['alias'];?></a>
					</li>
					
				<?php endforeach;?>
				
			</ul>
			<?php
		} else {
			?>
			<p><?php _e( 'To add new bookmarks, you can hover this box header to activate the configure link', 'bp-my-home' ) ;?></p>
			<?php
		}

	}
	
	/**
	 * Displays the bmph widget user settings form
	 *
	 * @param array $args 
	 * @param array $instance 
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses get_user_meta() to get user's bookmark list
	 * @uses esc_html() to sanitize the alias
	 * @uses esc_url() to sanitize the url
	 * @uses wp_nonce_field() for security reasons
	 * @return string html of the user's settings form
	 */
	function user_settings( $args, $instance ) {
	
		extract( $args );
		
		$user_id = bp_loggedin_user_id();
		
		$bpmh_bookmarks_list = get_user_meta( $user_id, 'bpmh_bkmks_list', true );
		
		if( empty( $bpmh_bookmarks_list ) )
			$bpmh_bookmarks_list = array();

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Bookmarks' ) : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;

		if ( $title)
			echo $before_title . sprintf( __('Settings for %s', 'bp-my-home' ), $title ) . $after_title;
		?>
		<form action="" method="post" class="standard-form">
			<h4><?php _e( 'Add a new bookmark', 'bp-my-home' );?></h4>
			<ul>
				<li>
					<label for="bpmh-bookmark-alias"><?php _e( 'Alias', 'bp-my-home' );?></label>
					<input type="text" id="bpmh-bookmark-alias" name="_bpmh_bookmark[alias]"/>
				</li>
				<li>
					<label for="bpmh-bookmark-link"><?php _e( 'Link', 'bp-my-home' );?></label>
					<input type="text" id="bpmh-bookmark-link" name="_bpmh_bookmark[url]" placeholder="http://"/>
				</li>
			</ul>
			
			<?php if( !empty( $bpmh_bookmarks_list ) && is_array( $bpmh_bookmarks_list ) && count( $bpmh_bookmarks_list ) > 0 ):?>
				
				<h4><?php _e( 'Delete bookmarks', 'bp-my-home' );?></h4>
				
				<table>
					<thead>
						<tr>
							<th><?php _e( 'Alias', 'bp-my-home' );?></th>
							<th><?php _e( 'Select to delete', 'bp-my-home' );?></th>
						<tr>
					</thead>
					<tbody>
						
						<?php foreach( $bpmh_bookmarks_list as $bkmk ):?>
							
							<tr>
								<td><?php echo esc_html( $bkmk['alias'] );?></td>
								<td><input type="checkbox" name="_bpmh_bookmark[delete][]"/ value="<?php echo esc_url( $bkmk['url'] );?>"></td>
							</tr>
							
						<?php endforeach;?>
						
					</tbody>
				</table>
				
			<?php endif;?>
			<?php wp_nonce_field( 'bpmh_bookmark_options', '_wpnonce_widget_options_' . $this->id_base ); ?>
			<p><input type="submit" name="_bpmh_bookmark[save]" value="<?php _e( 'Save', 'bp-my-home' );?>"></p>
		</form>
		<?php
			echo $after_widget;
	}
	
	/**
	 * Stores the bmph widget user settings
	 *
	 * @uses bp_my_home_current_is_widget_settings() to check we're on widget's setting page
	 * @uses bp_action_variable() to get the widget user want to save settings for
	 * @uses check_admin_referer() for security reasons
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses get_user_meta() to get user's bookmark list
	 * @uses esc_url_raw() to sanitize the url
	 * @uses esc_html() to sanitize the alias
	 * @uses update_user_meta() to save the bookmarks
	 * @uses delete_user_meta() to remove the bookmarks
	 * @uses bp_my_home_get_user_settings_link() to get user's my home setting link
	 * @uses bp_core_add_message() to add a notice to inform user
	 * @uses bp_core_redirect() to redirect user's and avoids double posting
	 * @return string html of the user's settings form
	 */
	function save_user_settings() {
		if( bp_my_home_current_is_widget_settings() && base64_decode( bp_action_variable( 1 ) ) == get_class( $this ) ) {
			
			if( empty( $_POST['_bpmh_bookmark']['save'] ) )
				return;
			
			check_admin_referer( 'bpmh_bookmark_options', '_wpnonce_widget_options_' . $this->id_base );
			
			$user_id = bp_loggedin_user_id();
			
			$bpmh_bookmarks_list = get_user_meta( $user_id, 'bpmh_bkmks_list', true );

			if( empty( $bpmh_bookmarks_list ) )
				$bpmh_bookmarks_list = array();
			
			if( !empty( $_POST['_bpmh_bookmark']['delete'] ) && is_array( $_POST['_bpmh_bookmark']['delete'] ) ) {
				$bpmh_delete_list = $_POST['_bpmh_bookmark']['delete'];
				foreach( $bpmh_bookmarks_list as $key => $bkmk ) {
					if( in_array( esc_url_raw( $bkmk['url'] ), $bpmh_delete_list ) )
						unset( $bpmh_bookmarks_list[$key] );
				}
			}

			if( !empty( $_POST['_bpmh_bookmark']['alias'] ) && !empty( $_POST['_bpmh_bookmark']['url'] ) ) {

				$new_bookmark[] = array( 'alias' => esc_html( $_POST['_bpmh_bookmark']['alias'] ), 'url' => esc_url_raw( $_POST['_bpmh_bookmark']['url'] ) );

				if( empty( $bpmh_bookmarks_list ) )
					$bpmh_bookmarks_list = $new_bookmark;
				else
					$bpmh_bookmarks_list = array_merge( $bpmh_bookmarks_list, $new_bookmark );

			}

			if( !empty( $bpmh_bookmarks_list ) )
				update_user_meta( $user_id, 'bpmh_bkmks_list', $bpmh_bookmarks_list );
			else
				delete_user_meta( $user_id, 'bpmh_bkmks_list' );
				
			// let's finally redirect
			$widget_hash = trailingslashit( base64_encode( get_class( $this ) ) );
			$redirect = trailingslashit( bp_my_home_get_user_settings_link() . 'widget' ) . $widget_hash;
			
			bp_core_add_message( __( 'Widget settings saved successfully', 'bp-my-home' ) );
			bp_core_redirect( $redirect );
		}
	}

}

/**
 * Notepad Widget
 *
 * @package BP My Home
 */
class BPMH_Widget_Notepad extends WP_Widget {

	/**
	 * The constructor
	 */
	function __construct() {
		$widget_ops = array('classname' => 'bpmh-widget_notepad', 'description' => __( 'A small Notepad for your members', 'bp-my-home' ) );
		parent::__construct( false, _x( '(BPMH) Notepad', 'widget name', 'bp-my-home' ), $widget_ops );
		
		add_action( 'bpmh_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	
	/**
	 * Load some styles if the widget is activated
	 *
	 * @uses bp_my_home_is_activated_widget() to check the widget is activated
	 * @uses bp_is_current_component() to check we're on the my-home component
	 * @uses bp_my_home_get_slug() to get plugin's slug
	 * @uses bp_action_variables() to check for action variables
	 * @uses wp_enqueue_style() to enqueue the needed style
	 * @uses bp_my_home_get_css_url() to get the url of the css dir
	 * @uses bp_my_home_get_version() to get the version of the plugin
	 * @uses wp_register_script() to register jeditable
	 * @uses wp_enqueue_script() to enqueue the needed javascript
	 * @uses bp_my_home_get_js_url() to get the url of the js dir
	 * @uses wp_localize_script() to include some data and translate it
	 */
	function enqueue_scripts() {
		if( bp_my_home_is_activated_widget( $this->id_base ) && bp_is_current_component( bp_my_home_get_slug() ) && !bp_action_variables() ) {
			wp_enqueue_style( 'bpmh-notepad-css', bp_my_home_get_css_url() . 'bpmh-notepad.css', false, bp_my_home_get_version() );
			wp_register_script( 'jeditable-mini', bp_my_home_get_js_url() . 'jquery.jeditable.mini.js', array( 'jquery' ), bp_my_home_get_version(), true );
			wp_enqueue_script( 'bpmh-notepad-js', bp_my_home_get_js_url() . 'bpmh-notepad.js', array( 'jeditable-mini' ), bp_my_home_get_version(), true );
			wp_localize_script( 'bpmh-notepad-js', 'bpmh_notepad_var', array(
				'tooltip'  => __( 'Click to edit', 'bp-my-home' )
			));
		}
	}
	
	/**
	 * Registers the widget
	 *
	 * @uses register_widget()
	 */
	public static function register_widget() {
		register_widget( 'BPMH_Widget_Notepad' );
	}

	/**
	 * Displays the content of the widget
	 *
	 * @param array $args 
	 * @param array $instance 
	 * @uses is_admin() to check we're not in backend
	 * @uses is_network_admin() to check we're not in network backend
	 * @return string html the content of the widget
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Notepad' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;
		
		if( !is_admin() && !is_network_admin() )
			$this->user_content( $instance );
			
		else
			echo '<p>' . __( 'Content is only available in user&#39;s page', 'bp-my-home' ) . '</p>';
		
		echo $after_widget;
	}

	/**
	 * Updates the title of the widget
	 *
	 * @param array $new_instance 
	 * @param array $old_instance 
	 * @return array the instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the form in the admin of Widgets
	 *
	 * @param array $instance 
	 * @uses wp_parse_args() to merge args with defaults
	 * @uses esc_attr() to sanitize the title
	 * @return string html the form
	 */
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = esc_attr( $instance['title'] );
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}
	
	/**
	 * Displays the user's private content
	 *
	 * @param array $instance 
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses get_user_meta() to get user's notes
	 * @return string html of the private content
	 */
	function user_content( $instance = array() ) {
		$user_id = bp_loggedin_user_id();
		
		$bpmh_notepad_text = nl2br( get_user_meta( $user_id, 'bpmh_notepad_notes', true ) );
		
		?>
		<div id="notepad-content">
			<div class="bpmh-notepad-text"><?php echo $bpmh_notepad_text;?></div>
		</div>
		<?php
	}

}

/**
 * RSS Widget
 *
 * @package BP My Home
 */
class BPMH_Widget_Rss extends WP_Widget {

	/**
	 * The constructor
	 */
	function __construct() {
		$widget_ops = array('classname' => 'bpmh-widget_rss', 'description' => __( 'An RSS reader for your members', 'bp-my-home') );
		parent::__construct( false, _x( '(BPMH) Rss Reader', 'widget name', 'bp-my-home' ), $widget_ops );
		
		add_action( 'bpmh_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'bpmh_actions', array( $this, 'save_user_settings' ) );
	}
	
	/**
	 * Load some styles if the widget is activated
	 *
	 * @uses bp_my_home_is_activated_widget() to check the widget is activated
	 * @uses bp_is_current_component() to check we're on the my-home component
	 * @uses bp_my_home_get_slug() to get plugin's slug
	 * @uses bp_action_variables() to check for action variables
	 * @uses wp_enqueue_style() to enqueue the needed style
	 * @uses bp_my_home_get_css_url() to get the url of the css dir
	 * @uses bp_my_home_get_version() to get the version of the plugin
	 * @uses wp_enqueue_script() to enqueue the needed javascript
	 * @uses bp_my_home_get_js_url() to get the url of the js dir
	 * @uses wp_localize_script() to include some data and translate it
	 */
	function enqueue_scripts() {
		if( bp_my_home_is_activated_widget( $this->id_base ) && bp_is_current_component( bp_my_home_get_slug() ) && !bp_action_variables() ) {
			wp_enqueue_style( 'bpmh-rss-css', bp_my_home_get_css_url() . 'bpmh-rss.css' );
			wp_enqueue_script( 'bpmh-rss-js', bp_my_home_get_js_url() . 'bpmh-rss.js', array( 'jquery' ), bp_my_home_get_version(), true );
			wp_localize_script( 'bpmh-rss-js', 'bpmh_rss_var', array(
				'loadingmessage'  => __( 'Refreshing the feed, please wait..', 'bp-my-home' )
			));
		}
	}
	
	/**
	 * Registers the widget
	 *
	 * @uses register_widget()
	 */
	public static function register_widget() {
		register_widget( 'BPMH_Widget_Rss' );
	}

	/**
	 * Displays the content of the widget
	 *
	 * @param array $args 
	 * @param array $instance 
	 * @uses bp_my_home_get_user_settings_link() to get user's my settings link
	 * @uses is_admin() to check we're not in backend
	 * @uses is_network_admin() to check we're not in network backend
	 * @return string html the content of the widget
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Rss Reader', 'bp-my-home' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		
		$widget_hash = trailingslashit( base64_encode( get_class( $this ) ) );
		
		$config_link = trailingslashit( bp_my_home_get_user_settings_link() . 'widget' ) . $widget_hash;
		
		if ( $title )
			echo $before_title .'<span class="configure" ><a href="'.$config_link.'">'.__('Configure', 'bp-my-home') .'</a></span>' . $title . $after_title;
		
		if( !is_admin() && !is_network_admin() )
			$this->user_content( $instance );
			
		else
			echo '<p>' . __( 'Content is only available in user&#39;s page', 'bp-my-home' ) . '</p>';
		
		echo $after_widget;
	}

	/**
	 * Updates the title of the widget
	 *
	 * @param array $new_instance 
	 * @param array $old_instance 
	 * @return array the instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the form in the admin of Widgets
	 *
	 * @param array $instance 
	 * @uses wp_parse_args() to merge args with defaults
	 * @uses esc_attr() to sanitize the title
	 * @return string html the form
	 */
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = esc_attr( $instance['title'] );
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}
	
	/**
	 * Displays the user's private content
	 *
	 * @param array $instance 
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses get_user_meta() to get user's feed list
	 * @uses bpmh_rss_grab_feed() to get all feeds
	 * @uses esc_url() to sanitize feed
	 * @uses esc_attr() to sanitize alias
	 * @return string html of the private content
	 */
	function user_content( $instance ) {
		$user_id = bp_loggedin_user_id();
		
		$bpmh_feeds_list = get_user_meta( $user_id, 'bpmh_rss_feeds', true );
		
		if( !empty( $bpmh_feeds_list ) && is_array( $bpmh_feeds_list ) && count( $bpmh_feeds_list ) > 0 ) {
			$feeds = bpmh_rss_grab_feed();
			?>
			<select id="feed_selector">
				
				<option value="all"><?php _e( 'all','bp-my-home' );?></option>
			
				<?php foreach( $bpmh_feeds_list as $feed ):?>

					<option value="<?php echo esc_url( $feed['url'] );?>"><?php echo esc_attr( $feed['alias'] );?></option>
							
				<?php endforeach;?>
				
			</select>
			
			<div id="bpmh_feed_reader">
				
				<?php foreach( $feeds as $feed ) :
					echo $feed;
				endforeach;?>
			</div>
			<?php
		} else {
			?>
			<p><?php _e( 'To add new feeds, you can hover this box header to activate the configure link', 'bp-my-home' ) ;?></p>
			<?php
		}

	}
	
	/**
	 * Displays the bmph widget user settings form
	 *
	 * @param array $args 
	 * @param array $instance 
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses get_user_meta() to get user's feed list
	 * @uses esc_html() to sanitize the alias
	 * @uses esc_url() to sanitize the url
	 * @uses wp_nonce_field() for security reasons
	 * @return string html of the user's settings form
	 */
	function user_settings( $args, $instance ) {
	
		extract( $args );
		
		$user_id = bp_loggedin_user_id();
		$bpmh_feeds_list = get_user_meta( $user_id, 'bpmh_rss_feeds', true );
		
		if( empty( $bpmh_feeds_list ) )
			$bpmh_feeds_list = array();

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Rss Reader' ) : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;

		if ( $title)
			echo $before_title . sprintf( __('Settings for %s', 'bp-my-home' ), $title ) . $after_title;
		?>
		<form action="" method="post" class="standard-form">
			<h4><?php _e( 'Add a new feed', 'bp-my-home' );?></h4>
			<ul>
				<li>
					<label for="bpmh-rss-alias"><?php _e( 'Alias', 'bp-my-home' );?></label>
					<input type="text" id="bpmh-rss-alias" name="_bpmh_rss[alias]"/>
				</li>
				<li>
					<label for="bpmh-rss-feed"><?php _e( 'Feed', 'bp-my-home' );?></label>
					<input type="text" id="bpmh-rss-feed" name="_bpmh_rss[url]" placeholder="http://"/>
				</li>
			</ul>
			
			<?php if( !empty( $bpmh_feeds_list ) && is_array( $bpmh_feeds_list ) && count( $bpmh_feeds_list ) > 0 ):?>
				
				<h4><?php _e( 'Delete feeds', 'bp-my-home' );?></h4>
				
				<table>
					<thead>
						<tr>
							<th><?php _e( 'Alias', 'bp-my-home' );?></th>
							<th><?php _e( 'Select to delete', 'bp-my-home' );?></th>
						<tr>
					</thead>
					<tbody>
						
						<?php foreach( $bpmh_feeds_list as $feed ):?>
							
							<tr>
								<td><?php echo esc_html( $feed['alias'] );?></td>
								<td><input type="checkbox" name="_bpmh_rss[delete][]"/ value="<?php echo esc_url( $feed['url'] );?>"></td>
							</tr>
							
						<?php endforeach;?>
						
					</tbody>
				</table>
				
			<?php endif;?>
			<?php wp_nonce_field( 'bpmh_rss_options', '_wpnonce_widget_options_' . $this->id_base ); ?>
			<p><input type="submit" name="_bpmh_rss[save]" value="<?php _e( 'Save', 'bp-my-home' );?>"></p>
		</form>
		<?php
			echo $after_widget;
	}
	
	/**
	 * Stores the bmph widget user settings
	 *
	 * @uses bp_my_home_current_is_widget_settings() to check we're on widget's setting page
	 * @uses bp_action_variable() to get the widget user want to save settings for
	 * @uses check_admin_referer() for security reasons
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses get_user_meta() to get user's feed list
	 * @uses esc_url_raw() to sanitize the url
	 * @uses esc_html() to sanitize the alias
	 * @uses update_user_meta() to save the feeds
	 * @uses delete_user_meta() to remove the feeds
	 * @uses bp_my_home_get_user_settings_link() to get user's my home setting link
	 * @uses bp_core_add_message() to add a notice to inform user
	 * @uses bp_core_redirect() to redirect user's and avoids double posting
	 * @return string html of the user's settings form
	 */
	function save_user_settings() {
		if( bp_my_home_current_is_widget_settings() && base64_decode( bp_action_variable( 1 ) ) == get_class( $this ) ) {
			
			if( empty( $_POST['_bpmh_rss']['save'] ) )
				return;
			
			check_admin_referer( 'bpmh_rss_options', '_wpnonce_widget_options_' . $this->id_base );
			
			$user_id = bp_loggedin_user_id();
			
			$bpmh_feeds_list = get_user_meta( $user_id, 'bpmh_rss_feeds', true );

			if( empty( $bpmh_feeds_list ) )
				$bpmh_feeds_list = array();
			
			if( !empty( $_POST['_bpmh_rss']['delete'] ) && is_array( $_POST['_bpmh_rss']['delete'] ) ) {
				$bpmh_delete_list = $_POST['_bpmh_rss']['delete'];
				foreach( $bpmh_feeds_list as $key => $feed ) {
					if( in_array( esc_url_raw( $feed['url'] ), $bpmh_delete_list ) )
						unset( $bpmh_feeds_list[$key] );
				}
			}
			
			if( !empty( $_POST['_bpmh_rss']['alias'] ) && !empty( $_POST['_bpmh_rss']['url'] ) ) {
				
				$new_feed[] = array( 'alias' => esc_html( $_POST['_bpmh_rss']['alias'] ), 'url' => esc_url_raw( $_POST['_bpmh_rss']['url'] ) );
				
				if( empty( $bpmh_feeds_list ) )
					$bpmh_feeds_list = $new_feed;
				else
					$bpmh_feeds_list = array_merge( $bpmh_feeds_list, $new_feed );
				
			}
			
			if( !empty( $bpmh_feeds_list ) )
				update_user_meta( $user_id, 'bpmh_rss_feeds', $bpmh_feeds_list );
			else
				delete_user_meta( $user_id, 'bpmh_rss_feeds' );
				
			// let's finally redirect
			$widget_hash = trailingslashit( base64_encode( get_class( $this ) ) );
			$redirect = trailingslashit( bp_my_home_get_user_settings_link() . 'widget' ) . $widget_hash;
			
			bp_core_add_message( __( 'Widget settings saved successfully', 'bp-my-home' ) );
			bp_core_redirect( $redirect );
		}
	}

}

/**
 * Comments Widget
 *
 * @package BP My Home
 */
class BPMH_Widget_Comments extends WP_Widget {

	/**
	 * The constructor
	 */
	function __construct() {
		$widget_ops = array('classname' => 'bpmh-widget_comments', 'description' => __( 'Let your members follow their latest comments', 'bp-my-home') );
		parent::__construct( false, _x( '(BPMH) Comments', 'widget name', 'bp-my-home' ), $widget_ops );
		
		add_action( 'bpmh_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	
	/**
	 * Load some styles if the widget is activated
	 *
	 * @uses bp_my_home_is_activated_widget() to check the widget is activated
	 * @uses bp_is_current_component() to check we're on the my-home component
	 * @uses bp_my_home_get_slug() to get plugin's slug
	 * @uses bp_action_variables() to check for action variables
	 * @uses wp_enqueue_style() to enqueue the needed style
	 * @uses bp_my_home_get_css_url() to get the url of the css dir
	 * @uses bp_my_home_get_version() to get the version of the plugin
	 * @uses wp_enqueue_script() to enqueue the needed javascript
	 * @uses bp_my_home_get_js_url() to get the url of the js dir
	 * @uses wp_localize_script() to include some data and translate it
	 */
	function enqueue_scripts() {
		if( bp_my_home_is_activated_widget( $this->id_base ) && bp_is_current_component( bp_my_home_get_slug() ) && !bp_action_variables() ) {
			wp_enqueue_style( 'bpmh-comments-css', bp_my_home_get_css_url() . 'bpmh-comments.css' );
			wp_enqueue_script( 'bpmh-comments-js', bp_my_home_get_js_url() . 'bpmh-comments.js', array( 'jquery' ), bp_my_home_get_version(), true );
			wp_localize_script( 'bpmh-comments-js', 'bpmh_comments_var', array(
				'loadingmessage'  => __( 'Loading the comments, please wait..', 'bp-my-home' )
			));
		}
	}
	
	/**
	 * Registers the widget
	 *
	 * @uses register_widget()
	 */
	public static function register_widget() {
		register_widget( 'BPMH_Widget_Comments' );
	}

	/**
	 * Displays the content of the widget
	 *
	 * @param array $args 
	 * @param array $instance 
	 * @uses is_admin() to check we're not in backend
	 * @uses is_network_admin() to check we're not in network backend
	 * @return string html the content of the widget
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Comments follow up', 'bp-my-home' ) : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		
		if ( $title )
			echo $before_title . $title . $after_title;
		
		if( !is_admin() && !is_network_admin() )
			$this->user_content( $instance );
			
		else
			echo '<p>' . __( 'Content is only available in user&#39;s page', 'bp-my-home' ) . '</p>';
		
		echo $after_widget;
	}

	/**
	 * Updates the title of the widget
	 *
	 * @param array $new_instance 
	 * @param array $old_instance 
	 * @return array the instance
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the form in the admin of Widgets
	 *
	 * @param array $instance 
	 * @uses wp_parse_args() to merge args with defaults
	 * @uses esc_attr() to sanitize the title
	 * @return string html the form
	 */
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = esc_attr( $instance['title'] );
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
<?php
	}
	
	/**
	 * Displays the user's private content
	 *
	 * @param array $instance 
	 * @uses bp_loggedin_user_id() to get current user's id
	 * @uses is_multisite() to check for a network of blogs
	 * @uses bpmh_list_blogs() to list the blogs of the network
	 * @uses esc_attr() to sanitize alias
	 * @uses bpmh_comments_display_comment_list() displays the list of comments
	 * @return string html of the private content
	 */
	function user_content( $instance ) {
		$user_id = bp_loggedin_user_id();
		
		$blog_selector = false;
		
		//1st are we on multisite ?
		if( is_multisite() ) {
			
			//let's try to get a blog list thanks to bp-blogs component	
			$blogs = bpmh_list_blogs();
			
			if( !empty( $blogs ) && is_array( $blogs ) && count( $blogs ) > 0 ) {
				// we build the blog selector
				
				$blog_selector = '<div id="blog_selector">';
				$blog_selector .= '<select id="the_blog_id">';
				$blog_selector .= '<option value="0">'. __( 'Select the blog', 'bp-my-home' ) .'</option>';
				
				foreach( $blogs as $blog ) {
					$blog_selector .= '<option value="'.intval( $blog['blog_id'] ).'">'. esc_attr( $blog['blog_name'] ).'</option>';
				}
				
				$blog_selector .= '</select></div>';
			}
		}
		
		echo $blog_selector;?>
		<div id="bpmh-comments-list">
			<?php bpmh_comments_display_comment_list();?>
		</div>
		
		<?php
	}

}

add_action( 'bpmh_widgets_init', array( 'BPMH_Widget_Bookmark', 'register_widget' ), 10 );
add_action( 'bpmh_widgets_init', array( 'BPMH_Widget_Notepad',  'register_widget' ), 10 );
add_action( 'bpmh_widgets_init', array( 'BPMH_Widget_Rss',      'register_widget' ), 10 );
add_action( 'bpmh_widgets_init', array( 'BPMH_Widget_Comments', 'register_widget' ), 10 );