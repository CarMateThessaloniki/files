<?php
/**
 * Template Name: Contact Page Template
 *
 * Description: This templates uses Google maps to show yor location
 *
 * @package WordPress
 * @subpackage Sweetdate
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Sweetdate 1.0
 */

get_header(); ?>

<?php if ( sq_option('gps_lat') &&  sq_option('gps_lon')): ?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script>
function initialize() {
  var myLatlng = new google.maps.LatLng(<?php echo sq_option('gps_lat');?>,<?php echo sq_option('gps_lon');?>);
  var mapOptions = {
    zoom: 12,
    center: myLatlng,
    scrollwheel: false,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }

  var map = new google.maps.Map(document.getElementById('gmap'), mapOptions);

  var contentString = '<div id="mapcontent">'+
      '<img id="logo_img" src="<?php echo sq_option('logo',get_template_directory_uri().'/assets/images/logo.png'); ?>" width="200" alt="<?php bloginfo('name'); ?>">'
      '</div>';

  var infowindow = new google.maps.InfoWindow({
      content: contentString
  });

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: '<?php bloginfo('name'); ?>',
      icon: '<?php echo get_template_directory_uri();?>/assets/images/icons/apple-touch-icon-57x57.png'
  });

  google.maps.event.addListener(marker, 'click', function() {
    //infowindow.open(map,marker);
  });
}

google.maps.event.addDomListener(window, 'load', initialize);

</script>
<section>
    <div id="gmap" class="map"></div>    
</section>

<?php endif; ?>


<!-- MAIN SECTION
================================================ -->
<section>
    <div id="main">
        
        <?php
        /**
         * Before main part - action
         */
        do_action('kleo_before_main');
        ?>
        
        <div class="row">
            <div class="twelve columns">


                <?php /* Start the Loop */ ?>
                <?php while ( have_posts() ) : the_post(); ?>

                        <?php get_template_part( 'content', 'page' ); ?>
                
                        <!-- Begin Comments -->
                        <?php comments_template( '', true ); ?>
                        <!-- End Comments -->

                <?php endwhile; ?>

            </div><!--end twelve-->

        </div><!--end row-->
  </div><!--end main-->

    <?php
    /**
     * After main part - action
     */
    do_action('kleo_after_main');
    ?>
  
</section>
<!--END MAIN SECTION-->

<?php get_footer(); ?>