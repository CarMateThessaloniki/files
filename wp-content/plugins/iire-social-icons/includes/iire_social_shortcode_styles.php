<?php 
header('Content-type: text/css');   

	echo '#emaildialog { font-size: 11px; }';
	echo '#emaildialog input, #emaildialog textarea { padding: 5px; width: 95%; margin-bottom: 10px; }';			
	echo 'p.email_message { text-align: center; }';
	echo 'p.email_message.error{ color: #CC0000; font-weight: bold; }';

	// Shortcode Width
	$wid = 'width: '.$_GET['w'].'px; ';
	if ($_GET['sresp'] == '1') {
		$wid = 'width: 100%; ';
	}		
	
	// Shortcode Height	
	$hgt = 'height: '.$_GET['h'].'px; ';
	
	// Shortcode Vertical
	if ($_GET['o'] == 'vertical') {
		$sz = $_GET['sz'];		
		$sp = $_GET['sp'];
		if ($_GET['ds'] == '1') { 		
			$vw = $sz + $sp;
		} else {
			$vw = $sz;		
		}	
		$vh = ($sz * 7) + ($sp * 7);
		$wid = 'width: '.$vw.'px; '; 
		$hgt = 'height: '.$vh.'px; ';							
	}	

	// Shortcode Padding
	if ($_GET['p'] != '') {		
		$p = explode(',',$_GET['p']);
		$pt = $p[0];
		$pr = $p[1];
		$pb = $p[2];
		$pl = $p[3];						
		$pad = 'padding: '.$p[0].'px '.$p[1].'px '.$p[2].'px '.$p[3].'px; ';
	} else {
		$pad = '';		
	}

	// Shortcode Margin		
	if ($_GET['m'] != '') {		
		$m = explode(',',$_GET['m']);
		$mt = $m[0];
		$mr = $m[1];
		$mb = $m[2];
		$ml = $m[3];						
		$mar = 'margin: '.$m[0].'px '.$m[1].'px '.$m[2].'px '.$m[3].'px; ';
	} else {
		$mar = '';			
	}			

	// Shortcode Background Color?
	if ( $_GET['wbk'] == '1' ) {		
		$wbgc = 'background-color:#'.$_GET['wbgc'].'; ';
	} else {
		$wbgc = '';			
	}	

	// Shortcode Border Width/Color
	if ( $_GET['wbrs'] != '0' ) {		
		$bor = 'border:#'. $_GET['wbrc'].' '. $_GET['wbrs'].'px solid;';
	} else {
		$bor = '';			
	}	
		
	echo 'div.iire_social_shortcode { position:relative; '.$wid.$hgt.$pad.$mar.$wbgc.$bor.' }';			


	// Shortcode Orientation
	if ($_GET['o'] == 'horizontal') {
		if ($_GET['a'] == 'left') {
			echo 'div.iire_social_shortcode .horizontal { float:left; text-align:left; }';	
		} else {	
			echo 'div.iire_social_shortcode .horizontal { float:right; text-align:right; }';
		}		
	} else {	
		echo 'div.iire_social_shortcode .vertical { float:none; }';
	}		


	$theme = $_GET['theme'];
	$sz = $_GET['sz'];


	// Shortcode Icons & Background Colors
	if ($_GET['bgc'] == '1') {	
		$bg = ' bgcolor';
		$bup = '#'.$_GET['bup'];
		$bov = '#'.$_GET['bov']; 
		echo 'div.iire_social_shortcode .icon'.$sz.'.'.$theme.' { background-color: '.$bup.'; background-image: url('.$_GET['pluginurl'].'themes/'.$theme.'/'.$sz.'_sprite.png); }';
		echo 'div.iire_social_shortcode .icon'.$sz.'.'.$theme.':hover { background-color:'.$bov.'; }';			
	} else {
		$bg = '';
		echo 'div.iire_social_shortcode .icon'.$sz.'.'.$theme.' { background-color: none; background-image: url('.$_GET['pluginurl'].'themes/'.$theme.'/'.$sz.'_sprite.png); }';
		echo 'div.iire_social_shortcode .icon'.$sz.'.'.$theme.':hover { background-color: none; }';			 			
	}		
		


	// Shortcode Icon Spacing			
	echo 'div.iire_social_shortcode .sp'.$_GET['sp'].' { margin:0px '.$_GET['sp'].'px '.$_GET['sp'].'px 0px; }';
	
	
	// Shortcode Icon Dropshadow	
	if ($_GET['ds'] == '1') { 
		$dshz = $_GET['dshz']; 		
		$dsvt = $_GET['dsvt']; 
		$dsblur = $_GET['dsblur']; 						
		$dscolor = $_GET['dscolor']; 
		echo'div.iire_social_shortcode .dropshadow { -moz-box-shadow: '.$dshz.'px '.$dsvt.'px '.$dsblur.'px #'.$dscolor.'; -webkit-box-shadow: '.$dshz.'px '.$dsvt.'px '.$dsblur.'px #'.$dscolor.'; box-shadow: '.$dshz.'px '.$dsvt.'px '.$dsblur.'px #'.$dscolor.'; }';
	}		
	

	// Shortcode Icon Rounded Corners		
	if ($_GET['rc'] == '1') {
		$rc = 'div.iire_social_shortcode roundedcorners';
		$rctl = $_GET['rctl'];
		$rctr = $_GET['rctr']; 
		$rcbl = $_GET['rcbl']; 
		$rcbr = $_GET['rcbr'];  		

		echo 'div.iire_social_shortcode .roundedcorners {';
	
		if ($rctl == $rctr && $rctl == $rcbl) {
			echo '-moz-border-radius: '.$rctl.'px; ';
			echo '-webkit-border-radius: '.$rctl.'px; ';
			echo 'border-radius: '.$rctl.'px; ';											
		} else {
			echo '-moz-border-radius-topleft: '.$rctl.'px; ';
			echo '-moz-border-radius-topright: '.$rctr.'px; ';
			echo '-moz-border-radius-bottomleft: '.$rcbl.'px; ';
			echo '-moz-border-radius-bottomright: '.$rcbr.'px; ';						
			echo '-webkit-border-top-left-radius: '.$rctl.'px; ';
			echo '-webkit-border-top-right-radius: '.$rctr.'px; '; 
			echo '-webkit-border-bottom-left-radius: '.$rcbl.'px; '; 
			echo '-webkit-border-bottom-right-radius: '.$rcbr.'px; ';
			echo 'border-top-left-radius: '.$rctl.'px; ';
			echo 'border-top-right-radius: '.$rctr.'px; ';
			echo 'border-bottom-left-radius: '.$rcbl.'px; ';		
			echo 'border-bottom-right-radius: '.$rcbr.'px; ';							
		}	
					 
		echo '}';
	}
	
	// Shortcode Icon Opacity
	$opacity = $_GET['op'];	
	if ($opacity >= 10 && $opacity < 100) { 
		$opval = $opacity/100;
		echo'div.iire_social_shortcode .opacity { opacity:'.$opval.'; }';			
	}
	
	// Shortcode Icon Glow	
	if ($_GET['seff'] == 'glow') { 
		$sz = $_GET['sz'];
		if ($sz == '64') { $hb = '20'; }
		if ($sz == '48') { $hb = '16'; }
		if ($sz == '32') { $hb = '12'; }
		if ($sz == '24') { $hb = '8'; }		
		if ($sz == '16') { $hb = '4'; }	
		$bov = '#'.$_GET['bov']; 	
		echo'div.iire_social_shortcode .addglow { -moz-box-shadow: 0 0 '.$hb.'px '.$bov.'; -webkit-box-shadow:  0 0 '.$hb.'px '.$bov.'; box-shadow:  0 0 '.$hb.'px '.$bov.'; filter: progid:DXImageTransform.Microsoft.Glow(Color='.$bov.',Strength=3); }';
	}	
?>	