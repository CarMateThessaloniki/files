<!--<form role="search" method="get" id="searchform" class="custom" action="<?php echo home_url( '/' ); ?>">
    <div class="row collapse">
        <div class="nine columns">
            <input type="text" value="<?php if (isset($_GET) && isset($_GET['s'])) echo esc_attr($_GET['s']);?>" name="s" id="s">
        </div>
        <div class="three columns">
            <input type="submit" class="button radius small secondary expand postfix" id="searchsubmit" value="<?php _e("Search", 'kleo_framework');?>">
        </div>
    </div>
</form> -->

<form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
      <label class="screen-reader-text" for="s"><?php _e( 'Search for:', 'woocommerce' ); ?></label>
      <div class="row collapse">
        <div class="nine columns">
		<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="<?php _e( 'Search for products', 'woocommerce' ); ?>" />
        </div>
        <div class="three columns">
  
          <input type="submit" class="button radius small secondary expand postfix" id="searchsubmit"></input>
				<input type="hidden" name="post_type" value="product" />
        </div>
      </div>
    
		</form>
    
    
    