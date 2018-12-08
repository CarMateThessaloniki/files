jQuery(document).ready(function($){
	$('#bpmh-comments-list').on( 'click', '.bpmh-comments-view', function( event ){
		event.preventDefault();

		$(this).parent().parent().next().slideToggle();
	});
	
	$('#the_blog_id').on( 'change', function( event ){
		event.preventDefault();

		var blogid = $(this).val();
		
		if( blogid == 0 )
			return false;
			
			
		$('#bpmh-comments-list').html( '<span class="loading">' + bpmh_comments_var.loadingmessage + '</span>' );

		var data = {
		    action:'bpmh_comments_refresh',
		    blog_id: blogid
		};

		$.post(ajaxurl, data, function(response) {

			if( response != 0 )
				$('#bpmh-comments-list').html(response);

		} );
		
	});
});