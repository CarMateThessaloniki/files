jQuery(document).ready( function($){
	$('#feed_selector').on( 'change', function( event ){

		event.preventDefault();
		
		$('#bpmh_feed_reader').html( '<span class="loading">' + bpmh_rss_var.loadingmessage + '</span>' );
		
		var data = {
	      action:'bpmh_rss_refresh',
	      feed: $(this).val()
	    };

	    $.post(ajaxurl, data, function(response) {
			
			if( response != 0 )
				$('#bpmh_feed_reader').html(response);
		
	    } );
	    
	});
});