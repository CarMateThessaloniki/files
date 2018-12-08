// IIRE SOCIAL (Demo Version) - 06-12-2013

jQuery(document).ready(function() {

// SEND EMAIL
	jQuery('div#iire-email').bind('click', function(e) {
		e.preventDefault();	
		var recipient = jQuery("input#email_recipient").val();		
		if (recipient == undefined) {
			alert('Site Administrator - Please specify a recipient in the Email Settings!');	
			return;	
		}
		
		jQuery("#emaildialog").dialog({
			resizable: false,
			draggable: true,			
			width: 320,									
			height: 385,
			modal: true,
			buttons: {
				"Send Message": function() {
					var en = jQuery("input#email_name").val();
					if (en == '- Your Name -') {
						jQuery("p.email_message").text("Please enter your name!").addClass('error');						
						return;
					}
					
					var es = jQuery("input#email_sender").val();
					if (es == '- Your Email -') {
						jQuery("p.email_message").text("Please enter your email address!").addClass('error');						
						return;
					}					

					var em = jQuery("textarea#email_message").val();
					if (em == 'Add your message here...') {
						jQuery("p.email_message").text("Please enter a message!").addClass('error');						
						return;
					}						

					var recipient = jQuery("input#email_recipient").val();
					var cc = jQuery("input#email_cc").val();
					var bcc = jQuery("input#email_bcc").val();
					var subject = jQuery("input#email_subject").val();
					var pluginurl = jQuery("input#plugin_url").val();
					var dataString = "fnln="+en+"&sender="+es+"&recipient="+recipient+"&cc="+cc+"&bcc="+bcc+"&subject="+subject+"&message="+em;

					jQuery.ajax({
						type: "POST",
						url: ""+ pluginurl+"/includes/iire_send_email.php",
   			 			data: dataString,				
						success: function(msg){
							alert(msg);
						}
					});
					jQuery(this).dialog("close");
				},
				Cancel: function() {
					jQuery("p.email_message").text("Enter your name, email address and a message.").removeClass('error');	
					jQuery( this ).dialog( "close" );
				}			
			}			
		});		

	});
	
	
	// ADD TO FAVORITES 1
	jQuery('div#iire-favorite1').bind('click', function(e) {
		e.preventDefault();	
		addiiReBookmark();
	});
	
	// ADD TO FAVORITES 2
	jQuery('div#iire-favorite2').bind('click', function(e) {
		e.preventDefault();	
		addiiReBookmark();
	});	
	
	function addiiReBookmark() {
		var sURL = location.href;
		var sTitle = document.title;
		var userAgent = navigator.userAgent.toLowerCase();
		var userBrowserName  = navigator.appName.toLowerCase();
		jQuery.browser = {
			version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1],
			safari: /webkit/.test( userAgent ),
			opera: /opera/.test( userAgent ),
			msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
			mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent ),
			name:userBrowserName
		};
		
		if (window.chrome) {
  			alert ('Press Ctrl+D to bookmark this page in Google Chrome.');			
			return;
		}		
		if (jQuery.browser.safari == true) {
			alert ('Press Ctrl+D (Command+D) to bookmark this page in Apple Safari.');
			return;
		}
		if (jQuery.browser.opera == true) {
			alert ('Press Ctrl+D to bookmark this page in Opera.');
			return;
		}
		if (jQuery.browser.msie == true) {
 			window.external.AddFavorite (sURL,sTitle);			
			return;
		}
		if (jQuery.browser.mozilla == true) {
			window.sidebar.addPanel(sTitle,sURL, "");			
			return;
		}
	}

	// PRINT
	jQuery('div#iire-print').bind('click', function(e) {						
		e.preventDefault();
		window.print();		
	});

	// ICON OPACITY ON MOUSE ENTER
	jQuery('div.opacity').live('mouseenter', function() {
		var eff = jQuery(this).parents("div:first").attr("data-effect");	
		if (eff != 'fadein') {												  
			jQuery(this).css("opacity", "1.00");
		}	
	});	

	// ICON OPACITY ON MOUSE OUT
	jQuery('div.opacity').live('mouseout', function() {
		var eff = jQuery(this).parents("div:first").attr("data-effect");
		if (eff != 'fadein') {
			var op = jQuery(this).parents("div:first").attr("data-opacity");
			jQuery(this).css("opacity", op);
		}
	});	
	

	jQuery(window).load(function(){	 
		jQuery("div.fadeout").css("opacity", "1.00");
	});

	// ICON EFFECT - FADE IN
	jQuery('div.fadein').live('mouseenter', function() {
  		jQuery(this).stop().animate({opacity: 1.0}, 200);		
	});
    jQuery("div.fadein").live('mouseout', function() {
		var opac = jQuery(this).parents("div:first").attr("data-opacity");	
		jQuery(this).stop().animate({opacity: opac}, 200);
    });


	// ICON EFFECT - FADE OUT
	jQuery('div.fadeout').live('mouseenter', function() {
		var opac = jQuery(this).parents("div:first").attr("data-opacity");	
		jQuery(this).stop().animate({ opacity: opac}, 200);													  
	});
   	jQuery("div.fadeout").live('mouseout', function() {
 		jQuery(this).stop().animate({ opacity: 1.0}, 200);	
   	});

	
	// ICON EFFECT - SHAKE
	jQuery('div.shake').live('mouseenter', function() {
		var sp = jQuery(this).parents("div:first").attr("data-spacing");
		var sh1 = parseInt(sp)+3;
		var sh2 = parseInt(sp)+2;		
		var sh3 = parseInt(sp)+1;		
		jQuery(this).stop().animate({ marginLeft: "-3px", marginRight: ""+sh1+"px" }, 40).animate({  marginLeft: ""+sh1+"px", marginRight: "-3px"  }, 40);
		jQuery(this).stop().animate({ marginLeft: "-2px", marginRight: ""+sh2+"px" }, 40).animate({  marginLeft: ""+sh2+"px", marginRight: "-2px"  }, 40);
		jQuery(this).stop().animate({ marginLeft: "-1px", marginRight: ""+sh3+"px" }, 40).animate({  marginLeft: ""+sh3+"px", marginRight: "-1px"  }, 40);
		jQuery(this).stop().animate({ marginLeft: "0px", marginRight: ""+sp+"px" }, 40);		
	});


	// ICON EFFECT - BOUNCE
	jQuery('div.bounce').live('mouseenter', function() {
		var sz = jQuery(this).parents("div:first").attr("data-size");
		if (sz == '64') { var hv = '8'; var hb = '5'; }
		if (sz == '48') { var hv = '6'; var hb = '4'; }
		if (sz == '32') { var hv = '5'; var hb = '3'; }
		if (sz == '24') { var hv = '4'; var hb = '2'; }		
		if (sz == '16') { var hv = '2'; var hb = '1'; }
		
		var sp = jQuery(this).parents("div:first").attr("data-spacing");
		var id = jQuery(this).parents("div:first").attr("id");
		if (id == "iire_social_shortcode") {
			jQuery(this).stop().animate({ marginTop: "-"+hv+"px" }, 40).animate({ marginTop: "0px" }, 40).animate({ marginTop: "-"+hb+"px" }, 40).animate({ marginTop: "0px" }, 40);
		} else {
			jQuery(this).stop().animate({ marginBottom: "-"+hv+"px" }, 40).animate({ marginBottom: ""+sp+"px" }, 40).animate({ marginBottom: "-"+hb+"px" }, 40).animate({ marginBottom: ""+sp+"px" }, 40);	
		}
	});


	// ICON EFFECT - DROP
	jQuery('div.drop').live('mouseenter', function() {
		var sz = jQuery(this).parents("div:first").attr("data-size");
		if (sz == '64') { var hb = '5'; }
		if (sz == '48') { var hb = '4'; }
		if (sz == '32') { var hb = '3'; }
		if (sz == '24') { var hb = '2'; }		
		if (sz == '16') { var hb = '1'; }		
		var id = jQuery(this).parents("div:first").attr("id");		
		if (id == "iire_social_shortcode") {
			jQuery(this).stop().animate({ marginTop: ""+sz+"px", height: ""+hb+"px" }, 200).animate({ marginTop: "0px", height: ""+sz+"px" }, 400);
		} else {
			jQuery(this).stop().animate({ height: ""+hb+"px" }, 200).animate({ height: ""+sz+"px" }, 400);
		}		
	});


	// ICON EFFECT - HIGHLIGHT
	jQuery('div.highlight').live('mouseenter', function() {
		jQuery(this).stop().animate({ 'opacity': '0.4'}, 200).animate({ 'opacity': '1' }, 200);
	});


	// ICON EFFECT - GLOW
	jQuery('div.glow').live('mouseenter', function() {
		jQuery(this).stop().addClass('addglow');
	});
	jQuery('div.glow').live('mouseleave', function() {   
		jQuery(this).stop().removeClass('addglow');		
	});
	jQuery('div.glow').live('click', function() {  //For Mobile Devices
		jQuery(this).delay(500).removeClass('addglow');	
	});	


	// ICON EFFECT - FLIP HORIZONTAL
	jQuery('div.fliphz').live('mouseenter', function() {
		jQuery(this).css({'-moz-transform': 'scaleX(-1)', '-o-transform': 'scaleX(-1)', '-webkit-transform': 'scaleX(-1)', 'transform': 'scaleX(-1)', 'filter': 'FlipH', '-ms-filter': 'FlipH', '-webkit-transition': 'all 1s', '-moz-transition': 'all 1s',  '-o-transition-duration': '1s' });
	});
	jQuery('div.fliphz').live('mouseleave', function() {   
		jQuery(this).css({'-moz-transform': 'scaleX(1)', '-o-transform': 'scaleX(1)', '-webkit-transform': 'scaleX(1)', 'transform': 'scaleX(1)', 'filter': 'FlipH', '-ms-filter': 'FlipH', '-webkit-transition': 'all 1s', '-moz-transition': 'all 1s',  '-o-transition-duration': '1s' });		
	});	

	// ICON EFFECT - FLIP VERTICAL
	jQuery('div.flipvt').live('mouseenter', function() {
		jQuery(this).css({'-moz-transform': 'scaleY(-1)', '-o-transform': 'scaleY(-1)', '-webkit-transform': 'scaleY(-1)', 'transform': 'scaleY(-1)', 'filter': 'FlipV', '-ms-filter': 'FlipV', '-webkit-transition': 'all 1s', '-moz-transition': 'all 1s',  '-o-transition-duration': '1s' });
	});
	jQuery('div.flipvt').live('mouseleave', function() {   
		jQuery(this).css({'-moz-transform': 'scaleY(1)', '-o-transform': 'scaleY(1)', '-webkit-transform': 'scaleY(1)', 'transform': 'scaleY(1)', 'filter': 'FlipV', '-ms-filter': 'FlipV', '-webkit-transition': 'all 1s', '-moz-transition': 'all 1s',  '-o-transition-duration': '1s' });		
	});	
	

	// ICON EFFECT - ROTATE
	jQuery('div.rotate').live('mouseenter', function() {
		var $ico = jQuery(this);
     	rotate(20);
     	function rotate(degree) {
		    $ico.css({ WebkitTransform: 'rotate(' + degree + 'deg)'});
		    $ico.css({ '-ms-transform': 'rotate(' + degree + 'deg)'});			
		    $ico.css({ '-moz-transform': 'rotate(' + degree + 'deg)'});
		    $ico.css({ '-o-transform': 'rotate(' + degree + 'deg)'});
		    $ico.css({ 'transform': 'rotate(' + degree + 'deg)'});
    		if (degree < 360) {// Animate rotation with a recursive call
				t = setTimeout(function() { rotate(++degree); },5);
  			}
		}
	});
	jQuery('div.rotate').live('mouseleave', function() {
		clearTimeout(t);
		var $ico = jQuery(this);		
     	rotate(20);		
     	function rotate(degree) {
		    $ico.css({ WebkitTransform: 'rotate(' + degree + 'deg)'});
		    $ico.css({ '-ms-transform': 'rotate(' + degree + 'deg)'});				
		    $ico.css({ '-moz-transform': 'rotate(' + degree + 'deg)'});
		    $ico.css({ '-o-transform': 'rotate(' + degree + 'deg)'});
		    $ico.css({ 'transform': 'rotate(' + degree + 'deg)'});
    		if (degree > 0 && degree < 360) {// Animate rotation with a recursive call
				x = setTimeout(function() { rotate(--degree); },10);
  			}
		}	
	});	
	

	// ICON EFFECT - SCALE EXPAND
	jQuery('div.expand').live('mouseenter', function() {
		jQuery(this).css({ '-ms-transform': 'scale(1.15, 1.15)', '-moz-transform': 'scale(1.15, 1.15)', '-o-transform': 'scale(1.15, 1.15)', '-webkit-transform': 'scale(1.15, 1.15)', 'transform': 'scale(1.15, 1.15)', '-webkit-transition': 'all 0.25s', '-moz-transition': 'all 0.25s', '-o-transition-duration': '0.25s' });
	});
	jQuery('div.expand').live('mouseleave', function() {   
		jQuery(this).css({'-ms-transform': 'scale(1.0, 1.0)', '-moz-transform': 'scale(1.0, 1.0)', '-o-transform': 'scale(1.0, 1.0)', '-webkit-transform': 'scale(1.0, 1.0)', 'transform': 'scale(1.0, 1.0)', '-webkit-transition': 'all 0.25s', '-moz-transition': 'all 0.25s', '-o-transition-duration': '0.25s' });		
	});	
	

	// ICON EFFECT - SCALE SHRINK
	jQuery('div.shrink').live('mouseenter', function() {
		jQuery(this).css({ '-ms-transform': 'scale(0.85, 0.85)', '-moz-transform': 'scale(0.85, 0.85)', '-o-transform': 'scale(0.85, 0.85)', '-webkit-transform': 'scale(0.85, 0.85)', 'transform': 'scale(0.85, 0.85)', '-webkit-transition': 'all 0.25s', '-moz-transition': 'all 0.25s', '-o-transition-duration': '0.25s' });
	});
	jQuery('div.shrink').live('mouseleave', function() {   
		jQuery(this).css({ '-ms-transform': 'scale(1.0, 1.0)', '-moz-transform': 'scale(1.0, 1.0)', '-o-transform': 'scale(1.0, 1.0)', '-webkit-transform': 'scale(1.0, 1.0)', 'transform': 'scale(1.0, 1.0)', '-webkit-transition': 'all 0.25s', '-moz-transition': 'all 0.25s', '-o-transition-duration': '0.25s' });		
	});	

}); // End Document Ready