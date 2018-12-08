jQuery(document).ready( function($) {
	
	var rem, the_id;
	
	$('#bpmh-left-sidebar').addClass('bpmh-widgets-sortables');
	$('#bpmh-right-sidebar').addClass('bpmh-widgets-sortables');
	
	var parent = $('#bp_my_home_widgets').parent().parent();
	console.log( parent.prop( 'class' ) );
	parent.removeClass('inactive-sidebar');
	var inside = parent.find('.widget-holder').first();
	inside.removeClass('inactive');
	$('#bp_my_home_widgets').append( $('#bpmh-widgets-list').html() );
	$('#bpmh-widgets-list').remove();
	
	$("#bpmh-widgets .widget").each(function(){
		$(this).addClass('ui-draggable');
	});
	
	$('#bp_my_home_widgets .sidebar-name h3').append('<span id="removing-bpmhwidget">Deactivate <span></span></span>');
	
	$('#available-widgets .widget').each( function(){
		if( $(this).attr('id').indexOf('bpmh_') != -1 )
			$(this).remove();
	});
	
});