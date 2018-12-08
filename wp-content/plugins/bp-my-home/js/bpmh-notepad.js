jQuery(document).ready( function($) {
     $('.bpmh-notepad-text').editable(ajaxurl, {
	 	loadurl : ajaxurl,
        loaddata  : {action: "bpmh_notepad_ajax_get"},
		submitdata : {action: "bpmh_notepad_ajax_put"},
		type      : 'textarea',
        onblur    : 'submit',
        tooltip   : bpmh_notepad_var.tooltip,
		placeholder : bpmh_notepad_var.tooltip
     });
 });