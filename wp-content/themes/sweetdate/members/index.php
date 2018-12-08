<?php

/**
 * BuddyPress - Members Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); 
global $bp_fields;
?>

<?php do_action( 'bp_before_directory_members_page' ); ?>

<!-- MAIN SECTION
================================================ -->
	<section>
		<div id="main" class="search-result text-center">
			<div class="row">
				<div class="<?php echo apply_filters('kleo_bp_directory_main_cols','twelve');?> columns">

					<?php do_action( 'bp_before_directory_members' ); ?>

					<?php do_action( 'bp_before_directory_members_content' ); ?>

					<?php
					global $bp_results;

					$page_title = get_the_title();
					$lead ='';
					if(isset($_GET['bs']) && $bp_results['users'][0] != 0)
					{
						$page_title = __('Profile Search <span class="pink-text">results</span>', 'kleo_framework');
						$lead = __("Your search returned", 'kleo_framework')." ".count($bp_results['users']) ." ". _n( 'member', 'members', count($bp_results['users']), 'kleo_framework' );;

					} 
					?>

					<h2><?php echo $page_title; ?></h2>
					<p class="lead"><?php echo $lead;  ?></p>

					<div class="item-list-tabs" role="navigation">
						<ul>
							<li class="selected" id="members-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_members_root_slug() ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'buddypress' ), bp_get_total_member_count() ); ?></a></li>

							<?php if ( is_user_logged_in() && bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

								<li id="members-personal"><a href="<?php echo bp_loggedin_user_domain() . bp_get_friends_slug() . '/my-friends/' ?>"><?php printf( __( 'My Friends <span>%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>

							<?php endif; ?>

							<?php do_action( 'bp_members_directory_member_types' ); ?>

						</ul>
					</div><!-- .item-list-tabs -->

					<div class="item-list-tabs" id="subnav" role="navigation">
						<ul>

							<?php do_action( 'bp_members_directory_member_sub_types' ); ?>

						</ul>
					</div>

					<div id="members-dir-list" class="members dir-list">

						<!--Search List-->
						<div class="search-list twelve">

							<?php locate_template( array( 'members/members-loop.php' ), true ); ?>

						</div><!--end Search List-->

					</div><!-- #members-dir-list -->

					<?php do_action( 'bp_directory_members_content' ); ?>

					<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

					<?php do_action( 'bp_after_directory_members_content' ); ?>

				</div><!--end twelve-->
    
				<?php do_action( 'bp_after_directory_members' ); ?>
                    
			</div><!--end row-->
		</div><!--end main-->

	</section>
	<!--END MAIN SECTION-->

<?php do_action( 'bp_after_directory_members_page' ); ?>

<?php get_footer( 'buddypress' ); ?>
