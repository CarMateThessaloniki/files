<?php
 
	$operations = new RevOperations();

	//set Layer settings	
	$contentCSS = $operations->getCaptionsContent();
	$arrAnimations = $operations->getArrAnimations();
	$arrEndAnimations = $operations->getArrEndAnimations();
	
	$htmlButtonDown = '<div id="layer_captions_down" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthick-1-s"></span></div>';
	$buttonEditStyles = UniteFunctionsRev::getHtmlLink("javascript:void(0)", "<i class=\"revicon-magic\"></i>Edit Style","button_edit_css","button-primary revblue");
	$buttonEditStylesGlobal = UniteFunctionsRev::getHtmlLink("javascript:void(0)", "<i class=\"revicon-palette\"></i>Edit Global Style","button_edit_css_global","button-primary revblue");
	
	$arrEasing = $operations->getArrEasing();
	$arrEndEasing = $operations->getArrEndEasing();
	
	$captionsAddonHtml = $htmlButtonDown.$buttonEditStyles.$buttonEditStylesGlobal;
	
	//set Layer settings
	$layerSettings = new UniteSettingsAdvancedRev();
	$layerSettings->addSection(__("Layer Params",REVSLIDER_TEXTDOMAIN),__("layer_params",REVSLIDER_TEXTDOMAIN));
	$layerSettings->addSap(__("Layer Params",REVSLIDER_TEXTDOMAIN),__("layer_params", REVSLIDER_TEXTDOMAIN));
	$layerSettings->addTextBox(__("layer_caption", REVSLIDER_TEXTDOMAIN), __("caption_green",REVSLIDER_TEXTDOMAIN), __("Style",REVSLIDER_TEXTDOMAIN),array(UniteSettingsRev::PARAM_ADDTEXT=>$captionsAddonHtml,"class"=>"textbox-caption"));
	
	$addHtmlTextarea = '';
	if($sliderTemplate == "true"){
		$addHtmlTextarea .= UniteFunctionsRev::getHtmlLink("javascript:void(0)", "insert post template","linkInsertTemplate","disabled");
	}
	$addHtmlTextarea .= UniteFunctionsRev::getHtmlLink("javascript:void(0)", "insert button","linkInsertButton","disabled");
	
	$layerSettings->addTextArea("layer_text", "",__("Text / Html",REVSLIDER_TEXTDOMAIN),array("class"=>"area-layer-params",UniteSettingsRev::PARAM_ADDTEXT_BEFORE_ELEMENT=>$addHtmlTextarea));
	$layerSettings->addTextBox("layer_image_link", "",__("Image Link",REVSLIDER_TEXTDOMAIN),array("class"=>"text-sidebar-link","hidden"=>true));
	$layerSettings->addSelect("layer_link_open_in",array("same"=>__("Same Window",REVSLIDER_TEXTDOMAIN),"new"=>__("New Window",REVSLIDER_TEXTDOMAIN)),__("Link Open In",REVSLIDER_TEXTDOMAIN),"same",array("hidden"=>true));
		
	$layerSettings->addSelect("layer_animation",$arrAnimations,__("Start Animation",REVSLIDER_TEXTDOMAIN),"fade");	
	$layerSettings->addSelect("layer_easing", $arrEasing, __("Start Easing",REVSLIDER_TEXTDOMAIN),"Power3.easeInOut");
	$params = array("unit"=>__("ms",REVSLIDER_TEXTDOMAIN));
	$layerSettings->addTextBox("layer_speed", "","Start Speed",$params);
	$layerSettings->addCheckbox("layer_hidden", false,__("Hide Under Width",REVSLIDER_TEXTDOMAIN));
	
	//scale for img
	$textScaleX = __("Width",REVSLIDER_TEXTDOMAIN);
	$textScaleProportionalX = __("Width/Height",REVSLIDER_TEXTDOMAIN);
	$params = array("attrib_text"=>"data-textproportional='".$textScaleProportionalX."' data-textnormal='".$textScaleX."'", "hidden"=>false);
	$layerSettings->addTextBox("layer_scaleX", "",__("Width",REVSLIDER_TEXTDOMAIN),$params);
	$layerSettings->addTextBox("layer_scaleY", "",__("Height",REVSLIDER_TEXTDOMAIN),array("hidden"=>false));
	$layerSettings->addCheckbox("layer_proportional_scale", false,__("Scale Proportional",REVSLIDER_TEXTDOMAIN),array("hidden"=>false));
	
	//put left top
	$textOffsetX = __("OffsetX",REVSLIDER_TEXTDOMAIN);
	$textX = __("X",REVSLIDER_TEXTDOMAIN);
	$params = array("attrib_text"=>"data-textoffset='".$textOffsetX."' data-textnormal='".$textX."'");	
	$layerSettings->addTextBox("layer_left", "",__("X",REVSLIDER_TEXTDOMAIN),$params);
	
	$textOffsetY = __("OffsetY",REVSLIDER_TEXTDOMAIN);
	$textY = __("Y",REVSLIDER_TEXTDOMAIN);	
	$params = array("attrib_text"=>"data-textoffset='".$textOffsetY."' data-textnormal='".$textY."'");
	$layerSettings->addTextBox("layer_top", "",__("Y",REVSLIDER_TEXTDOMAIN),$params);
	
	$layerSettings->addTextBox("layer_align_hor", "left","Hor Align",array("hidden"=>true));
	$layerSettings->addTextBox("layer_align_vert", "top","Vert Align",array("hidden"=>true));
	
	$layerSettings->addSelect("layer_slide_link", $arrSlideLinkLayers, __("Link To Slide",REVSLIDER_TEXTDOMAIN),"nothing");
	
	$params = array("unit"=>__("px",REVSLIDER_TEXTDOMAIN),"hidden"=>true);
	$layerSettings->addTextBox("layer_scrolloffset", "0",__("Scroll Under Slider Offset",REVSLIDER_TEXTDOMAIN),$params);

	$layerSettings->addButton("button_change_image_source", __("Change Image Source",REVSLIDER_TEXTDOMAIN),array("hidden"=>true,"class"=>"button-primary revblue"));	
	$layerSettings->addTextBox("layer_alt", "","Alt Text",array("hidden"=>true, "class"=>"area-alt-params"));	
	$layerSettings->addButton("button_edit_video", __("Edit Video",REVSLIDER_TEXTDOMAIN),array("hidden"=>true,"class"=>"button-primary revblue"));


	
	$params = array("unit"=>__("ms",REVSLIDER_TEXTDOMAIN));
	$layerSettings->addTextBox("layer_endtime", "",__("End Time",REVSLIDER_TEXTDOMAIN),$params);
	$layerSettings->addTextBox("layer_endspeed", "",__("End Speed",REVSLIDER_TEXTDOMAIN),$params);
	$layerSettings->addSelect("layer_endanimation",$arrEndAnimations,__("End Animation",REVSLIDER_TEXTDOMAIN),"auto");
	$layerSettings->addSelect("layer_endeasing", $arrEndEasing, __("End Easing",REVSLIDER_TEXTDOMAIN),"nothing");
	$params = array("unit"=>__("ms",REVSLIDER_TEXTDOMAIN));
	
	//advanced params
	$arrCorners = array("nothing"=>__("No Corner",REVSLIDER_TEXTDOMAIN),
						"curved"=>__("Sharp",REVSLIDER_TEXTDOMAIN),
						"reverced"=>__("Sharp Reversed",REVSLIDER_TEXTDOMAIN));	
	$params = array();
	$layerSettings->addSelect("layer_cornerleft", $arrCorners, __("Left Corner",REVSLIDER_TEXTDOMAIN),"nothing",$params);
	$layerSettings->addSelect("layer_cornerright", $arrCorners, __("Right Corner",REVSLIDER_TEXTDOMAIN),"nothing",$params);
	$layerSettings->addCheckbox("layer_resizeme", true,__("Responsive Through All Levels",REVSLIDER_TEXTDOMAIN),$params);
	
	$params = array();
	$layerSettings->addTextBox("layer_id", "",__("ID",REVSLIDER_TEXTDOMAIN),$params);
	$layerSettings->addTextBox("layer_classes", "",__("Classes",REVSLIDER_TEXTDOMAIN),$params);
	$layerSettings->addTextBox("layer_title", "",__("Title",REVSLIDER_TEXTDOMAIN),$params);
	$layerSettings->addTextBox("layer_rel", "",__("Rel",REVSLIDER_TEXTDOMAIN),$params);
	
	
	self::storeSettings("layer_settings",$layerSettings);
	
	//store settings of content css for editing on the client.
	self::storeSettings("css_captions_content",$contentCSS);
	
?>