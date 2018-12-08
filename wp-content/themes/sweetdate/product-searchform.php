<form role="search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
      <label class="screen-reader-text" for="s"><?php _e( 'Search for:', 'woocommerce' ); ?></label>
      <div class="row collapse">
        <div class="ten mobile-three columns">
				<input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" placeholder="<?php _e( 'Search for products', 'woocommerce' ); ?>" />
        </div>
        <div class="two mobile-one columns">
  
          <button type="submit" class="small button expand postfix" id="searchsubmit"><i class="icon icon-search"></i></button>
				<input type="hidden" name="post_type" value="product" />
        </div>
      </div>
    
		</form>
    
    
    