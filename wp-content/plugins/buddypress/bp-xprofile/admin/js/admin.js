function add_option(forWhat) {
	var holder    = document.getElementById(forWhat + "_more");
	var theId     = document.getElementById(forWhat + '_option_number').value;
	var newDiv    = document.createElement('p');
	var newOption = document.createElement('input');
	var span      = document.createElement( 'span' );
	var txt       = document.createTextNode( "\u00A0\u039E\u00A0" );
	var isDefault = document.createElement( 'input' );
	var span1     = document.createElement( 'span' );
	var txt1      = document.createTextNode( " Default Value " );

	newDiv.setAttribute('id', forWhat + '_div' + theId);
	newDiv.setAttribute('class', 'sortable');

	newOption.setAttribute( 'type', 'text' );
	newOption.setAttribute( 'name', forWhat + '_option[' + theId + ']' );
	newOption.setAttribute( 'id', forWhat + '_option' + theId );

	span.appendChild( txt );

	if ( forWhat == 'checkbox' || forWhat == 'multiselectbox' ) {
		isDefault.setAttribute( 'type', 'checkbox' );
		isDefault.setAttribute( 'name', 'isDefault_' + forWhat + '_option[' + theId + ']' );
	} else {
		isDefault.setAttribute( 'type', 'radio' );
		isDefault.setAttribute( 'name', 'isDefault_' + forWhat + '_option' );
	}

	isDefault.setAttribute( 'value', theId );

	span1.appendChild( txt1 );

	var toDelete     = document.createElement( 'a' );
	var toDeleteText = document.createTextNode( '[x]' );

	toDelete.setAttribute( 'href', "javascript:hide('" + forWhat + '_div' + theId + "')" );
	toDelete.setAttribute( 'class', 'delete' );
	toDelete.appendChild( toDeleteText );

	newDiv.appendChild( span );
	newDiv.appendChild( newOption );
	newDiv.appendChild( document.createTextNode( " " ) );
	newDiv.appendChild( isDefault );
	newDiv.appendChild( span1 );
	newDiv.appendChild( toDelete );
	holder.appendChild( newDiv );

	// re-initialize the sorable ui
	enableSortableFieldOptions( forWhat );

	// set focus on newly created element
	document.getElementById(forWhat + "_option" + theId).focus();

	theId++;

	document.getElementById(forWhat + "_option_number").value = theId;
}

function show_options(forWhat) {
	document.getElementById( 'radio'          ).style.display = 'none';
	document.getElementById( 'selectbox'      ).style.display = 'none';
	document.getElementById( 'multiselectbox' ).style.display = 'none';
	document.getElementById( 'checkbox'       ).style.display = 'none';

	if ( forWhat == 'radio' )
		document.getElementById( 'radio' ).style.display = "";

	if ( forWhat == 'selectbox' )
		document.getElementById( 'selectbox' ).style.display = "";

	if ( forWhat == 'multiselectbox' )
		document.getElementById( 'multiselectbox' ).style.display = "";

	if ( forWhat == 'checkbox' )
		document.getElementById( 'checkbox' ).style.display = "";
}

function hide( id ) {
	if ( !document.getElementById( id ) ) return false;

	document.getElementById( id ).style.display = "none";
	// the field id is [fieldtype]option[iterator] and not [fieldtype]div[iterator]
	field_id = id.replace( 'div', 'option' ); 
	document.getElementById( field_id ).value = '';
}

var fixHelper = function(e, ui) {
	ui.children().each(function() {
		jQuery(this).width( jQuery(this).width() );
	});
	return ui;
};

function enableSortableFieldOptions( forWhat ) {
	if ( jQuery( '#' + forWhat + ' p.sortable' ).length > 1 ) {
		jQuery( '.bp-options-box' ).sortable( {
			items: 'p.sortable',
			tolerance: 'pointer',
			axis: 'y',
			handle: 'span'
		});

		jQuery( '.sortable span' ).css( 'cursor', 'move' );
	}
}

function destroySortableFieldOptions() {
	jQuery( '.bp-options-box' ).sortable( 'destroy' );
	jQuery( '.sortable span' ).css( 'cursor', 'default' );
}

jQuery( document ).ready( function() {

	// Set focus in Field Title, if we're on the right page
	jQuery( '#bp-xprofile-add-field #title' ).focus();
	
	// Set up deleting options ajax
	jQuery( 'a.ajax-option-delete' ).on( 'click', function() {
		var theId = this.id.split( '-' );
		theId = theId[1];

		jQuery.post( ajaxurl, {
			action: 'xprofile_delete_option',
			'cookie': encodeURIComponent( document.cookie ),
			'_wpnonce': jQuery('input#_wpnonce').val(),
			'option_id': theId
		},
		function( response ) {} );
	} );

	//
	jQuery( '[id^="sort_order_"]' ).change(function() {
		if ( jQuery( this ).val() != 'custom' ) {
			destroySortableFieldOptions();
		} else {
			enableSortableFieldOptions( jQuery('#fieldtype :selected').val() );
		}
	});

	// Show object if JS is enabled
	jQuery( 'ul#field-group-tabs' ).show();

	// Allow reordering of field group tabs
	jQuery( 'ul#field-group-tabs' ).sortable( {
		cursor: 'move',
		axis: 'x',
		opacity: 0.6,
		items: 'li',
		tolerance: 'pointer',

		update: function() {
			jQuery.post( ajaxurl, {
				action: 'xprofile_reorder_groups',
				'cookie': encodeURIComponent( document.cookie ),
				'_wpnonce_reorder_groups': jQuery( 'input#_wpnonce_reorder_groups' ).val(),
				'group_order': jQuery( this ).sortable( 'serialize' )
			},
			function( response ) {} );
		}
	}).disableSelection();

	// Allow reordering of fields within groups
	jQuery( 'fieldset.field-group' ).sortable({
		cursor: 'move',
		opacity: 0.3,
		items: 'fieldset',
		tolerance: 'pointer',

		update: function() {
			jQuery.post( ajaxurl, {
				action: 'xprofile_reorder_fields',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce_reorder_fields': jQuery( 'input#_wpnonce_reorder_fields' ).val(),
				'field_order': jQuery(this).sortable( 'serialize' ),
				'field_group_id': jQuery(this).attr( 'id' )
			},
			function( response ) {} );
		}
	})

	// Disallow text selection
	.disableSelection()

	// Change cursor to move if JS is enabled
	.css( 'cursor', 'move' );

	// Allow reordering of field options
	enableSortableFieldOptions( jQuery('#fieldtype :selected').val() );

	// tabs init with a custom tab template and an "add" callback filling in the content
	var $tab_items;
	var $tabs = jQuery( '#tabs' ).tabs();
	set_tab_items( $tabs );

	function set_tab_items( $tabs ) {
		$tab_items = jQuery( 'ul:first li', $tabs ).droppable({
			accept: '.connectedSortable fieldset',
			hoverClass: 'ui-state-hover',
			activeClass: 'ui-state-acceptable',
			touch: 'pointer',
			tolerance: 'pointer',

			// When field is dropped on tab
			drop: function( ev, ui ) {
				// The tab
				var $item = jQuery(this);

				// The tab body
				var $list = jQuery( $item.find( 'a' ).attr( 'href' ) ).find( '.connectedSortable' );

				// Remove helper class
				jQuery($item).removeClass( 'drop-candidate' );

				// Hide field, change selected tab, and show new placement
				ui.draggable.hide( 'slow', function() {

					// Select new tab as current
					$tabs.tabs( 'option', 'active', $tab_items.index( $item ) );

					// Show new placement
					jQuery(this).appendTo($list).show( 'slow' ).animate( {opacity: "1"}, 500 );

					// Refresh $list variable
					$list = jQuery( $item.find( 'a' ).attr( 'href' ) ).find( '.connectedSortable' );
					jQuery($list).find( 'p.nofields' ).hide( 'slow' );

					// Ajax update field locations and orders
					jQuery.post( ajaxurl, {
						action: 'xprofile_reorder_fields',
						'cookie': encodeURIComponent(document.cookie),
						'_wpnonce_reorder_fields': jQuery( "input#_wpnonce_reorder_fields" ).val(),
						'field_order': jQuery( $list ).sortable( 'serialize' ),
						'field_group_id': jQuery( $list ).attr( 'id' )
					},
					function( response ) {} );
				});
			},
			over: function( event, ui ) {
				jQuery(this).addClass( 'drop-candidate' );
			},
			out: function( event, ui ) {
				jQuery(this).removeClass( 'drop-candidate' );
			}
		});
	}
});
