<?php
// Admin Page for Social Icons Shortcode - (Demo Version) - 06-12-2013

function iire_admin_social_shortcode() {
	global $wpdb;
	global $blog_id;
	
	$table_name = $wpdb->get_blog_prefix($blog_id)."iire_social";

	// UPDATE OPTIONS
	if (isset($_POST['sc_icon_theme'])){
		foreach($_POST as $k=>$v){
			if ($k != 'tab') {
				$fields .= $k."='".mysql_escape_string(trim($v))."', ";
				$value = mysql_escape_string(trim($v));		
				$wpdb->query("UPDATE ".$table_name." SET option_value = '$value' WHERE option_name = '$k'");
			}	
		}	
	}		
	
	// GET SETTINGS
	$settings = array();		
	$rs = $wpdb->get_results("SELECT * FROM $table_name");
	foreach ($rs as $row) {
		$settings[$row->option_name] = $row->option_value;
	}		


	// Shortcode Theme
	if ($settings['sc_icon_theme'] == '') {
		$th = 'default';
	} else {
		$th = $settings['sc_icon_theme'];
	}		

	// Shortcode Container Orientation
	if ($settings['sc_orientation'] == 'horizontal') { $ot = 'horizontal';	} else { $ot = 'vertical'; }	

	// Shortcode Icon Size
	if ($settings['sc_icon_size'] == '') {
		$iconsize = 'icon32';
		$sz = '32';
	} else {
		$iconsize = 'icon'.$settings['sc_icon_size'];
		$sz = $settings['sc_icon_size'];						
	}		

	// Shortcode Icon Spacing	
	for ( $x = 0; $x <= 25; $x++ ) {
		if ($settings['sc_icon_spacing'] == $x) { $sp = 'sp'.$x; }
	}		

	// Shortcode Icon Dropshadow	
	if ($settings['sc_dropshadow'] == '1') { 
		$ds = ' dropshadow';
	} else {
		$ds = '';
	}		
	$dshz = $settings['sc_dropshadow_horizontal_offset']; 		
	$dsvt = $settings['sc_dropshadow_vertical_offset']; 
	$dsblur = $settings['sc_dropshadow_blur']; 						
	$dscolor = $settings['sc_dropshadow_color']; 

	// Shortcode Icon Rounded Corners		
	if ($settings['sc_roundedcorners'] == '1') {
		$rc = ' roundedcorners';
		$rctl = $settings['sc_roundedcorners_topleft'];
		$rctr = $settings['sc_roundedcorners_topright']; 
		$rcbl = $settings['sc_roundedcorners_bottomleft']; 
		$rcbr = $settings['sc_roundedcorners_bottomright']; 		
	} else {
		$rc = '';	
	}

	// Shortcode Icon Opacity
	$opacity = $settings['sc_icon_opacity'];	
	if ($opacity >= 10 && $opacity < 100) { 
		$op = ' opacity';
		$opval = $opacity/100;
	} else {
		$op = ' opacity';
		$opval = "100";		
	}	
	
	// Shortcode Icon Background Colors
	if ($settings['sc_icon_bgcolor'] == '1') {	
		$bg = ' bgcolor';
		$bup = '#'.$settings['sc_icon_bgcolor_up'];
		$bov = '#'.$settings['sc_icon_bgcolor_hover']; 
	} else {
		$bg = '';
		$bup = 'none';
		$bov = 'none'; 			
	}		
		
	//Add Classes											
	$addclasses = $iconsize.' '.$th.' '.$ot.' '.$sp.$ds.$rc.$op.$bg;
?>	

<style>
	div#viewport { width:auto; min-width:300px; min-height:235px; height:auto; padding:10px; background-color:#EDEDED; position:relative; top:0px; left:0px; background-image: url('<?php echo IIRE_SOCIAL_URL ?>/includes/images/preview_shortcode.png'); background-repeat:no-repeat; background-position: top right;}

	<?php echo $settings['css']; ?>	
	
	.opacity { opacity:<?php echo $opval; ?>; }

	.roundedcorners { 
	<?php 
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
	?>						 
	}

	.dropshadow { -moz-box-shadow: <?php echo $dshz; ?>px <?php echo $dsvt; ?>px <?php echo $dsblur; ?>px #<?php echo $dscolor; ?>; -webkit-box-shadow: <?php echo $dshz; ?>px <?php echo $dsvt; ?>px <?php echo $dsblur; ?>px #<?php echo $dscolor; ?>; box-shadow: <?php echo $dshz; ?>px <?php echo $dsvt; ?>px <?php echo $dsblur; ?>px #<?php echo $dscolor; ?>; }	

	/* CHOOSE ICONS */
	li.choose { width:24px; height:24px; margin:0px; padding:0px; display: inline-table; cursor:pointer; }		

	<?php
		$isz= explode(",", $settings['theme_sizes']);		
		$iszlen=count($isz);		
		
		$x = explode('/',IIRE_SOCIAL_BASENAME);
		$d = "../".IIRE_SOCIAL_CONTENT_URL."/plugins/".$x[0]."/themes/";	
		$subd = glob($d . "*");
		foreach($subd as $f) {
			if(is_dir($f)) {
				$theme = str_replace($d,'',$f);
				//CHOOSE ICONS - SIZES - HOVER COLOR
				for ( $x=0; $x < count($isz); $x++ ) {
					if ($x==0) {
						echo 'li.choose.'.$theme.' { background-image: url('.IIRE_SOCIAL_URL.'themes/'.$theme.'/24_sprite.png); }';	
					}
					echo '.icon'.$isz[$x].'.'.$theme.' { background-color: '.$bup.'; background-image: url('.IIRE_SOCIAL_URL.'themes/'.$theme.'/'.$isz[$x].'_sprite.png); }';
					echo '.icon'.$isz[$x].'.'.$theme.':hover { background-color: '.$bov.'; }';															
 				}	
 			}
		}			
	?>	

	div#iire_social_shortcode div.move:hover { background-color: <?php echo $bov; ?>; }
</style>



<div class="wrap" style="min-width:300px;">

<div id="icon_iire"><br></div>	
<h2>iiRe Social Icons - Shortcode Settings</h2>
<input type="hidden" id="plugin_path" name="plugin_path" value="<?php echo IIRE_SOCIAL_URL; ?>" >

<form id="settings" method="POST">

<div id="iire_social_panel_tab"><span>&raquo;</span></div>	<!-- RIGHT PANEL TAB-->

<div id="iire_social_panel_right">	<!-- START RIGHT PANEL -->

	<p id="btnholder" style="text-align:center; padding:0px; margin:20px 0px 20px 0px;"> <input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"> <a id="reset" class="reset button-secondary">Reset</a> <a id="preview" href="<?php echo get_option('siteurl'); ?>" target="_blank" class="preview button-secondary" title="Preview will launch in new tab/window!">Preview</a></p>

	<div id="right_panel">	<!-- Start Right Panel Container -->
	
		<!-- Start Theme-->
		<h3 id="icon_theme"><a href="#">Icon Theme</a></h3>
		<div>
			<p><select id="sc_icon_theme" name="sc_icon_theme" class="w185">
				<?php
				foreach($subd as $f) {
					if(is_dir($f)) {
						$theme = str_replace($d,'',$f);
						$theme_name = ucwords(str_replace('_',' ',$theme));
						echo '<option value="'.$theme.'" ';
						if ($settings['sc_icon_theme'] == $theme ) { echo 'selected'; } 
						echo '>'.$theme_name.'</option>';
 					}
				}
				?>																
			</select></p>
			
			<p><img class="icon_theme" src="<?php echo IIRE_SOCIAL_URL; ?>themes/<?php echo $settings['sc_icon_theme']; ?>/screenshot.png" width="185" border="0" /></p>
		</div>
	
		
		<!-- Start Icon Sizes -->
		<h3 id="icon_size"><a href="#">Icon Size &amp; Spacing</a></h3>
		<div>

 			<!-- Size -->
			<p><label>Icon Size:</label>			
			<select id="sc_icon_size" name="sc_icon_size" class="w50">
				<?php
				for ( $x=0; $x < count($isz); $x++ ) {
					echo '<option value="'.$isz[$x].'" ';
					if ($settings['sc_icon_size'] == $isz[$x]) { echo 'selected'; } 
					echo '>'.$isz[$x].'</option>';
 				}
				?>			
			</select> px
			</p>
			<div id="sc_size" class="slider"></div>
			
			<p>&nbsp;</p>
 			
			<!-- Spacing -->
			<p><label>Icon Spacing:</label>			
			<select id="sc_icon_spacing" name="sc_icon_spacing" class="w50">
				<?php
				for ( $x = 0; $x <= 25; $x++ ) {
					echo '<option value="'.$x.'" ';
					if ($settings['sc_icon_spacing'] == $x) { 
						echo 'selected';
					}
					echo '>'.$x.'</option>';
				}
				?>												
			</select> px
			</p>
			<div id="sc_spacing" class="slider"></div>
			
		</div>
 		<!-- End Icon Sizing -->


		<!-- Start Icon Styling -->
		<h3 id="icon_styling"><a href="#">Icon Styling</a></h3>
		<div>

 			<!-- Dropshadow -->
			<p><label>Drop Shadow?</label>		
			<select id="sc_dropshadow" name="sc_dropshadow" class="w70">
				<option value="0" <?php if ($settings['sc_dropshadow'] =='0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['sc_dropshadow'] =='1') { echo 'selected'; } ?>>Yes</option>				
			</select>
			</p>
			
			<p class="ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>"><label>Shadow Color:</label>	
			<input type="text" id="sc_dropshadow_color" name="sc_dropshadow_color" value="<?php echo $settings['sc_dropshadow_color']; ?>" class="w70 color ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>">
			</p>

			<p class="ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>"><label>Horizontal Offset:</label>
			<select id="sc_dropshadow_horizontal_offset" name="sc_dropshadow_horizontal_offset" class="w50 ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>">			
				<?php
				for ( $x = -10; $x <= 10; $x++ ) {
					echo '<option value="'.$x.'" ';
					if ($settings['sc_dropshadow_horizontal_offset'] == $x) { 
						echo 'selected';
					}
					echo '>'.$x.'</option>';
				}
				?>												
			</select> px
			</p>

			<p class="ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>"><label>Vertical Offset:</label>
			<select id="sc_dropshadow_vertical_offset" name="sc_dropshadow_vertical_offset" class="w50 ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>">			
				<?php
				for ( $x = -10; $x <= 10; $x++ ) {
					echo '<option value="'.$x.'" ';
					if ($settings['sc_dropshadow_vertical_offset'] == $x) { 
						echo 'selected';
					}
					if ($x == 0) {
						echo '>None</option>';
					} else {	
						echo '>'.$x.'</option>';
					}	
				}
				?>												
			</select> px
			</p>
			
			<p class="ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>"><label>Blur:</label>
			<select id="sc_dropshadow_blur" name="sc_dropshadow_blur" class="w50 ds <?php if ($settings['sc_dropshadow'] =='0') { echo 'hidden'; } ?>">			
				<?php
				for ( $x = 0; $x <= 20; $x++ ) {
					echo '<option value="'.$x.'" ';
					if ($settings['sc_dropshadow_blur'] == $x) { 
						echo 'selected';
					}
					echo '>'.$x.'</option>';
				}
				?>												
			</select> px
			</p>							

			<p class="ds <?php if ($settings['sc_dropshadow'] == '0') { echo 'hidden'; } ?>"><br /><br /></p>
			<p>&nbsp;</p>			

	
 			<!-- Rounded Corners -->
			<p><label>Rounded Corners?</label>			
			<select id="sc_roundedcorners" name="sc_roundedcorners" class="w70">
				<option value="0" <?php if ($settings['sc_roundedcorners'] == '0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['sc_roundedcorners'] == '1') { echo 'selected'; } ?>>Yes</option>				
			</select>
			</p>
			
			<p class="rc <?php if ($settings['sc_roundedcorners'] =='0') { echo 'hidden'; } ?>"><label>Top Left:</label>
			<input type="text" id="sc_roundedcorners_topleft" name="sc_roundedcorners_topleft" value="<?php echo $settings['sc_roundedcorners_topleft']; ?>" class="w50"> px</p>				
			</p>				

			<p class="rc <?php if ($settings['sc_roundedcorners'] =='0') { echo 'hidden'; } ?>"><label>Top Right:</label>
			<input type="text" id="sc_roundedcorners_topright" name="sc_roundedcorners_topright" value="<?php echo $settings['sc_roundedcorners_topright']; ?>" class="w50"> px</p>			
			</p>
			
			<p class="rc <?php if ($settings['sc_roundedcorners'] =='0') { echo 'hidden'; } ?>"><label>Bottom Left:</label>
			<input type="text" id="sc_roundedcorners_bottomleft" name="sc_roundedcorners_bottomleft" value="<?php echo $settings['sc_roundedcorners_bottomleft']; ?>" class="w50"> px</p>			
			</p>
			
			<p class="rc <?php if ($settings['sc_roundedcorners'] == '0') { echo 'hidden'; } ?>"><label>Bottom Right:</label>
			<input type="text" id="sc_roundedcorners_bottomright" name="sc_roundedcorners_bottomright" value="<?php echo $settings['sc_roundedcorners_bottomright']; ?>" class="w50"> px</p>
			</p>
			
			<p class="rc <?php if ($settings['sc_roundedcorners'] == '0') { echo 'hidden'; } ?>"><br /><br /></p>	
			
			
 			<!-- Background Color -->			
			<p><label>Background Color?</label>			
			<select id="sc_icon_bgcolor" name="sc_icon_bgcolor" class="w70">
				<option value="0" <?php if ($settings['sc_icon_bgcolor'] == '0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['sc_icon_bgcolor'] == '1') { echo 'selected'; } ?>>Yes</option>				
			</select>
			</p>

			<p class="bg <?php if ($settings['sc_icon_bgcolor'] == '0') { echo 'hidden'; } ?>"><label>Up State:</label>	
			<input type="text" id="sc_icon_bgcolor_up" name="sc_icon_bgcolor_up" value="<?php echo $settings['sc_icon_bgcolor_up']; ?>" class="w70 bg color <?php if ($settings['sc_icon_bgcolor'] == '0') { echo 'hidden'; } ?>"></p>

			<p class="bg <?php if ($settings['sc_icon_bgcolor'] =='0') { echo 'hidden'; } ?>"><label>Hover State:</label>	
			<input type="text" id="sc_icon_bgcolor_hover" name="sc_icon_bgcolor_hover" value="<?php echo $settings['sc_icon_bgcolor_hover']; ?>" class="w70 bg color <?php if ($settings['sc_icon_bgcolor'] == '0') { echo 'hidden'; } ?>"></p>
			
			<p class="bg <?php if ($settings['sc_icon_bgcolor'] =='0') { echo 'hidden'; } ?>"><br />Background colors are best used with the "Symbol" or "Cutouts" themes.</p>		

			<p>&nbsp;</p>

 			<!-- Effect -->			
			<p><label>Effect?</label>			
			<select id="sc_effect" name="sc_effect" class="w70">
				<option value="" <?php if ($settings['sc_effect'] == '') { echo 'selected'; } ?>>None</option>
				<option value="bounce" <?php if ($settings['sc_effect'] == 'bounce') { echo 'selected'; } ?>>Bounce</option>					
				<option value="drop" <?php if ($settings['sc_effect'] == 'drop') { echo 'selected'; } ?>>Drop</option>
				<option value="expand" <?php if ($settings['sc_effect'] == 'expand') { echo 'selected'; } ?>>Expand</option>									
				<option value="fadein" <?php if ($settings['sc_effect'] == 'fadein') { echo 'selected'; } ?>>Fade In</option>
				<option value="fadeout" <?php if ($settings['sc_effect'] == 'fadeout') { echo 'selected'; } ?>>Fade Out</option>
				<option value="fliphz" <?php if ($settings['sc_effect'] == 'fliphz') { echo 'selected'; } ?>>Flip Horizontal</option>
				<option value="flipvt" <?php if ($settings['sc_effect'] == 'flipvt') { echo 'selected'; } ?>>Flip Vertical</option>					
				<option value="glow" <?php if ($settings['sc_effect'] == 'glow') { echo 'selected'; } ?>>Glow</option>				
				<option value="highlight" <?php if ($settings['sc_effect'] == 'highlight') { echo 'selected'; } ?>>Highlight</option>
				<option value="rotate" <?php if ($settings['sc_effect'] == 'rotate') { echo 'selected'; } ?>>Rotate</option>							
				<option value="shake" <?php if ($settings['sc_effect'] == 'shake') { echo 'selected'; } ?>>Shake</option>
				<option value="shrink" <?php if ($settings['sc_effect'] == 'shrink') { echo 'selected'; } ?>>Shrink</option>					
			</select>
			</p>
					

			<p>&nbsp;</p>			
			

 			<!-- Opacity -->				
			<p><label>Icon Opacity:</label>	
			<input type="text" id="op" name="sc_icon_opacity" value="<?php echo $settings['sc_icon_opacity']; ?>" class="w50"> %</p>
			<div id="sc_opacity" class="slider"></div>					
			
							
		</div> 
		<!-- End Icons Styling -->
	

		 <!-- Start Icon Links -->
		<h3 id="icon_links"><a href="#">Icon Links</a></h3>
		<div>
 			<!-- Show Title? -->
			<p><label>Show Alt/Title?</label>			
			<select id="link_title" name="link_title" class="w70">
				<option value="0" <?php if ($settings['link_title'] =='0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['link_title'] =='1') { echo 'selected'; } ?>>Yes</option>				
			</select>
			</p>

			<p>&nbsp;</p>
			
 			<!-- New Window? -->
			<p><label>Open in New Window?</label>			
			<select id="link_target" name="link_target" class="w70">
				<option value="_self" <?php if ($settings['link_target'] =='_self') { echo 'selected'; } ?>>No</option>
				<option value="_blank" <?php if ($settings['link_target'] =='_blank') { echo 'selected'; } ?>>Yes</option>				
			</select>
			</p>
			
			<p>&nbsp;</p>
			
 			<!-- No Follow? -->
			<p><label>No Follow?</label>			
			<select id="link_nofollow" name="link_nofollow" class="w70">
				<option value="0" <?php if ($settings['link_nofollow'] =='0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['link_nofollow'] =='1') { echo 'selected'; } ?>>Yes</option>			
			</select>
			</p>			
		</div> 
		<!-- End Icon Links -->
	
		
		<!-- Start Icon Container -->	
		<h3 id="sc_container"><a href="#">Shortcode Container</a></h3>
		<div>
			<p><label class="w80">Type:</label>
			<select id="sc_orientation" name="sc_orientation" class="w100">
				<option value="horizontal" <?php if ($settings['sc_orientation'] =='horizontal') { echo 'selected'; } ?>>Horizontal</option>
				<option value="vertical" <?php if ($settings['sc_orientation'] =='vertical') { echo 'selected'; } ?>>Vertical</option>				
			</select>
			</p>
			
			<p>&nbsp;</p>
			
			<p><label>Alignment:</label>
			<select id="sc_align" name="sc_align" class="w70">
				<option value="left" <?php if ($settings['sc_align'] =='left') { echo 'selected'; } ?>>Left</option>
				<option value="right" <?php if ($settings['sc_align'] =='right') { echo 'selected'; } ?>>Right</option>	
			</select>
			</p>

			<p>&nbsp;</p>
									
			<p><label>Responsive:</label>
			<select id="sc_responsive" name="sc_responsive" class="w70">
				<option value="0" <?php if ($settings['sc_responsive'] =='0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['sc_responsive'] =='1') { echo 'selected'; } ?>>Yes</option>	
			</select>
			</p>
			
			<p class="srs <?php if ($settings['sc_responsive'] =='1') { echo 'hidden'; } ?>">&nbsp;</p>
															
			<p class="srs <?php if ($settings['sc_responsive'] =='1') { echo 'hidden'; } ?>"><label>Width:</label>
			<input type="text" id="ww" name="sc_width" value="<?php echo $settings['sc_width']; ?>" class="w50 srs"> px</p>
			<div id="sc_width" class="slider srs <?php if ($settings['sc_responsive'] =='1') { echo 'hidden'; } ?>"></div>
			
			<p>&nbsp;</p>	

			<p><label>Height:</label>
			<input type="text" id="wh" name="sc_height" value="<?php echo $settings['sc_height']; ?>" class="w50"> px</p>			
			<div id="sc_height" class="slider"></div>
			
			<p>&nbsp;</p>				
			
			<p><label>Padding Top:</label><input type="text" id="sc_pad_top" name="sc_pad_top" value="<?php echo $settings['sc_pad_top']; ?>" class="w35 inline"> px</p>
			<p><label>Padding Left:</label><input type="text" id="sc_pad_left" name="sc_pad_left" value="<?php echo $settings['sc_pad_left']; ?>" class="w35 inline"> px</p>
			<p><label>Padding Bottom:</label><input type="text" id="sc_pad_bottom" name="sc_pad_bottom" value="<?php echo $settings['sc_pad_bottom']; ?>" class="w35 inline"> px</p>		
			<p><label>Padding Right:</label><input type="text" id="sc_pad_right" name="sc_pad_right" value="<?php echo $settings['sc_pad_right']; ?>" class="w35 inline"> px</p>

			<p>&nbsp;</p>
			
			<p><label>Margin Top:</label><input type="text" id="sc_margin_top" name="sc_margin_top" value="<?php echo $settings['sc_margin_top']; ?>" class="w35 inline"> px</p>
			<p><label>Margin Left:</label><input type="text" id="sc_margin_left" name="sc_margin_left" value="<?php echo $settings['sc_margin_left']; ?>" class="w35 inline"> px</p>
			<p><label>Margin Bottom:</label><input type="text" id="sc_margin_bottom" name="sc_margin_bottom" value="<?php echo $settings['sc_margin_bottom']; ?>" class="w35 inline"> px</p>			
			<p><label>Margin Right:</label><input type="text" id="sc_margin_right" name="sc_margin_right" value="<?php echo $settings['sc_margin_right']; ?>" class="w35 inline"> px</p>
		</div>
		<!-- End Icon Container -->



		<!-- Start Shortcode Container -->
		<h3 id="sc_styling"><a href="#">Shortcode Container Styling</a></h3>
		<div>
			<!-- Add Background Color -->
			<p><label>Add Background?</label>
			<select id="sc_background" name="sc_background" class="w70">
				<option value="0" <?php if ($settings['sc_background'] == '0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['sc_background'] == '1') { echo 'selected'; } ?>>Yes</option>			
			</select>
			</p>

			<p class="addbg <?php if ($settings['sc_background'] == '0') { echo 'hidden'; } ?>">&nbsp;</p>
		
		
			<p class="addbg <?php if ($settings['sc_background'] == '0') { echo 'hidden'; } ?>"><label>Background Color:</label>	
			<input type="text" id="sc_bg_color" name="sc_bg_color" value="<?php echo $settings['sc_bg_color']; ?>" class="w70 color"></p>

			<p class="addbg <?php if ($settings['sc_background'] == '0') { echo 'hidden'; } ?>">&nbsp;</p>

			<p class="addbg <?php if ($settings['sc_background'] == '0') { echo 'hidden'; } ?>"><label>Border Size:</label>
			<select id="sc_border_size" name="sc_border_size" class="w50">
				<option value="0" <?php if ($settings['sc_border_size'] == '0') { echo 'selected'; } ?>>0</option>
				<option value="1" <?php if ($settings['sc_border_size'] == '1') { echo 'selected'; } ?>>1</option>
				<option value="2" <?php if ($settings['sc_border_size'] == '2') { echo 'selected'; } ?>>2</option>
				<option value="3" <?php if ($settings['sc_border_size'] == '3') { echo 'selected'; } ?>>3</option>
				<option value="4" <?php if ($settings['sc_border_size'] == '4') { echo 'selected'; } ?>>4</option>	
				<option value="5" <?php if ($settings['sc_border_size'] == '5') { echo 'selected'; } ?>>5</option>
				<option value="6" <?php if ($settings['sc_border_size'] == '6') { echo 'selected'; } ?>>6</option>
				<option value="7" <?php if ($settings['sc_border_size'] == '7') { echo 'selected'; } ?>>7</option>
				<option value="8" <?php if ($settings['sc_border_size'] == '8') { echo 'selected'; } ?>>8</option>
				<option value="9" <?php if ($settings['sc_border_size'] == '9') { echo 'selected'; } ?>>9</option>
				<option value="10" <?php if ($settings['sc_border_size'] == '10') { echo 'selected'; } ?>>10</option>																																																
			</select> px
			</p>			

			<p class="addbg wbs <?php if ($settings['sc_background'] == '0' || $settings['sc_border_size'] == '0' ) { echo 'hidden'; } ?>"><label>Border Color:</label>	
			<input type="text" id="sc_border_color" name="sc_border_color" value="<?php echo $settings['sc_border_color']; ?>" class="w70 color"></p>


			<p>&nbsp;</p>
			
			<p>Custom CSS:</p>				
			<textarea id="sc_css" name="sc_css" cols="20" rows="3" class="w100p h120"><?php echo $settings['sc_css']; ?></textarea>	
		</div>
		<!-- End Shortcode Container -->		


 		<!-- Start Email -->		
		<h3 id="email"><a href="#">Email Settings</a></h3>
		<div>
			<!-- Recipient -->
			<p><label>Recipient Email:</label>
			<input type="text" id="email_recipient" name="email_recipient" value="<?php echo $settings['email_recipient']; ?>" class="w100p">
			</p>

 			<!-- CC -->
			<p><label>CC:</label>			
			<input type="text" id="email_cc" name="email_cc" value="<?php echo $settings['email_cc']; ?>" class="w100p">
			</p>
			
 			<!-- BCC -->
			<p><label>BCC:</label>			
			<input type="text" id="email_bcc" name="email_bcc" value="<?php echo $settings['email_bcc']; ?>" class="w100p">
			</p>

 			<!-- Subject -->
			<p><label>Subject:</label>			
			<input type="text" id="email_subject" name="email_subject" value="<?php echo $settings['email_subject']; ?>" class="w100p">
			</p>
			
 			<!-- Message -->
			<p><label>Message:</label>	
			<textarea id="email_message" name="email_message" cols="20" rows="3" class="w100p h120"><?php echo $settings['email_message']; ?></textarea>						
			</p>			
		</div>	
		<!-- End Email -->
		
		
 		<!-- Start General -->		
		<h3 id="general"><a href="#">General Settings</a></h3>
		<div>

			<!-- Add This Sharing -->
			<p><label>Add This?</label>
			<select id="addthis" name="addthis" class="w70">
				<option value="0" <?php if ($settings['addthis'] =='0') { echo 'selected'; } ?>>No</option>
				<option value="1" <?php if ($settings['addthis'] =='1') { echo 'selected'; } ?>>Yes</option>		
			</select></p>
			<p class="addthis2 <?php if ($settings['addthis'] == '1') { echo 'hidden'; } ?>"><br />Included the Add This sharing code with your shortcode?</p>
							
			<p class="addthis <?php if ($settings['addthis'] == '0') { echo 'hidden'; } ?>"><label>Analytics Key:</label>
			<input type="text" id="addthis_key" name="addthis_key" value="<?php echo $settings['addthis_key']; ?>" class="w100p">
			<br />For more information, please visit <a href="http://addthis.com" target="_blank">http://addthis.com</a></p>										
		</div>	
		<!-- End General -->
		
		
 		<!-- Start Registration -->		
		<h3 id="registration"><a href="#">Registration</a></h3>
		<div>
			<p><label>Your Email:</label>			
			<input id="registration_email" name="registration_email" type="text" value="<?php echo $settings['registration_email'] ?>" class="registration">
			</p>
			<p><label>Activation Key:</label>				
			<input id="registration_key" name="registration_key" type="text" value="<?php echo $settings['registration_key'] ?>" class="registration">
			</p>
			<p>&nbsp;</p>			
			<p><input type="submit" name="submit" id="activate" class="button-secondary" value="Activate Registration"></p>				
		</div>	
		<!-- End Registration -->			
			

	</div><!-- End Right Panel Container -->

</div>	<!-- END RIGHT PANEL -->






	<h3>Shortcode Designer <span class="instructions">(Double-click icon to edit link and title... Drag icon to change position... Drag to Trash to remove.)</span></h3>

	<div id="viewport">
		<?php
		$wid = 'width:'.$settings['sc_width'].'px; '; 
		$hgt = 'height:'.$settings['sc_height'].'px; ';
		
		if ($settings['sc_pad_top'] != '0' || $settings['sc_pad_right'] != '0' || $settings['sc_pad_bottom'] != '0' || $settings['sc_pad_left'] != '0') {		
			$pad = 'padding: '.$settings['sc_pad_top'].'px '.$settings['sc_pad_right'].'px '.$settings['sc_pad_bottom'].'px '.$settings['sc_pad_left'].'px; ';
		} else {
			$pad = '';			
		}	
		
		if ($settings['sc_margin_top'] != '0' || $settings['sc_margin_right'] != '0' || $settings['sc_margin_bottom'] != '0' || $settings['sc_margin_left'] != '0') {			
			$mar = 'margin: '.$settings['sc_margin_top'].'px '.$settings['sc_margin_right'].'px '.$settings['sc_margin_bottom'].'px '.$settings['sc_margin_left'].'px; ';
		} else {
			$mar = '';			
		}	
		
		if ($settings['sc_background'] == '0') {		
			$bdg = 'background: none; ';
		} else {
			$bdg = 'background-color:#'.$settings['sc_bg_color'].'; ';			
		}	
		
		if ( $settings['sc_border_size'] != '0') {
			$bor = 'border:#'.$settings['sc_border_color'].' '.$settings['sc_border_size'].'px solid;';
		} else {
			$bor = '';			
		}						
		
		echo '<div id="iire_social_shortcode" class="iire_social_shortcode" style="'.$wid.$hgt.$pad.$mar.$bdg.$bor.'">'; 
		echo stripslashes($settings['sc_icons']);		
		echo '</div>';		
		?>
		<div id="trash" title="Drop Icon to Remove"></div>
	</div> <!-- End Viewport -->

	<h3>Icons <span class="instructions">(Click an icon below to add it to the Shortcode Designer.)
	<?php 
		if ($settings['registration_key'] == '') {
			echo ' Additional networks require activation.';
		}	
	?>
	</span></h3>

	<div id="chooseicons">
		<ul id="chooseicons">
			<li class="choose <?php echo $th; ?>" id="iire-facebook" alt="https://www.facebook.com/example.profile.3" title="Facebook" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-twitter" alt="https://twitter.com/example" title="Follow Us On Twitter" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-googleplus" alt="https://plus.google.com/+chrome/posts" title="Google +" lang=""></li>				
			<li class="choose <?php echo $th; ?>" id="iire-pinterest" alt="https://pinterest.com/source/example.pl/" title="Pinterest" lang=""></li>						
			<li class="choose <?php echo $th; ?>" id="iire-youtube" alt="http://www.youtube.com/user/example" title="YouTube" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-rss" alt="<?php echo get_option('siteurl'); ?>/feed.rss" title="RSS Feed" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-favorite1" alt="" title="Add to Favorites" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-favorite2" alt="" title="Add to Favorites" lang=""></li>			
			<li class="choose <?php echo $th; ?>" id="iire-website" alt="<?php echo get_option('siteurl'); ?>" title="Alternate Website" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-map" alt="http://maps.google.com/maps" title="Map" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-profile" alt="<?php echo get_option('siteurl'); ?>/profile" title="Profile" lang=""></li>			
			<li class="choose <?php echo $th; ?>" id="iire-contact" alt="<?php echo get_option('siteurl'); ?>/contact-us" title="Contact Page" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-chat" alt="<?php echo get_option('siteurl'); ?>/chat" title="Chat" lang=""></li>			
			<li class="choose <?php echo $th; ?>" id="iire-icq" alt="http://" title="ICQ" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-music" alt="http://" title="Music" lang=""></li>											
			<li class="choose <?php echo $th; ?>" id="iire-customlink" alt="http://" title="Custom Link" lang=""></li>			
			<li class="choose <?php echo $th; ?>" id="iire-info1" alt="<?php echo get_option('siteurl'); ?>/more-information" title="More Information" lang=""></li>			
			<li class="choose <?php echo $th; ?>" id="iire-info2" alt="<?php echo get_option('siteurl'); ?>/more-information" title="More Information" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-camera" alt="<?php echo get_option('siteurl'); ?>/gallery" title="Photo Gallery" lang=""></li>			
			<li class="choose <?php echo $th; ?>" id="iire-cart" alt="<?php echo get_option('siteurl'); ?>/shop" title="Shop" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-print" alt="http://" title="Print" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-email" alt="you@yourwebsite.com" title="Email" lang="Use the email settings for this information!"></li>
			<li class="choose <?php echo $th; ?>" id="iire-email2" alt="mailto:<?php echo $settings['email_recipient']; ?>" title="Launch Email Program" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-blank" alt="http://" title="Blank" lang=""></li>					
			<li class="choose <?php echo $th; ?>" id="iire-blank2" alt="http://" title="Blank 2" lang=""></li>
			<li class="choose <?php echo $th; ?>" id="iire-blank3" alt="http://" title="Blank 3" lang=""></li>										
										
			<li class="choose trial <?php echo $th; ?>" id="iire-360cities" alt="http://360cities.com" title="360 Cities" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-43things" alt="http://43things.com" title="43 Things" lang=""></li>					
			<li class="choose trial <?php echo $th; ?>" id="iire-500px" alt="http://500px.com" title="500px" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-aboutme" alt="http://about.me" title="About Me" lang=""></li>							
			<li class="choose trial <?php echo $th; ?>" id="iire-activerain" alt="http://activerain.com" title="Active Rain" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-amazon" alt="http://amazon.com" title="Amazon"></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-bandcamp" alt="http://bandcamp.com" title="Band Camp" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-bandmix" alt="http://bandmix.com" title="Bandmix"></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-badoo" alt="http://badoo.com" title="Badoo" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-bebo" alt="http://bebo.com" title="Bebo" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-beatport" alt="http://beatport.com" title="Beatport" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-behance" alt="http://behance.net" title="Behance" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-blinklist" alt="http://blinklist.com" title="Blinklist" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-bloglovin" alt="http://bloglovin.com" title="Blog Lovin" lang=""></li>								
			<li class="choose trial <?php echo $th; ?>" id="iire-blogmarks" alt="http://blogmarks.com" title="Blog Marks" lang=""></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-blogger" alt="http://blogger.com" title="Blogger" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-brightkite" alt="http://limbo.com" title="Brightkite" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-buzznet" alt="http://buzznet.com" title="Buzznet" lang=""></li>				
			<li class="choose trial <?php echo $th; ?>" id="iire-cafemom" alt="http://cafemom.com" title="Cafe Mom" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-cdbaby" alt="http://cdbaby.com" title="CD Baby"></li>			

			<li class="choose trial <?php echo $th; ?>" id="iire-delicious" alt="http://delicious.com" title="Delicious" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-designbump" alt="http://designbump.com" title="Design Bump" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-designfloat" alt="http://designfloat.com" title="Design Float" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-designmoo" alt="http://designmoo.com" title="Design Moo" lang=""></li>														
			<li class="choose trial <?php echo $th; ?>" id="iire-deviantart" alt="http://deviantart.com" title="Deviant Art" lang=""></li>															
			<li class="choose trial <?php echo $th; ?>" id="iire-digg" alt="http://digg.com" title="Digg" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-diigo" alt="http://diigo.com" title="Diigo" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-disqus" alt="http://disqus.com" title="Disqus" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-dopplr" alt="http://dopplr.com" title="Dopplr" lang=""></li>										
			<li class="choose trial <?php echo $th; ?>" id="iire-dribbble" alt="http://dribbble.com" title="Dribbble" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-dzone" alt="http://dzone.com" title="Dzone" lang=""></li>								
			<li class="choose trial <?php echo $th; ?>" id="iire-ember" alt="http://ember.appappeal.com/" title="Ember" lang=""></li>							
			<li class="choose trial <?php echo $th; ?>" id="iire-etsy" alt="http://etsy.com" title="Etsy" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-evernote" alt="http://evernote.com" title="Evernote" lang=""></li>

			<li class="choose trial <?php echo $th; ?>" id="iire-feedburner" alt="http://feedburner.com" title="Feed Burner" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-fineart" alt="http://fineartamerica.com" title="Fine Art America" lang=""></li>								
			<li class="choose trial <?php echo $th; ?>" id="iire-flickr" alt="http://flickr.com" title="Flickr" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-formspring" alt="http://formspring.me" title="Formspring" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-forrst" alt="http://forrst.com" title="Forrst" lang=""></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-foursquare" alt="http://foursquare.com" title="Foursquare" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-friendfeed" alt="http://friendfeed.com" title="Friend Feed" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-friendster" alt="http://friendster.com" title="Friendster" lang=""></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-geocaching" alt="http://geocaching.com" title="Geocaching" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-github" alt="http://github.com" title="Github" lang=""></li>				
			<li class="choose trial <?php echo $th; ?>" id="iire-goodreads" alt="http://goodreads.com" title="Good Reads" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-grooveshark" alt="http://grooveshark.com" title="Groove Shark" lang=""></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-hellocotton" alt="http://hellocotton.com" title="Hello Cotton" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-hi5" alt="http://hi5.com" title="Hi 5" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-hyves" alt="http://hyves.nl" title="Hyves" lang=""></li>				
			<li class="choose trial <?php echo $th; ?>" id="iire-ilike" alt="http://ilike.com" title="ILIke" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-instagram" alt="http://instagram.com" title="Instagram" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-itunes" alt="http://itunes.com" title="iTunes"></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-lastfm" alt="http://lastfm.com" title="Last FM" lang=""></li>
			
			<li class="choose trial <?php echo $th; ?>" id="iire-linkedin" alt="http://linkedin.com" title="Linked In" lang=""></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-livejournal" alt="http://livejournal.com" title="Live Journal" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-medium" alt="http://medium.com" title="Medium"></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-meneame" alt="http://meneame.com" title="Meneame"></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-mixcloud" alt="http://mixcloud.com" title="Mixcloud"></li>									
			<li class="choose trial <?php echo $th; ?>" id="iire-mixx" alt="http://mixx.com" title="Mixx"></li>				
			<li class="choose trial <?php echo $th; ?>" id="iire-meetup" alt="http://meetup.com" title="Meet Up" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-mylife" alt="http://mylife.com" title="My Life" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-multiply" alt="http://multiply.com" title="Multiply" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-myspace" alt="http://myspace.com" title="My Space" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-newsgator" alt="http://newsgator.com" title="Newsgator" lang=""></li>							
			<li class="choose trial <?php echo $th; ?>" id="iire-newsvine" alt="http://newsvine.com" title="News Vine" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-ning" alt="http://ning.com" title="Ning" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-openid" alt="http://openid.net" title="Open ID" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-orkut" alt="http://orkut.com" title="Orkut" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-paypal" alt="http://paypal.com" title="Paypal" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-pheed" alt="http://pheed.com" title="Pheed" lang=""></li>							
			<li class="choose trial <?php echo $th; ?>" id="iire-picasa" alt="http://picasa.com" title="Picasa" lang=""></li>
			
			<li class="choose trial <?php echo $th; ?>" id="iire-plurk" alt="http://plurk.com" title="Plurk" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-posterous" alt="http://posterous.com" title="Posterous" lang=""></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-purevolume" alt="http://purevolume.com" title="Pure Volume" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-qik" alt="http://qik.com" title="Qik" lang=""></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-rdio" alt="http://rdio.com" title="Rdio" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-reddit" alt="http://reddit.com" title="Reddit" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-resident" alt="http://residentadvisor.net" title="Resident Advisor" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-reverbnation" alt="http://reverbnation.com" title="Reverb Nation" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-sharethis" alt="http://sharethis.com" title="Share This" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-skype" alt="http://skype.com" title="Skype" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-slashdot" alt="http://slashdot.org" title="Slash Dot" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-slideshare" alt="http://slideshare.net" title="Slide Share" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-smugmug" alt="http://smugmug.com" title="Smugmug"></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-snapjoy" alt="http://snapjoy.com" title="Snap Joy"></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-sonicbids" alt="http://sonicbids.com" title="Sonic Bids"></li>										
			<li class="choose trial <?php echo $th; ?>" id="iire-soundcloud" alt="http://soundcloud.com" title="Sound Cloud" lang=""></li>	
			<li class="choose trial <?php echo $th; ?>" id="iire-spurl" alt="http://spurl.net" title="Spurl" lang=""></li>				
										
			<li class="choose trial <?php echo $th; ?>" id="iire-spotify" alt="http://spotify.com" title="Spotify" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-squidoo" alt="http://squidoo.com" title="Squidoo"></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-stackoverflow" alt="http://stackoverflow.com" title="Stack Overflow"></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-steam" alt="http://store.steampowered.com/" title="Steam Powered"></li>									
			<li class="choose trial <?php echo $th; ?>" id="iire-stumbleupon" alt="http://stumbleupon.com" title="Stumble Upon" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-tagged" alt="http://www.tagged.com/" title="Tagged" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-technorati" alt="http://technorati.com" title="Technorati" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-thumb" alt="http://thumb.com" title="Thumb" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-tripadvisor" alt="http://tripadvisor.com" title="Trip Advisor"></li>						
			<li class="choose trial <?php echo $th; ?>" id="iire-tumblr" alt="http://tumblr.com" title="Tumblr" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-viddler" alt="http://viddler.com" title="Viddler"></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-vimeo" alt="http://vimeo.com" title="Vimeo" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-virb" alt="http://virb.com" title="Virb"></li>	
			<li class="choose trial <?php echo $th; ?>" id="iire-wikio" alt="http://wikio.com" title="Wikio"></li>							
			<li class="choose trial <?php echo $th; ?>" id="iire-wordpress" alt="http://wordpress.com" title="Wordpress" lang=""></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-xanga" alt="http://xanga.com" title="Xanga"></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-xing" alt="http://xing.com" title="Xing" lang=""></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-yammer" alt="http://yammer.com" title="Yammer"></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-yelp" alt="http://yelp.com" title="Yelp" lang=""></li>

			<li class="choose trial <?php echo $th; ?>" id="iire-ziki" alt="http://ziki.com" title="Ziki"></li>			
			<li class="choose trial <?php echo $th; ?>" id="iire-zorpia" alt="http://zorpia.com" title="Zorpia"></li>
			<li class="choose trial <?php echo $th; ?>" id="iire-zune" alt="http://zune.com" title="Zune"></li>						

			<li class="choose hidden <?php echo $th; ?>" id="iire-addthis" alt="http://www.addthis.com/bookmark.php?v=250&pubid=<?php echo $settings['addthis_key']; ?>" title="Add This Social Bookmarking" lang=""></li>		
		</ul>

		<input type="hidden" id="sc_addclasses" name="sc_addclasses" value="<?php echo $addclasses; ?>">
	</div>

<p class="submit" align="left"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">&nbsp;&nbsp;&nbsp;<a id="reset" class="reset button-secondary">Reset</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo get_option('siteurl'); ?>" target="_blank" class="preview button-secondary" title="Preview will launch in new tab/window!">Preview</a></p>

<h3>[raw] [iire_social_icons] [/raw] <span class="instructions">(Add this shortcode to any post, page or text widget to include these icons.)</span></h3>


<h3>Quick Start</h3>
<ol>
<li>Add this shortcode [raw] [iire_social_icons] [/raw] to any post, page or text widget.</li>
<li>Go to "iiRe Social Icons", "Shortcode Settings".</li>
<li>In the Icons section, click an icon to add it to the Shortcode Designer.</li>
<li>Repeat the previous step to add additional icons.</li>
<li>Double-click each icon in the Shortcode Designer to edit the link and title.</li>
<li>Click "Icon Theme" in the side panel, choose a theme i.e. "Mobile Phone" or use the "Default" theme.</li>
<li>Click "Save Changes".</li>
<li>Click "Preview" to view the output in the section where you placed the shortcode!</li>
<li>To quickly reset all the settings, click "Start Over". This will reload all the default values.</li>
</ol>

<p>&nbsp;</p>

<h3>Additional Free Themes</h3>
<p>You can find <a href="http://iireproductions.com/web/website-development/wordpress-plugins/plugins-social-icons/plugins-iire-social-icons-free-themes/" target="_blank">additional free themes</a> on our website!</p>

<p>To install additional themes via FTP:<p>
<ul>
<li>1. Download the desired icon theme to your hardrive and unzip. (The name of the zip file is the name of the theme folder)</li>
<li>2. Upload the icon theme folder to the /wp-content/plugins/iire-social-icons/themes/ directory.</li>
</ul>

<p>&nbsp;</p>


<h3>Notes</h3>
<p>To use the identical settings for the shortcode generated in the Widget Designer, go to "Widget Settings", "General Settings", set "Clone Widget Settings" to "Yes" and save your changes.</p>

<p>If you would like the icons to wrap in a Responsive Theme, go to "Shortcode Container, set "Responsive" to "Yes" and save your changes. This will override the Shortcode Container width.</p>

<p>Aligning the Shortcode Container to the right will order the icons in reverse. Drag and drop the icons to the desired order.</p>

<p>The Shortcode Designer works independently!!  You can create vastly different settings for the shortcode (which is best used is a page or post) or the widget (which is best used as a sidebar widget).</p>

<p>To include the shortcode in a template, copy this code into the desired location in your template.</p>
<code>
&lt;?php if(function_exists('iire_social_theme')) { iire_social_theme(); } ?&gt;
</code> 

<input type="text" id="theme_names" name="theme_names" value="<?php echo $settings['theme_names']; ?>" style="width:100%; visibility: hidden;">
<input type="text" id="theme_sizes" name="theme_sizes" value="<?php echo $settings['theme_sizes']; ?>" style="width:100%; visibility: hidden;">

<textarea id="sc_icons" name="sc_icons" cols="20" rows="3" class="h150" style="width:100%; visibility: hidden;"><?php echo stripslashes($settings['sc_icons']); ?></textarea>
<textarea id="sc_output" name="sc_output" cols="20" rows="3" class="h150" style="width:100%;  visibility: hidden;"><?php echo stripslashes($settings['sc_output']); ?></textarea>


<!-- EDIT ICON SETTINGS -->
<div id="editdialog" title="Edit Icon Settings" style="display:none;">
	<p class="instructions">Enter your site link and a title.</p>
	<p align="left">Link:&nbsp;&nbsp;<input type="text" id="choose_url" value="" class="choose_url" style="display:inline; width:250px"></p>
	<p align="left">Title: <input type="text" id="choose_title" value="" class="choose_title" style="display:inline; width:250px"></p>
	<p align="left"><span id="instructions"></span></p>
	<input type="hidden" value="" class="choose_id">
	<p align="right"><a id="edit_close" class="button-secondary">Close</a></p>			
</div>


<!-- UNLOCK DIALOG -->
<div id="unlockdialog" title="Unlock Features" style="display:none;">
	<p align="center">Please consider making a donation<br/>or upgrading to the full version<br/>to unlock this feature.</p>
	<p align="center">Visit <a href="http://iireproductions.com/web/website-development/wordpress-plugins/plugins-social-icons/#donate" target="_blank">iiRe Productions</a> for more information.</p>
	<p align="right"><a id="unlock_close" class="button-secondary">Close</a></p>			
</div>

<input id="registration_version" name="registration_version" type="hidden" value="<?php echo $settings['registration_version'] ?>" class="registration">
<input id="registration_activated" name="registration_activated" type="hidden" value="<?php echo $settings['registration_activated'] ?>" class="registration">
<input id="registration_expired" name="registration_expired" type="hidden" value="<?php echo $settings['registration_expired'] ?>" class="registration">

</form>

<div id="codepreview" style="visibility: hidden;"><?php echo stripslashes($settings['sc_output']); ?></div>

</div><!-- End Settings -->

</div> <!-- End Wrap -->

<?php
}
?>