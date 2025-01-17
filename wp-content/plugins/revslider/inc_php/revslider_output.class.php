<?php

	class RevSliderOutput{
		
		private static $sliderSerial = 0;
		
		private $sliderHtmlID;
		private $sliderHtmlID_wrapper;
		private $slider;
		private $oneSlideMode = false;
		private $oneSlideData;
		private $previewMode = false;	//admin preview mode
		private $slidesNumIndex;
		private $sliderLang = null;
		
		/**
		 * 
		 * check the put in string
		 * return true / false if the put in string match the current page.
		 */
		public static function isPutIn($putIn,$emptyIsFalse = false){
			
			$putIn = strtolower($putIn);
			$putIn = trim($putIn);
			
			if($emptyIsFalse && empty($putIn))
				return(false);
			
			if($putIn == "homepage"){		//filter by homepage
				if(is_front_page() == false)
					return(false);
			}				
			else		//case filter by pages	
			if(!empty($putIn)){
				$arrPutInPages = array();
				$arrPagesTemp = explode(",", $putIn);
				foreach($arrPagesTemp as $page){
					if(is_numeric($page) || $page == "homepage")
						$arrPutInPages[] = $page;
				}
				if(!empty($arrPutInPages)){
					
					//get current page id
					$currentPageID = "";
					if(is_front_page() == true)
						$currentPageID = "homepage";
					else{
						global $post;
						if(isset($post->ID))
							$currentPageID = $post->ID;
					}
						
					//do the filter by pages
					if(array_search($currentPageID, $arrPutInPages) === false) 
						return(false);
				}
			}
			
			return(true);
		}
		
		
		/**
		 * 
		 * put the rev slider slider on the html page.
		 * @param $data - mixed, can be ID ot Alias.
		 */
		public static function putSlider($sliderID,$putIn=""){
			
			$isPutIn = self::isPutIn($putIn);
			if($isPutIn == false)
				return(false);
			
			//check if on mobile and if option hide on mobile is set
			
			$output = new RevSliderOutput();
			$output->putSliderBase($sliderID);
			
			$slider = $output->getSlider();
			return($slider);
		}
		
		
		/**
		 * 
		 * set language
		 */
		public function setLang($lang){
			$this->sliderLang = $lang;
		}
		
		/**
		 * 
		 * set one slide mode for preview
		 */
		public function setOneSlideMode($data){
			$this->oneSlideMode = true;
			$this->oneSlideData = $data;
		}
		
		/**
		 * 
		 * set preview mode
		 */
		public function setPreviewMode(){
			$this->previewMode = true;
		}
		
		/**
		 * 
		 * get the last slider after the output
		 */
		public function getSlider(){
			return($this->slider);
		}
		
		/**
		 * 
		 * get slide full width video data
		 */
		private function getSlideFullWidthVideoData(RevSlide $slide){
			
			$response = array("found"=>false);
			
			//deal full width video:
			$enableVideo = $slide->getParam("enable_video","false");
			if($enableVideo != "true")
				return($response);
				
			$videoID = $slide->getParam("video_id","");
			$videoID = trim($videoID);
			
			if(empty($videoID))
				return($response);
				
			$response["found"] = true;
			
			$videoType = is_numeric($videoID)?"vimeo":"youtube";
			$videoAutoplay = $slide->getParam("video_autoplay");
			$videoCover = $slide->getParam("cover");
			$videoAutoplayOnlyFirstTime = $slide->getParam("autoplayonlyfirsttime");
			$previewimage = $slide->getParam("previewimage", "");
			$videoNextslide = $slide->getParam("video_nextslide");
			$mute = $slide->getParam("mute");
			
			$response["type"] = $videoType;
			$response["videoID"] = $videoID;
			$response["autoplay"] = UniteFunctionsRev::strToBool($videoAutoplay);
			$response["cover"] = UniteFunctionsRev::strToBool($videoCover);
			$response["autoplayonlyfirsttime"] = UniteFunctionsRev::strToBool($videoAutoplayOnlyFirstTime);
			$response["previewimage"] = UniteFunctionsRev::strToBool($previewimage);
			$response["nextslide"] = UniteFunctionsRev::strToBool($videoNextslide);
			$response["mute"] = UniteFunctionsRev::strToBool($mute);
			
			return($response);
		}
		
		
		/**
		 * 
		 * put full width video layer
		 */
		private function putFullWidthVideoLayer($videoData){
			
			if($videoData["found"] == false)
				return(false);
			
			$autoplayonlyfirsttime = "";
			$autoplay = UniteFunctionsRev::boolToStr($videoData["autoplay"]);
			if($autoplay == "true"){
				$autoplayonlyfirsttime = UniteFunctionsRev::boolToStr($videoData["autoplayonlyfirsttime"]);
				$autoplayonlyfirsttime = ' data-autoplayonlyfirsttime="'. $autoplayonlyfirsttime.'"';
			}
			$nextslide = UniteFunctionsRev::boolToStr($videoData["nextslide"]);
			
			$htmlParams = 'data-x="0" data-y="0" data-speed="500" data-start="10" data-easing="easeOutBack"';
			
			if($videoData["previewimage"] != '') $htmlParams.= '			data-thumbimage="'.$videoData["previewimage"].'"';
			
			$videoID = $videoData["videoID"];
			
			$setBase = (is_ssl()) ? "https://" : "http://";
			
			$mute = ($videoData['mute']) ? ' data-volume="mute"' : '';
			
			if($videoData["type"] == "youtube"):	//youtube
				?>	<div class="tp-caption fade fullscreenvideo" data-nextslideatend="<?php echo $nextslide?>" data-autoplay="<?php echo $autoplay?>"<?php echo $autoplayonlyfirsttime; ?> <?php echo $htmlParams?><?php echo $mute; ?>><iframe src="<?php echo $setBase; ?>www.youtube.com/embed/<?php echo $videoID?>?hd=1&amp;wmode=opaque&amp;controls=1&amp;showinfo=0;rel=0;" width="100%" height="100%"></iframe></div><?php 
			else:									//vimeo
				?>	<div class="tp-caption fade fullscreenvideo" data-nextslideatend="<?php echo $nextslide?>" data-autoplay="<?php echo $autoplay?>"<?php echo $autoplayonlyfirsttime; ?> <?php echo $htmlParams?><?php echo $mute; ?>><iframe src="<?php echo $setBase; ?>player.vimeo.com/video/<?php echo $videoID?>?title=0&amp;byline=0&amp;portrait=0;api=1" width="100%" height="100%"></iframe></div><?php
			endif;
		}
		
		/**
		 * 
		 * filter the slides for one slide preview
		 */
		private function filterOneSlide($slides){
			
			$oneSlideID = $this->oneSlideData["slideid"];
			
			
			$oneSlideParams = UniteFunctionsRev::getVal($this->oneSlideData, "params");		 	
			$oneSlideLayers = UniteFunctionsRev::getVal($this->oneSlideData, "layers");
			
			if(gettype($oneSlideParams) == "object")
				$oneSlideParams = (array)$oneSlideParams;

			if(gettype($oneSlideLayers) == "object")
				$oneSlideLayers = (array)$oneSlideLayers;
				
			if(!empty($oneSlideLayers))
				$oneSlideLayers = UniteFunctionsRev::convertStdClassToArray($oneSlideLayers);
			
			$newSlides = array();
			foreach($slides as $slide){	
				$slideID = $slide->getID();
				
				if($slideID == $oneSlideID){
										
					if(!empty($oneSlideParams))
						$slide->setParams($oneSlideParams);
					
					if(!empty($oneSlideLayers))
						$slide->setLayers($oneSlideLayers);
					
					$newSlides[] = $slide;	//add 2 slides
					$newSlides[] = $slide;
				}
			}
			
			return($newSlides);
		}
		
				
		/**
		 * 
		 * put the slider slides
		 */
		private function putSlides($doWrapFromTemplate){
			//go to template slider if post template
			if($doWrapFromTemplate !== false)	$this->slider->initByMixed($doWrapFromTemplate); //back to original Slider
			
			$sliderType = $this->slider->getParam("slider_type");
			
			$publishedOnly = true;
			if($this->previewMode == true && $this->oneSlideMode == true){	
				$previewSlideID = UniteFunctionsRev::getVal($this->oneSlideData, "slideid");
				$previewSlide = new RevSlide();
				$previewSlide->initByID($previewSlideID);
				$slides = array($previewSlide);
				
			}else{
				$slides = $this->slider->getSlidesForOutput($publishedOnly,$this->sliderLang);
			}
			
			
			$this->slidesNumIndex = $this->slider->getSlidesNumbersByIDs(true);
			
			if(empty($slides)):
				?>
				<div class="no-slides-text">
					No slides found, please add some slides
				</div>
				<?php 
			endif;
			
			//go back to normal slider if post template
			if($doWrapFromTemplate)	$this->slider->initByMixed($this->slider->getParam("slider_template_id",false)); //back to template for JS
			
			$thumbWidth = $this->slider->getParam("thumb_width",100);
			$thumbHeight = $this->slider->getParam("thumb_height",50);
			
			$slideWidth = $this->slider->getParam("width",900);
			$slideHeight = $this->slider->getParam("height",300);
			
			$navigationType = $this->slider->getParam("navigaion_type","none"); 
			$isThumbsActive = ($navigationType == "thumb")?true:false;
			
			$lazyLoad = $this->slider->getParam("lazy_load","off");
			
			//for one slide preview
			if($this->oneSlideMode == true)				
				$slides = $this->filterOneSlide($slides);
				
			echo "<ul>";
			
			$htmlFirstTransWrap = "";
			
			$startWithSlide = $this->slider->getStartWithSlideSetting();
				
			$firstTransActive = $this->slider->getParam("first_transition_active","false");
			if($firstTransActive == "true"){
				
				$firstTransition = $this->slider->getParam("first_transition_type","fade");						
				$htmlFirstTransWrap .= " data-fstransition=\"$firstTransition\"";
				
				$firstDuration = $this->slider->getParam("first_transition_duration","300");
				if(!empty($firstDuration) && is_numeric($firstDuration))
					$htmlFirstTransWrap .= " data-fsmasterspeed=\"$firstDuration\"";
					
				$firstSlotAmount = $this->slider->getParam("first_transition_slot_amount","7");
				if(!empty($firstSlotAmount) && is_numeric($firstSlotAmount))						
				$htmlFirstTransWrap .= " data-fsslotamount=\"$firstSlotAmount\"";
					
			}
			
			foreach($slides as $index => $slide){
				$params = $slide->getParams();
				
				//check if date is set
				$date_from = $slide->getParam("date_from","");
				$date_to = $slide->getParam("date_to","");
				
				if($date_from != ""){
					$date_from = strtotime($date_from);
					if(time() < $date_from) continue;
				}
				
				if($date_to != ""){
					$date_to = strtotime($date_to);
					if(time() > $date_to) continue;
				}
				
				$transition = $slide->getParam("slide_transition","random");
				//if($transition == "fade") $transition = "tp-fade";
				//$transitionPremium = $slide->getParam("slide_transition_premium","random");
				
				//if(trim($transition) == '')
				//	$transition = $transitionPremium;
				//else
				//	if(trim($transitionPremium) != '') $transition .= ','.$transitionPremium;
				
				
				$slotAmount = $slide->getParam("slot_amount","7");
				
				$isExternal = $slide->getParam("background_type","image");
				if($isExternal != "external"){
					$urlSlideImage = $slide->getImageUrl();
					
					//get image alt
					$imageFilename = $slide->getImageFilename();
					$info = pathinfo($imageFilename);
					$alt = $info["filename"];
				}else{
					
					$urlSlideImage = $slide->getParam("slide_bg_external","");
				
					$info = '';
					$alt = '';
				}
				
				//get thumb url
				$htmlThumb = "";
				if($isThumbsActive == true){
					$urlThumb = null;
					
					//check if post slider, if yes, get thumb from featured image
					//if($this->slider->isSlidesFromPosts())
					//	$urlThumb = '';
						
					if(empty($urlThumb)){
						$urlThumb = $slide->getParam("slide_thumb","");
					}
					

			
					if(empty($urlThumb)){	//try to get resized thumb
						$pathThumb = $slide->getImageFilepath();
						if(!empty($pathThumb))
							$urlThumb = UniteBaseClassRev::getImageUrl($pathThumb,$thumbWidth,$thumbHeight,true);
					}
					
					//if not - put regular image:
					if(empty($urlThumb))						
						$urlThumb = $slide->getImageUrl();
					
					$htmlThumb = 'data-thumb="'.$urlThumb.'" ';
				}
			
				//get link
				$htmlLink = "";
				$enableLink = $slide->getParam("enable_link","false");
				if($enableLink == "true"){
					$linkType = $slide->getParam("link_type","regular");
					switch($linkType){
						
						//---- normal link
						
						default:		
						case "regular":
							$link = $slide->getParam("link","");
							$linkOpenIn = $slide->getParam("link_open_in","same");
							$htmlTarget = "";
							if($linkOpenIn == "new")
								$htmlTarget = ' data-target="_blank"';
							$htmlLink = "data-link=\"$link\" $htmlTarget ";	
						break;		
						
						//---- link to slide
						
						case "slide":
							$slideLink = UniteFunctionsRev::getVal($params, "slide_link");
							if(!empty($slideLink) && $slideLink != "nothing"){
								//get slide index from id
								if(is_numeric($slideLink))
									$slideLink = UniteFunctionsRev::getVal($this->slidesNumIndex, $slideLink);
								
								if(!empty($slideLink))
									$htmlLink = "data-link=\"slide\" data-linktoslide=\"$slideLink\" ";
							}
						break;
					}
					
					//set link position:
					$linkPos = UniteFunctionsRev::getVal($params, "link_pos","front");
					if($linkPos == "back")
						$htmlLink .= ' data-slideindex="back"';	
				}
				
				//set delay
				$htmlDelay = "";
				$delay = $slide->getParam("delay","");
				if(!empty($delay) && is_numeric($delay))
					$htmlDelay = "data-delay=\"$delay\" ";
				
				//get duration
				$htmlDuration = "";
				$duration = $slide->getParam("transition_duration","");
				if(!empty($duration) && is_numeric($duration))
					$htmlDuration = "data-masterspeed=\"$duration\" ";
				
				//get rotation
				$htmlRotation = "";
				$rotation = $slide->getParam("transition_rotation","");
				if(!empty($rotation)){
					$rotation = (int)$rotation;
					if($rotation != 0){
						if($rotation > 720 && $rotation != 999)
							$rotation = 720;
						if($rotation < -720)
							$rotation = -720;
					}
					$htmlRotation = "data-rotate=\"$rotation\" ";
				}
				
				$fullWidthVideoData = $this->getSlideFullWidthVideoData($slide);
				
				//set full width centering.
				/*$htmlImageCentering = "";
				$fullWidthCentering = $slide->getParam("fullwidth_centering","false");
				if($sliderType == "fullwidth" && $fullWidthCentering == "true")
					$htmlImageCentering = ' data-fullwidthcentering="on"';
				*/
				
				//set first slide transition
				$htmlFirstTrans = "";
				if($index == $startWithSlide){
					$htmlFirstTrans = $htmlFirstTransWrap;
				}//first trans
				
				$htmlParams = $htmlDuration.$htmlLink.$htmlThumb.$htmlDelay.$htmlRotation.$htmlFirstTrans;
				
				$bgType = $slide->getParam("background_type","image");
				
				$styleImage = "";
				$urlImageTransparent = UniteBaseClassRev::$url_plugin."images/transparent.png";
				
				switch($bgType){
					case "trans":
						$urlSlideImage = $urlImageTransparent;
					break;
					case "solid":
						$urlSlideImage = $urlImageTransparent;
						$slideBGColor = $slide->getParam("slide_bg_color","#d0d0d0");
						$styleImage = "style='background-color:".$slideBGColor."'";
					break;
				}
				
				//additional params
				$imageAddParams = "";
				if($lazyLoad == "on"){
					$imageAddParams .= "data-lazyload=\"$urlSlideImage\"";
					$urlSlideImage = UniteBaseClassRev::$url_plugin."images/dummy.png";
				}
				
				//$imageAddParams .= $htmlImageCentering;
				
				//additional background params
				$bgFit = $slide->getParam("bg_fit","cover");
				$bgFitX = intval($slide->getParam("bg_fit_x","100"));
				$bgFitY = intval($slide->getParam("bg_fit_y","100"));
				
				$bgPosition = $slide->getParam("bg_position","center top");
				$bgPositionX = intval($slide->getParam("bg_position_x","0"));
				$bgPositionY = intval($slide->getParam("bg_position_y","0"));
				
				$bgRepeat = $slide->getParam("bg_repeat","no-repeat");
				
				if($bgPosition == 'percentage'){
					$imageAddParams .= ' data-bgposition="'.$bgPositionX.'% '.$bgPositionY.'%"';
				}else{
					$imageAddParams .= ' data-bgposition="'.$bgPosition.'"';
				}
				
				
				
				//check for kenburn & pan zoom
				$kenburn_effect = $slide->getParam("kenburn_effect","off");
				//$kb_rotation_start = intval($slide->getParam("kb_rotation_start","0"));
				//$kb_rotation_end = intval($slide->getParam("kb_rotation_end","0"));
				$kb_duration = intval($slide->getParam("kb_duration",$this->slider->getParam("delay",9000)));
				$kb_ease = $slide->getParam("kb_easing","Linear.easeNone");
				$kb_start_fit = $slide->getParam("kb_start_fit","100");
				$kb_end_fit =$slide->getParam("kb_end_fit","100");
	
				$kb_pz = '';
				
				if($kenburn_effect == "on" && ($bgType == 'image' || $bgType == 'external')){
					$kb_pz.= ' data-kenburns="on"';
					//$kb_pz.= ' data-rotationstart="'.$kb_rotation_start.'"';
					//$kb_pz.= ' data-rotationend="'.$kb_rotation_end.'"';
					$kb_pz.= ' data-duration="'.$kb_duration.'"';
					$kb_pz.= ' data-ease="'.$kb_ease.'"';
					$kb_pz.= ' data-bgfit="'.$kb_start_fit.'"';
					$kb_pz.= ' data-bgfitend="'.$kb_end_fit.'"';
					
					$bgEndPosition = $slide->getParam("bg_end_position","center top");
					$bgEndPositionX = intval($slide->getParam("bg_end_position_x","0"));
					$bgEndPositionY = intval($slide->getParam("bg_end_position_y","0"));
					
					if($bgEndPosition == 'percentage'){
						$kb_pz.= ' data-bgpositionend="'.$bgEndPositionX.'% '.$bgEndPositionY.'%"';
					}else{
						$kb_pz.= ' data-bgpositionend="'.$bgEndPosition.'"';
					}
					
					//set image original width and height
					//$imgSize = @getimagesize($urlSlideImage);
					//if(is_array($imgSize) && !empty($imgSize)){
					//	$kb_pz.= ' data-owidth="'.$imgSize[0].'"';
					//	$kb_pz.= ' data-oheight="'.$imgSize[1].'"';
					//}
					
				}else{ //only set if kenburner is off
					
					if($bgFit == 'percentage'){
						$imageAddParams .= ' data-bgfit="'.$bgFitX.'% '.$bgFitY.'%"';
					}else{
						$imageAddParams .= ' data-bgfit="'.$bgFit.'"';
					}
					
					$imageAddParams .= ' data-bgrepeat="'.$bgRepeat.'"';
					
				}
				
				
				//Html
				echo "	<!-- SLIDE  -->\n"; 
				echo "	<li data-transition=\"".$transition."\" data-slotamount=\"". $slotAmount."\" ". $htmlParams .">\n";
				echo "		<!-- MAIN IMAGE -->\n";
				echo "		<img src=\"". $urlSlideImage ."\" ". $styleImage ." alt=\"". $alt . "\" ".$imageAddParams. $kb_pz .">\n";
				echo "		<!-- LAYERS -->\n";
				//put video:
				if($fullWidthVideoData["found"] == true)	//backward compatability
					$this->putFullWidthVideoLayer($fullWidthVideoData);
					
				$this->putCreativeLayer($slide);
				
				echo "	</li>\n";
				
			}	//get foreach
			
			echo "</ul>\n";
		}
		
		
		/**
		 * 
		 * get html5 layer html from data
		 */
		private function getHtml5LayerHtml($data){
			$urlPoster = UniteFunctionsRev::getVal($data, "urlPoster");
			$urlMp4 = UniteFunctionsRev::getVal($data, "urlMp4");
			$urlWebm = UniteFunctionsRev::getVal($data, "urlWebm");
			$urlOgv = UniteFunctionsRev::getVal($data, "urlOgv");
			$width = UniteFunctionsRev::getVal($data, "width");
			$height = UniteFunctionsRev::getVal($data, "height");
			
			$ids = UniteFunctionsRev::getVal($data, "attrID");
			$ids = UniteFunctionsRev::getVal($data, "attrID");
			$classes = UniteFunctionsRev::getVal($data, "attrClasses");
			$title = UniteFunctionsRev::getVal($data, "attrTitle");
			$rel = UniteFunctionsRev::getVal($data, "attrRel");
			$ids = ($ids != '') ? ' id="'.$ids.'"' : '';
			$classes = ($classes != '') ? ' '.$classes : '';
			$title = ($title != '') ? ' title="'.$title.'"' : '';
			$rel = ($rel != '') ? ' rel="'.$rel.'"' : '';
			
			$fullwidth = UniteFunctionsRev::getVal($data, "fullwidth");
			$fullwidth = UniteFunctionsRev::strToBool($fullwidth);
			
			$videoloop = UniteFunctionsRev::getVal($data, "videoloop");
			$videoloop = UniteFunctionsRev::strToBool($videoloop);
			
			$controls = UniteFunctionsRev::getVal($data, "controls");
			$controls = UniteFunctionsRev::strToBool($controls);
			
			if($fullwidth == true){
				$width = "100%";
				$height = "100%";
			}
			
			$videoloop = ($videoloop == true) ? ' loop' : '';
			$controls = ($controls == true) ? '' : ' controls';
			
			$htmlPoster = "";
			if(!empty($urlPoster))
				$htmlPoster = "poster='".$urlPoster."'";
				
			$htmlMp4 = "";
			if(!empty($urlMp4))
				$htmlMp4 = "<source src='".$urlMp4."' type='video/mp4' />";

			$htmlWebm = "";
			if(!empty($urlWebm))
				$htmlWebm = "<source src='".$urlWebm."' type='video/webm' />";
				
			$htmlOgv = "";
			if(!empty($urlOgv))
				$htmlOgv = "<source src='".$urlOgv."' type='video/ogg' />";
			
			$html =	"<video class=\"video-js vjs-default-skin".$classes."\"".$ids.$title.$rel.$videoloop.$controls." preload=\"none\" width=\"".$width."\" height=\"".$height."\" \n";
	   		$html .=  $htmlPoster ." data-setup=\"{}\"> \n";
	        $html .=  $htmlMp4."\n";
	        $html .=  $htmlWebm."\n";
	        $html .=  $htmlOgv."\n";
			$html .=  "</video>\n";
			
			return($html);
		}
		
		
		/**
		 * 
		 * put creative layer
		 */
		private function putCreativeLayer(RevSlide $slide){
			$layers = $slide->getLayers();
			$customAnimations = RevOperations::getCustomAnimations('customin'); //get all custom animations
			$customEndAnimations = RevOperations::getCustomAnimations('customout'); //get all custom animations
			$startAnimations = RevOperations::getArrAnimations(false); //only get the standard animations
			$endAnimations = RevOperations::getArrEndAnimations(false); //only get the standard animations			
						
			if(empty($layers))
				return(false);
				
				$zIndex = 2;
				
				foreach($layers as $layer):
						
					$type = UniteFunctionsRev::getVal($layer, "type","text");
					
					//set if video full screen
					$isFullWidthVideo = false;
					if($type == "video"){
						$videoData = UniteFunctionsRev::getVal($layer, "video_data");
						if(!empty($videoData)){
							$videoData = (array)$videoData;
							$isFullWidthVideo = UniteFunctionsRev::getVal($videoData, "fullwidth");
							$isFullWidthVideo = UniteFunctionsRev::strToBool($isFullWidthVideo);
						}else
							$videoData = array();
					}
					
					
					$class = UniteFunctionsRev::getVal($layer, "style");
					$animation = UniteFunctionsRev::getVal($layer, "animation","tp-fade");
					if($animation == "fade") $animation = "tp-fade";
					
					$customin = '';
					if(!array_key_exists($animation, $startAnimations) && array_key_exists($animation, $customAnimations)){ //if true, add custom animation
						$customin.= 'data-customin="';
						$animArr = RevOperations::getCustomAnimationByHandle($customAnimations[$animation]);
						if($animArr !== false) $customin.= RevOperations::parseCustomAnimationByArray($animArr);						
						$customin.= '"';
						$animation = 'customin';
					}
					
					if(strpos($animation, 'customin-') !== false || strpos($animation, 'customout-') !== false) $animation = "tp-fade";
					
					//set output class:
					$outputClass = "tp-caption ". trim($class);
						$outputClass = trim($outputClass) . " ";
						
					$outputClass .= trim($animation);
					
					$left = UniteFunctionsRev::getVal($layer, "left",0);
					$top = UniteFunctionsRev::getVal($layer, "top",0);
					$speed = UniteFunctionsRev::getVal($layer, "speed",300);
					$time = UniteFunctionsRev::getVal($layer, "time",0);
					$easing = UniteFunctionsRev::getVal($layer, "easing","easeOutExpo");
					$randomRotate = UniteFunctionsRev::getVal($layer, "random_rotation","false");
					$randomRotate = UniteFunctionsRev::boolToStr($randomRotate);
					
					$text = UniteFunctionsRev::getVal($layer, "text");
					
					$htmlVideoAutoplay = "";
					$htmlVideoAutoplayOnlyFirstTime = "";
					$htmlVideoNextSlide = "";
					$htmlVideoThumbnail = "";
					$htmlMute = '';
					$htmlCover = '';
					$htmlDotted = '';
					$htmlRatio = '';
					$htmlRewind = '';
					
					$ids = UniteFunctionsRev::getVal($layer, "attrID");
					$classes = UniteFunctionsRev::getVal($layer, "attrClasses");
					$title = UniteFunctionsRev::getVal($layer, "attrTitle");
					$rel = UniteFunctionsRev::getVal($layer, "attrRel");
					
					$ids = ($ids != '') ? ' id="'.$ids.'"' : '';
					$classes = ($classes != '') ? ' '.$classes : '';
					$title = ($title != '') ? ' title="'.$title.'"' : '';
					$rel = ($rel != '') ? ' rel="'.$rel.'"' : '';
					
					//set html:
					$html = "";
					switch($type){
						default:
						case "text":						
							$html = $text;
							$html = do_shortcode($html);
						break;
						case "image":
							$alt = UniteFunctionsRev::getVal($layer, "alt");
							$urlImage = UniteFunctionsRev::getVal($layer, "image_url");
							
							$additional = "";
							$scaleX = UniteFunctionsRev::getVal($layer, "scaleX");
							$scaleY = UniteFunctionsRev::getVal($layer, "scaleY");
							if($scaleX != '') $additional .= ' data-ww="'.$scaleX.'"';
							if($scaleY != '') $additional .= ' data-hh="'.$scaleY.'"';
							if(is_ssl()){
								$urlImage = str_replace("http://", "https://", $urlImage);
							}
							
							$html = '<img src="'.$urlImage.'" alt="'.$alt.'"'.$additional.'>';
							$imageLink = UniteFunctionsRev::getVal($layer, "link","");
							if(!empty($imageLink)){
								$openIn = UniteFunctionsRev::getVal($layer, "link_open_in","same");

								$target = "";
								if($openIn == "new")
									$target = ' target="_blank"';
									
								$html = '<a href="'.$imageLink.'"'.$target.'>'.$html.'</a>';
							}								
						break;
						case "video":
							$videoType = trim(UniteFunctionsRev::getVal($layer, "video_type"));
							$videoID = trim(UniteFunctionsRev::getVal($layer, "video_id"));
							$videoWidth = trim(UniteFunctionsRev::getVal($layer, "video_width"));
							$videoHeight = trim(UniteFunctionsRev::getVal($layer, "video_height"));	
							$videoArgs = trim(UniteFunctionsRev::getVal($layer, "video_args"));
							
							$rewind = UniteFunctionsRev::getVal($videoData, "forcerewind");
							$rewind = UniteFunctionsRev::strToBool($rewind);
							$htmlRewind = ($rewind == true) ? ' data-forcerewind="on"' : '';
							
							
							if($isFullWidthVideo == true){
								$videoWidth = "100%";
								$videoHeight = "100%";
							}
							
							$setBase = (is_ssl()) ? "https://" : "http://";
							
							switch($videoType){
								case "youtube":
									if(empty($videoArgs))
										$videoArgs = GlobalsRevSlider::DEFAULT_YOUTUBE_ARGUMENTS;
									$html = "<iframe src='".$setBase."www.youtube.com/embed/".$videoID."?".$videoArgs."' width='".$videoWidth."' height='".$videoHeight."' style='width:".$videoWidth."px;height:".$videoHeight."px;'></iframe>";
									
								break;
								case "vimeo":
									if(empty($videoArgs))
										$videoArgs = GlobalsRevSlider::DEFAULT_VIMEO_ARGUMENTS;
									$html = "<iframe src='".$setBase."player.vimeo.com/video/".$videoID."?".$videoArgs."' width='".$videoWidth."' height='".$videoHeight."' style='width:".$videoWidth."px;height:".$videoHeight."px;'></iframe>";
								break;
								case "html5":
									$html = $this->getHtml5LayerHtml($videoData);
									$cover = UniteFunctionsRev::getVal($videoData, "cover");
									$cover = UniteFunctionsRev::strToBool($cover);
									if($cover == true){
										$htmlCover = ' data-forceCover="1"';
										$dotted = UniteFunctionsRev::getVal($videoData, "dotted");
										if($dotted !== 'none')
											$htmlDotted = ' data-dottedoverlay="'.$dotted.'"';	
										
										$ratio = UniteFunctionsRev::getVal($videoData, "ratio");
										if(!empty($ratio))
											$htmlRatio = ' data-aspectratio="'.$ratio.'"';
									}
								break;
								default:
									UniteFunctionsRev::throwError("wrong video type: $videoType");
								break;
							}
							
							//set video autoplay, with backward compatability
							if(array_key_exists("autoplay", $videoData))
								$videoAutoplay = UniteFunctionsRev::getVal($videoData, "autoplay");
							else	//backword compatability
								$videoAutoplay = UniteFunctionsRev::getVal($layer, "video_autoplay");
							
							//set video autoplayonlyfirsttime, with backward compatability
							if(array_key_exists("autoplayonlyfirsttime", $videoData))
								$videoAutoplayOnlyFirstTime = UniteFunctionsRev::getVal($videoData, "autoplayonlyfirsttime");
							else
								$videoAutoplayOnlyFirstTime = "";
							
							$videoAutoplay = UniteFunctionsRev::strToBool($videoAutoplay);
							$videoAutoplayOnlyFirstTime = UniteFunctionsRev::strToBool($videoAutoplayOnlyFirstTime);
							$mute = UniteFunctionsRev::getVal($videoData, "mute");
							$mute = UniteFunctionsRev::strToBool($mute);
							$htmlMute = ($mute)	? ' data-volume="mute"' : '';
							
							if($videoAutoplay == true)
								$htmlVideoAutoplay = '			data-autoplay="true"'."\n";								
							else
								$htmlVideoAutoplay = '			data-autoplay="false"'."\n";
							
							if($videoAutoplayOnlyFirstTime == true && $videoAutoplay == true)
								$htmlVideoAutoplayOnlyFirstTime = '			data-autoplayonlyfirsttime="true"'."\n";								
							else
								$htmlVideoAutoplayOnlyFirstTime = '			data-autoplayonlyfirsttime="false"'."\n";
								
							$videoNextSlide = UniteFunctionsRev::getVal($videoData, "nextslide");
							$videoNextSlide = UniteFunctionsRev::strToBool($videoNextSlide);
							
							if($videoNextSlide == true)
								$htmlVideoNextSlide = '			data-nextslideatend="true"'."\n";								
								
							$videoThumbnail = @$videoData["previewimage"];
							
							if(trim($videoThumbnail) !== '') $htmlVideoThumbnail = '			data-thumbimage="'.$videoThumbnail.'"'."\n";
							
						break;
					}
					
					//handle end transitions:
					$endTime = trim(UniteFunctionsRev::getVal($layer, "endtime"));
					$htmlEnd = "";
					$customout = '';
					if(!empty($endTime)){
						$htmlEnd = "data-end=\"$endTime\""."\n";
					}
					$endSpeed = trim(UniteFunctionsRev::getVal($layer, "endspeed"));
					if(!empty($endSpeed))
						 $htmlEnd .= "data-endspeed=\"$endSpeed\""."\n";
						 
					$endEasing = trim(UniteFunctionsRev::getVal($layer, "endeasing"));
					if(!empty($endSpeed) && $endEasing != "nothing")
						 $htmlEnd .= "			data-endeasing=\"$endEasing\""."\n";
					
					//add animation to class
					$endAnimation = trim(UniteFunctionsRev::getVal($layer, "endanimation"));
					if($endAnimation == "fade") $endAnimation = "tp-fade";
					
					if(!array_key_exists($endAnimation, $endAnimations) && array_key_exists($endAnimation, $customEndAnimations)){ //if true, add custom animation
						$customout = 'data-customout="';
						$animArr = RevOperations::getCustomAnimationByHandle($customEndAnimations[$endAnimation]);
						if($animArr !== false) $customout.= RevOperations::parseCustomAnimationByArray($animArr);						
						$customout.= '"';
						$endAnimation = 'customout';
					}
					
					if(strpos($endAnimation, 'customin-') !== false || strpos($endAnimation, 'customout-') !== false) $endAnimation = "";
					
					if(!empty($endAnimation) && $endAnimation != "auto")
						$outputClass .= " ".$endAnimation;	
					
					//slide link
					$htmlLink = "";
					$slideLink = UniteFunctionsRev::getVal($layer, "link_slide");
					if(!empty($slideLink) && $slideLink != "nothing" && $slideLink != "scroll_under"){
						//get slide index from id
						if(is_numeric($slideLink))
							$slideLink = UniteFunctionsRev::getVal($this->slidesNumIndex, $slideLink);
						
						if(!empty($slideLink))
							$htmlLink = "data-linktoslide=\"$slideLink\""."\n";
					}
					
					//scroll under the slider
					if($slideLink == "scroll_under"){
						$outputClass .= " tp-scrollbelowslider";
						$scrollUnderOffset = UniteFunctionsRev::getVal($layer, "scrollunder_offset");
						if(!empty($scrollUnderOffset))
							$htmlLink .= "data-scrolloffset=\"".$scrollUnderOffset."\""."\n";
					}					
					
					//hidden under resolution
					$htmlHidden = "";
					$layerHidden = UniteFunctionsRev::getVal($layer, "hiddenunder");
					if($layerHidden == "true" || $layerHidden == "1")
						$htmlHidden = '			data-captionhidden="on"'."\n";
					
					$htmlParams = $htmlEnd.$htmlLink.$htmlVideoAutoplay.$htmlVideoAutoplayOnlyFirstTime.$htmlVideoNextSlide.$htmlVideoThumbnail.$htmlHidden.$htmlMute.$htmlCover.$htmlDotted.$htmlRatio.$htmlRewind;
					
					//set positioning options
					
					$alignHor = UniteFunctionsRev::getVal($layer,"align_hor","left");
					$alignVert = UniteFunctionsRev::getVal($layer, "align_vert","top");
					
					$htmlPosX = "";
					$htmlPosY = "";
					switch($alignHor){
						default:
						case "left":
							$htmlPosX = "data-x=\"".$left."\"";
						break;
						case "center":
							$htmlPosX = "data-x=\"center\" data-hoffset=\"".$left."\"";
						break;
						case "right":
							$left = (int)$left*-1;
							$htmlPosX = "data-x=\"right\" data-hoffset=\"".$left."\"";
						break;
					}
					
					switch($alignVert){
						default:
						case "top":
							$htmlPosY = "data-y=\"".$top."\" ";
						break;
						case "middle":
							$htmlPosY = "data-y=\"center\" data-voffset=\"".$top."\"";
						break;
						case "bottom":
							$top = (int)$top*-1;
							$htmlPosY = "data-y=\"bottom\" data-voffset=\"".$top."\"";
						break;						
					}
					
					//set corners
					$htmlCorners = "";
					
					if($type == "text"){
						$cornerLeft = UniteFunctionsRev::getVal($layer, "corner_left");
						$cornerRight = UniteFunctionsRev::getVal($layer, "corner_right");
						switch($cornerLeft){
							case "curved":
								$htmlCorners .= "<div class='frontcorner'></div>";
							break;
							case "reverced":
								$htmlCorners .= "<div class='frontcornertop'></div>";							
							break;
						}
						
						switch($cornerRight){
							case "curved":
								$htmlCorners .= "<div class='backcorner'></div>";
							break;
							case "reverced":
								$htmlCorners .= "<div class='backcornertop'></div>";							
							break;
						}
					
					//add resizeme class
					$resizeme = UniteFunctionsRev::getVal($layer, "resizeme");
					if($resizeme == "true" || $resizeme == "1")
						$outputClass .= ' tp-resizeme';
						
					}//end text related layer
					
					//make some modifications for the full screen video
					if($isFullWidthVideo == true){
						$htmlPosX = "data-x=\"0\"";
						$htmlPosY = "data-y=\"0\"";
						$outputClass .= " fullscreenvideo";
					}
					
				echo "\n		<!-- LAYER NR. ";
				echo $zIndex - 1;
				echo " -->\n";
				echo "		<div class=\"".$outputClass;
				echo ($classes != '') ? ' '.$classes : '';
				echo "\"\n";
				echo ($ids != '') ? '			'.$ids."\n" : '';
				echo ($title != '') ? '			'.$title."\n" : '';
				echo ($rel != '') ? '			'.$rel."\n" : '';
				if($htmlPosX != "") echo "			".$htmlPosX."\n";
				if($htmlPosY != "") echo "			".$htmlPosY."\n";
				if($customin != "") echo "			".$customin."\n";
				if($customout != "") echo "			".$customout."\n";
				echo "			data-speed=\"".$speed."\"\n"; 
				echo "			data-start=\"".$time."\"\n";
				echo "			data-easing=\"".$easing."\"\n";
				if($htmlParams != "") echo "			".$htmlParams;
				echo "			style=\"z-index: ".$zIndex. "\"";
				echo ">";
				echo $html."\n";
				if($htmlCorners != ""){
					echo $htmlCorners."\n";
				}
				echo "		</div>\n";
				$zIndex++;
			endforeach;
		}
		
		/**
		 * 
		 * put slider javascript
		 */
		private function putJS(){
			
			$params = $this->slider->getParams();
			$sliderType = $this->slider->getParam("slider_type");
			$optFullWidth = ($sliderType == "fullwidth")?"on":"off";
			
			$optFullScreen = "off";
			if($sliderType == "fullscreen"){
				$optFullWidth = "off";
				$optFullScreen = "on";
			}
			
			$noConflict = $this->slider->getParam("jquery_noconflict","on");
			
			//set thumb amount
			$numSlides = $this->slider->getNumSlides(true);
			$thumbAmount = (int)$this->slider->getParam("thumb_amount","5");
			if($thumbAmount > $numSlides)
				$thumbAmount = $numSlides;
				
			
			//get stop slider options
			 $stopSlider = $this->slider->getParam("stop_slider","off");
			 $stopAfterLoops = $this->slider->getParam("stop_after_loops","0");
			 $stopAtSlide = $this->slider->getParam("stop_at_slide","2");
			 
			 if($stopSlider == "off"){
				 $stopAfterLoops = "-1";
				 $stopAtSlide = "-1";
			 }
			
			// set hide navigation after
			$hideThumbs = $this->slider->getParam("hide_thumbs","200");
			if(is_numeric($hideThumbs) == false)
				$hideThumbs = "0";
			else{
				$hideThumbs = (int)$hideThumbs;
				if($hideThumbs < 10)
					$hideThumbs = 10;
			}
			
			$alwaysOn = $this->slider->getParam("navigaion_always_on","false");
			if($alwaysOn == "true")
				$hideThumbs = "0";
			
			$sliderID = $this->slider->getID();
			
			//treat hide slider at limit
			$hideSliderAtLimit = $this->slider->getParam("hide_slider_under","0",RevSlider::VALIDATE_NUMERIC);
			if(!empty($hideSliderAtLimit))
				$hideSliderAtLimit++;

			//this option is disabled in full width slider
			if($sliderType == "fullwidth")
				$hideSliderAtLimit = "0";
			
			$hideCaptionAtLimit = $this->slider->getParam("hide_defined_layers_under","0",RevSlider::VALIDATE_NUMERIC);;
			if(!empty($hideCaptionAtLimit))
				$hideCaptionAtLimit++;
			
			$hideAllCaptionAtLimit = $this->slider->getParam("hide_all_layers_under","0",RevSlider::VALIDATE_NUMERIC);;
			if(!empty($hideAllCaptionAtLimit))
				$hideAllCaptionAtLimit++;
			
			//start_with_slide
			$startWithSlide = $this->slider->getStartWithSlideSetting();
			
	 	  //modify navigation type (backward compatability)
			$arrowsType = $this->slider->getParam("navigation_arrows","nexttobullets");
			switch($arrowsType){
				case "verticalcentered":
					$arrowsType = "solo";
				break;
			}
			
			//More Mobile Options
			$hideThumbsOnMobile = $this->slider->getParam("hide_thumbs_on_mobile","off");
			
			$hideBulletsOnMobile = $this->slider->getParam("hide_bullets_on_mobile","off");
			
			$hideArrowsOnMobile = $this->slider->getParam("hide_arrows_on_mobile","off");
			
			$hideThumbsUnderResolution = $this->slider->getParam("hide_thumbs_under_resolution","0",RevSlider::VALIDATE_NUMERIC);
			
			$videoJsPath = UniteBaseClassRev::$url_plugin."rs-plugin/videojs/";			
			
			?>
			
			<script type="text/javascript">

				var tpj=jQuery;				
				<?php if($noConflict == "on"):?>tpj.noConflict();<?php endif;?>
				
				var revapi<?php echo $sliderID?>;
				
				tpj(document).ready(function() {
								
				if(tpj('#<?php echo $this->sliderHtmlID?>').revolution == undefined)
					revslider_showDoubleJqueryError('#<?php echo $this->sliderHtmlID?>');
				else
				   revapi<?php echo $sliderID?> = tpj('#<?php echo $this->sliderHtmlID?>').show().revolution(
					{
						dottedOverlay:"<?php echo $this->slider->getParam("background_dotted_overlay","none");?>",
						delay:<?php echo $this->slider->getParam("delay","9000",RevSlider::FORCE_NUMERIC)?>,
						startwidth:<?php echo $this->slider->getParam("width","900")?>,
						startheight:<?php echo $this->slider->getParam("height","300")?>,
						hideThumbs:<?php echo $hideThumbs?>,
						
						thumbWidth:<?php echo $this->slider->getParam("thumb_width","100",RevSlider::FORCE_NUMERIC)?>,
						thumbHeight:<?php echo $this->slider->getParam("thumb_height","50",RevSlider::FORCE_NUMERIC)?>,
						thumbAmount:<?php echo $thumbAmount?>,
						
						navigationType:"<?php echo $this->slider->getParam("navigaion_type","none")?>",
						navigationArrows:"<?php echo $arrowsType?>",
						navigationStyle:"<?php echo $this->slider->getParam("navigation_style","round")?>",
						
						touchenabled:"<?php echo $this->slider->getParam("touchenabled","on")?>",
						onHoverStop:"<?php echo $this->slider->getParam("stop_on_hover","on")?>",
						
						navigationHAlign:"<?php echo $this->slider->getParam("navigaion_align_hor","center")?>",
						navigationVAlign:"<?php echo $this->slider->getParam("navigaion_align_vert","bottom")?>",
						navigationHOffset:<?php echo $this->slider->getParam("navigaion_offset_hor","0",RevSlider::FORCE_NUMERIC)?>,
						navigationVOffset:<?php echo $this->slider->getParam("navigaion_offset_vert","20",RevSlider::FORCE_NUMERIC)?>,

						soloArrowLeftHalign:"<?php echo $this->slider->getParam("leftarrow_align_hor","left")?>",
						soloArrowLeftValign:"<?php echo $this->slider->getParam("leftarrow_align_vert","center")?>",
						soloArrowLeftHOffset:<?php echo $this->slider->getParam("leftarrow_offset_hor","20",RevSlider::FORCE_NUMERIC)?>,
						soloArrowLeftVOffset:<?php echo $this->slider->getParam("leftarrow_offset_vert","0",RevSlider::FORCE_NUMERIC)?>,

						soloArrowRightHalign:"<?php echo $this->slider->getParam("rightarrow_align_hor","right")?>",
						soloArrowRightValign:"<?php echo $this->slider->getParam("rightarrow_align_vert","center")?>",
						soloArrowRightHOffset:<?php echo $this->slider->getParam("rightarrow_offset_hor","20",RevSlider::FORCE_NUMERIC)?>,
						soloArrowRightVOffset:<?php echo $this->slider->getParam("rightarrow_offset_vert","0",RevSlider::FORCE_NUMERIC)?>,
								
						shadow:<?php echo $this->slider->getParam("shadow_type","2")?>,
						fullWidth:"<?php echo $optFullWidth?>",
						fullScreen:"<?php echo $optFullScreen?>",

						stopLoop:"<?php echo $stopSlider?>",
						stopAfterLoops:<?php echo $stopAfterLoops?>,
						stopAtSlide:<?php echo $stopAtSlide?>,

						
						shuffle:"<?php echo $this->slider->getParam("shuffle","off") ?>",
						
						<?php if($this->slider->getParam("slider_type") == "fullwidth"){ ?>autoHeight:"<?php echo $this->slider->getParam("auto_height", 'off'); ?>",<?php }  ?>
						
						<?php if($this->slider->getParam("slider_type") == "fullwidth" || $this->slider->getParam("slider_type") == "fullscreen"){ ?>forceFullWidth:"<?php echo $this->slider->getParam("force_full_width", 'off'); ?>",<?php }  ?>
						
						<?php if($this->slider->getParam("slider_type") == "fullscreen"){ ?>fullScreenAlignForce:"<?php echo $this->slider->getParam("full_screen_align_force","off") ?>",<?php }  ?>
						
						<?php if($this->slider->getParam("slider_type") == "fullscreen"){ ?>minFullScreenHeight:"<?php echo $this->slider->getParam("fullscreen_min_height","0") ?>",<?php }  ?>
						
						hideThumbsOnMobile:"<?php echo $hideThumbsOnMobile?>",
						hideBulletsOnMobile:"<?php echo $hideBulletsOnMobile?>",
						hideArrowsOnMobile:"<?php echo $hideArrowsOnMobile?>",
						hideThumbsUnderResolution:<?php echo $hideThumbsUnderResolution?>,
						
						hideSliderAtLimit:<?php echo $hideSliderAtLimit?>,
						hideCaptionAtLimit:<?php echo $hideCaptionAtLimit?>,
						hideAllCaptionAtLilmit:<?php echo $hideAllCaptionAtLimit?>,
						startWithSlide:<?php echo $startWithSlide?>,
						videoJsPath:"<?php echo $videoJsPath?>",
						fullScreenOffsetContainer: "<?php echo $this->slider->getParam("fullscreen_offset_container","");?>"	
					});
				
				});	//ready
				
			</script>
			
			<?php			
		}
		
		
		/**
		 * 
		 * put inline error message in a box.
		 */
		public function putErrorMessage($message){
			?>
			<div style="width:800px;height:300px;margin-bottom:10px;border:1px solid black;margin:0px auto;">
				<div style="padding-left:20px;padding-right:20px;line-height:1.5;padding-top:40px;color:red;font-size:16px;text-align:left;">
					<?php _e("Revolution Slider Error",REVSLIDER_TEXTDOMAIN)?>: <?php echo $message?> 
				</div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery(".rev_slider").show();	
				});
			</script>
			<?php 
		}
		
		/**
		 * 
		 * fill the responsitive slider values for further output
		 */
		private function getResponsitiveValues(){
			$sliderWidth = (int)$this->slider->getParam("width");
			$sliderHeight = (int)$this->slider->getParam("height");
			
			$percent = $sliderHeight / $sliderWidth;
			
			$w1 = (int) $this->slider->getParam("responsitive_w1",0);
			$w2 = (int) $this->slider->getParam("responsitive_w2",0);
			$w3 = (int) $this->slider->getParam("responsitive_w3",0);
			$w4 = (int) $this->slider->getParam("responsitive_w4",0);
			$w5 = (int) $this->slider->getParam("responsitive_w5",0);
			$w6 = (int) $this->slider->getParam("responsitive_w6",0);
			
			$sw1 = (int) $this->slider->getParam("responsitive_sw1",0);
			$sw2 = (int) $this->slider->getParam("responsitive_sw2",0);
			$sw3 = (int) $this->slider->getParam("responsitive_sw3",0);
			$sw4 = (int) $this->slider->getParam("responsitive_sw4",0);
			$sw5 = (int) $this->slider->getParam("responsitive_sw5",0);
			$sw6 = (int) $this->slider->getParam("responsitive_sw6",0);
			
			$arrItems = array();
			
			//add main item:
			$arr = array();				
			$arr["maxWidth"] = -1;
			$arr["minWidth"] = $w1;
			$arr["sliderWidth"] = $sliderWidth;
			$arr["sliderHeight"] = $sliderHeight;
			$arrItems[] = $arr;
			
			//add item 1:
			if(empty($w1))
				return($arrItems);
				
			$arr = array();				
			$arr["maxWidth"] = $w1-1;
			$arr["minWidth"] = $w2;
			$arr["sliderWidth"] = $sw1;
			$arr["sliderHeight"] = floor($sw1 * $percent);
			$arrItems[] = $arr;
			
			//add item 2:
			if(empty($w2))
				return($arrItems);
			
			$arr["maxWidth"] = $w2-1;
			$arr["minWidth"] = $w3;
			$arr["sliderWidth"] = $sw2;
			$arr["sliderHeight"] = floor($sw2 * $percent);
			$arrItems[] = $arr;
			
			//add item 3:
			if(empty($w3))
				return($arrItems);
			
			$arr["maxWidth"] = $w3-1;
			$arr["minWidth"] = $w4;
			$arr["sliderWidth"] = $sw3;
			$arr["sliderHeight"] = floor($sw3 * $percent);
			$arrItems[] = $arr;
			
			//add item 4:
			if(empty($w4))
				return($arrItems);
			
			$arr["maxWidth"] = $w4-1;
			$arr["minWidth"] = $w5;
			$arr["sliderWidth"] = $sw4;
			$arr["sliderHeight"] = floor($sw4 * $percent);
			$arrItems[] = $arr;

			//add item 5:
			if(empty($w5))
				return($arrItems);
			
			$arr["maxWidth"] = $w5-1;
			$arr["minWidth"] = $w6;
			$arr["sliderWidth"] = $sw5;
			$arr["sliderHeight"] = floor($sw5 * $percent);
			$arrItems[] = $arr;
			
			//add item 6:
			if(empty($w6))
				return($arrItems);
			
			$arr["maxWidth"] = $w6-1;
			$arr["minWidth"] = 0;
			$arr["sliderWidth"] = $sw6;
			$arr["sliderHeight"] = floor($sw6 * $percent);
			$arrItems[] = $arr;
			
			return($arrItems);
		}
		
		
		/**
		 * 
		 * put responsitive inline styles
		 */
		private function putResponsitiveStyles(){

			$bannerWidth = $this->slider->getParam("width");
			$bannerHeight = $this->slider->getParam("height");
			
			$arrItems = $this->getResponsitiveValues();
			
			?>
			<style type='text/css'>
				#<?php echo $this->sliderHtmlID?>, #<?php echo $this->sliderHtmlID_wrapper?> { width:<?php echo $bannerWidth?>px; height:<?php echo $bannerHeight?>px;}
			<?php
			foreach($arrItems as $item):			
				$strMaxWidth = "";
				
				if($item["maxWidth"] >= 0)
					$strMaxWidth = "and (max-width: ".$item["maxWidth"]."px)";
				
			?>
			
			   @media only screen and (min-width: <?php echo $item["minWidth"]?>px) <?php echo $strMaxWidth?> {
			 		  #<?php echo $this->sliderHtmlID?>, #<?php echo $this->sliderHtmlID_wrapper?> { width:<?php echo $item["sliderWidth"]?>px; height:<?php echo $item["sliderHeight"]?>px;}	
			   }
			
			<?php 
			endforeach;
			echo "</style>";
		}

		
		/**
		 * 
		 * modify slider settings for preview mode
		 */
		private function modifyPreviewModeSettings(){
			$params = $this->slider->getParams();
			$params["js_to_body"] = "false";
			
			$this->slider->setParams($params);
		}
		
		
		/**
		 * 
		 * put html slider on the html page.
		 * @param $data - mixed, can be ID ot Alias.
		 */
		
		//TODO: settings google font, position, margin, background color, alt image text
		
		public function putSliderBase($sliderID){
			
			try{
				self::$sliderSerial++;
				
				$this->slider = new RevSlider();
				$this->slider->initByMixed($sliderID);
				
				$doWrapFromTemplate = false;
				
				if($this->slider->isSlidesFromPosts() && $this->slider->getParam("slider_template_id",false) !== false){ //need to use general settings from the Template Slider
					$this->slider->initByMixed($this->slider->getParam("slider_template_id",false));
					$doWrapFromTemplate = $sliderID;
				}
				
				//modify settings for admin preview mode
				if($this->previewMode == true)
					$this->modifyPreviewModeSettings();
				
				//set slider language
				$isWpmlExists = UniteWpmlRev::isWpmlExists();
				$useWpml = $this->slider->getParam("use_wpml","off");
				if(	$isWpmlExists && $useWpml == "on"){					 
					if($this->previewMode == false)
						$this->sliderLang = UniteFunctionsWPRev::getCurrentLangCode();
				}
				
				//edit html before slider
				$htmlBeforeSlider = "";
				if($this->slider->getParam("load_googlefont","false") == "true"){
					$googleFont = $this->slider->getParam("google_font");
					if(is_array($googleFont)){
						foreach($googleFont as $key => $font){
							$htmlBeforeSlider .= RevOperations::getCleanFontImport($font);
						}
					}else{
						$htmlBeforeSlider .= RevOperations::getCleanFontImport($googleFont);
					}
					
				}
				
				//pub js to body handle
				if($this->slider->getParam("js_to_body","false") == "true"){
					$urlIncludeJS = UniteBaseClassRev::$url_plugin."rs-plugin/js/jquery.themepunch.plugins.min.js?rev=". GlobalsRevSlider::SLIDER_REVISION;
					$htmlBeforeSlider .= "<script type='text/javascript' src='$urlIncludeJS'></script>";
					$urlIncludeJS = UniteBaseClassRev::$url_plugin."rs-plugin/js/jquery.themepunch.revolution.min.js?rev=". GlobalsRevSlider::SLIDER_REVISION;
					$htmlBeforeSlider .= "<script type='text/javascript' src='$urlIncludeJS'></script>";
				}
				
				//the initial id can be alias
				$sliderID = $this->slider->getID();
				
				$bannerWidth = $this->slider->getParam("width",null,RevSlider::VALIDATE_NUMERIC,"Slider Width");
				$bannerHeight = $this->slider->getParam("height",null,RevSlider::VALIDATE_NUMERIC,"Slider Height");
				
				$sliderType = $this->slider->getParam("slider_type");
				
				//set wrapper height
				$wrapperHeigh = 0;
				$wrapperHeigh += $this->slider->getParam("height");
				
				//add thumb height
				if($this->slider->getParam("navigaion_type") == "thumb"){
					$wrapperHeigh += $this->slider->getParam("thumb_height");
				}

				$this->sliderHtmlID = "rev_slider_".$sliderID."_".self::$sliderSerial;
				$this->sliderHtmlID_wrapper = $this->sliderHtmlID."_wrapper";
				
				$containerStyle = "";
				
				$sliderPosition = $this->slider->getParam("position","center");
				
				//set position:
				if($sliderType != "fullscreen"){
					
					switch($sliderPosition){
						case "center":
						default:
							$containerStyle .= "margin:0px auto;";
						break;
						case "left":
							$containerStyle .= "float:left;";
						break;
						case "right":
							$containerStyle .= "float:right;";
						break;
					}
					
				}
					
				//add background color
				$backgrondColor = trim($this->slider->getParam("background_color"));
				if(!empty($backgrondColor))
					$containerStyle .= "background-color:$backgrondColor;";
				
				//set padding			
				$containerStyle .= "padding:".$this->slider->getParam("padding","0")."px;";
				
				//set margin:
				if($sliderType != "fullscreen"){
									
					if($sliderPosition != "center"){
						$containerStyle .= "margin-left:".$this->slider->getParam("margin_left","0")."px;";
						$containerStyle .= "margin-right:".$this->slider->getParam("margin_right","0")."px;";
					}
					
					$containerStyle .= "margin-top:".$this->slider->getParam("margin_top","0")."px;";
					$containerStyle .= "margin-bottom:".$this->slider->getParam("margin_bottom","0")."px;";
				}
				
				//set height and width:
				$bannerStyle = "display:none;";	
				
				//add background image (to banner style)
				$showBackgroundImage = $this->slider->getParam("show_background_image","false");
				if($showBackgroundImage == "true"){					
					$backgroundImage = $this->slider->getParam("background_image");
					$backgroundFit = $this->slider->getParam("bg_fit", "cover");
					$backgroundRepeat = $this->slider->getParam("bg_repeat", "no-repeat");
					$backgroundPosition = $this->slider->getParam("bg_position", "center top");
					
					if(!empty($backgroundImage))
						$bannerStyle .= "background-image:url($backgroundImage);background-repeat:".$backgroundRepeat.";background-fit:".$backgroundFit.";background-position:".$backgroundPosition.";";
				}
				
				//set wrapper and slider class:
				$sliderWrapperClass = "rev_slider_wrapper";
				$sliderClass = "rev_slider";
				
				$putResponsiveStyles = false;
				
				switch($sliderType){
					default:
					case "fixed":
						$bannerStyle .= "height:".$bannerHeight."px;width:".$bannerWidth."px;";
						$containerStyle .= "height:".$bannerHeight."px;width:".$bannerWidth."px;";
					break;
					case "responsitive":
						//$containerStyle .= "height:".$bannerHeight."px;";
						$putResponsiveStyles = true;						
					break;
					case "fullwidth":
						$sliderWrapperClass .= " fullwidthbanner-container";
						$sliderClass .= " fullwidthabanner";
						$bannerStyle .= "max-height:".$bannerHeight."px;height:".$bannerHeight.";";
						$containerStyle .= "max-height:".$bannerHeight."px;";						
					break;
					case "fullscreen":
						//$containerStyle .= "height:".$bannerHeight."px;";
						$sliderWrapperClass .= " fullscreen-container";
						$sliderClass .= " fullscreenbanner";
					break;
				}
				
				$htmlTimerBar = "";
				
				$timerBar =  $this->slider->getParam("show_timerbar","top");
				
				if($timerBar == "true")
					$timerBar = $this->slider->getParam("timebar_position","top");
										
				switch($timerBar){
					case "top":
						$htmlTimerBar = '<div class="tp-bannertimer"></div>';
					break;
					case "bottom":
						$htmlTimerBar = '<div class="tp-bannertimer tp-bottom"></div>';
					break;
				}
				
				//check inner / outer border
				$paddingType = $this->slider->getParam("padding_type","outter");
				if($paddingType == "inner")	
					$sliderWrapperClass .= " tp_inner_padding"; 
				
				global $revSliderVersion;
				
				echo "<!-- START REVOLUTION SLIDER ". $revSliderVersion ." ". $sliderType ." mode -->\n";

					if($putResponsiveStyles == true)
						$this->putResponsitiveStyles(); 
				
				echo $htmlBeforeSlider."\n";
				echo "<div id=\"";
				echo $this->sliderHtmlID_wrapper;
				echo "\" ";
				echo "class=\"". $sliderWrapperClass ."\"";
				echo " style=\"". $containerStyle ."\">\n";
					
				echo "	<div id=\"";
				echo $this->sliderHtmlID;
				echo "\" ";
				echo "class=\"". $sliderClass ."\"";
				echo " style=\"". $bannerStyle ."\">\n";
				
				echo $this->putSlides($doWrapFromTemplate);
				echo $htmlTimerBar;
				echo "	</div>\n";
				echo "</div>";
				
				$this->putJS();
				echo "<!-- END REVOLUTION SLIDER -->";
				
			}catch(Exception $e){
				$message = $e->getMessage();
				$this->putErrorMessage($message);
			}
			
		}
		
		
	}

?>