jQuery(document).ready( function($){
	
	$('.bpmh-add-bkmk').on( 'click', function( event ){
			
		if( $(this).attr('href') != '#' )
			return event;

		event.preventDefault();

		if( $(this).hasClass( 'loading' ) )
			return;
		
		if( $(this).hasClass( 'error' ) )
			return;
			
		$(this).addClass( 'loading' );
		
		var url = $(this).data( 'bpmhurl' );
		var item = $(this).data( 'bpmhalias' );
		var nonce = $('#_wpnoncebpmh_bkmk').val();
		var self = $(this);
		var message = self.find( '.bpmh-message' );
		
		var data = {
		    action:'bpmh_bookmark_add',
		    'link': url,
			'alias': item,
			'_wpnoncebpmh_bkmk': nonce 
		};

		$.post(ajaxurl, data, function(response) {
			
			self.removeClass('loading');
			
			if( response['newlink'] )
				self.attr( 'href', response['newlink'] );
			else
				self.addClass('error');
				
			if( response['newtitle'] )
				self.attr( 'title', response['newtitle'] );
				
			message.html( response['info'] ) ;

		}, 'json' );

	});
	
	$('.bpmh-add-rss').on( 'click', function( event ){

		if( $(this).attr('href') != '#' )
			return event;

		event.preventDefault();
		
		if( $(this).hasClass( 'loading' ) )
			return false;
		
		if( $(this).hasClass( 'error' ) )
			return false;
			
		$(this).addClass( 'loading' );
		
		var url = $(this).data( 'bpmhfeed' );
		var item = $(this).data( 'bpmhalias' );
		var nonce = $('#_wpnoncebpmh_rss').val();
		var self = $(this);
		var message = self.find( '.bpmh-message' );
		
		var data = {
		    action:'bpmh_rss_add',
		    'link': url,
			'alias': item,
			'_wpnoncebpmh_rss': nonce 
		};

		$.post(ajaxurl, data, function(response) {
			
			self.removeClass('loading');
			
			if( response['newlink'] )
				self.attr( 'href', response['newlink'] );
			else
				self.addClass('error');
				
			if( response['newtitle'] )
				self.attr( 'title', response['newtitle'] );
				
			message.html( response['info'] ) ;

		}, 'json' );
		
	});
	
});