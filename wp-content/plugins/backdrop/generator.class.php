<?php class backdropGenerator{
	public $css;
	public $js;
	public $zindex;
	function __contruct($settings){
		$this->css = '';
		$this->js = '';
	}
	function _set_zindex($z){
		// I know what your thinking, why isn't this done by the constructor, well I tried and it wasen't working, it
		// was also late and I dident want to be up the rest of the night figuring it out, perhans for a future update.
		$this->zindex = $z;
	}
	function color($a){
		$this->css.='html body,'."\n";
		$this->css.='html body.custom-background,'."\n";
		$this->css.='html body.customize-support{'."\n";
		$this->css.='	background:'.$a['color'].'!important;'."\n";
		$this->css.='}'."\n";
	}
	function image($a){
		// Varibles names are
		switch($a['repeat']){
			case 'none':
				$repeat = 'no-repeat';
			break;
			case 'x':
				$repeat = 'repeat-x';
			break;
			case 'y':
				$repeat = 'repeat-y';
			break;
			default:
				$repeat = 'repeat';
			break;
		}
		if($a['attachment']=='parallax'){
			// We need a tad bit of scripting to make parallax... prallax...
			$this->js  = 'window.onscroll=backdropParallax;'."\n";
			$this->js .= 'function backdropParallax(){
				var sY = (document.documentElement.scrollTop)?document.documentElement.scrollTop:window.pageYOffset;
				var pB = document.getElementsByTagName("body")[0];
				document.getElementsByTagName("body")[0].style.backgroundPosition="'.($a['hpos']=='stretch'?'center':$a['hpos']).' "+Math.round((sY*'.($a['parallax_adjustment']/100).')*-1)+"px"
			}'."\n";
			$this->js .= 'backdropParallax();'."\n";
			// And just in case it didn't work we have a backup
			$this->js .= 'window.onload=backdropParallax;'."\n";
		}
		if($a['attachment']=='slide'){
			// At it's core it's just a modified version of parallax that can work in both directions, of couse the increases the complextiy a bit.
			$this->js  = 'window.onscroll=backdropSlide;'."\n";
			$this->js .= 'function backdropSlide(){
				var vS = 0;
				var hS = 0;
				var sY = (document.documentElement.scrollTop)?document.documentElement.scrollTop:window.pageYOffset;
				var pB = document.getElementsByTagName("body")[0];'."\n";
				if(strripos($a['slide'],'left')!==false){
					$this->js.='		hS = ((sY*'.($a['slide_speed']/100).')*-1);'."\n";
				}
				if(strripos($a['slide'],'right')!==false){
					$this->js.='		hS = ((sY*'.($a['slide_speed']/100).'));'."\n";
				}
				if(strripos($a['slide'],'up')!==false){
					$this->js.='		vS = ((sY*'.($a['slide_speed']/100).')*-1);'."\n";
				}
				if(strripos($a['slide'],'down')!==false){
					$this->js.='		vS = ((sY*'.($a['slide_speed']/100).'));'."\n";
				}
			$this->js .= '
				pB.style.backgroundPosition=Math.round(hS)+"px "+Math.round(vS)+"px";
			}'."\n";
			$this->js .= 'backdropSlide();'."\n";
			// And just in case it didn't work we have a backup
			$this->js .= 'window.onload=backdropSlide;'."\n";

		}
//		if($a['attachment']=='parallax' || $a['attachment']=='slide'){
//			// Now that all the scripting is handled lets change to to fixed so the css is simpler
//			$a['attachment'] = 'fixed';
//		}
		switch($a['attachment']){
			case 'fullscreen':
				$this->css.='#backdrop_funky_foot{'."\n";
				$this->css.='	position:fixed;'."\n";
				// These positions are at -1px because chome had a bug that would improperly position the image, leaving a 1px space on the
				// side showing the background color, this will probably be corrected in the future, but I'll probably forget to update it.
				$this->css.='	top:-1px;'."\n";
				$this->css.='	bottom:-1px;'."\n";
				$this->css.='	left:-1px;'."\n";
				$this->css.='	right:-1px;'."\n";
				$this->css.='	z-index:'.$this->zindex.';'."\n";
				$this->css.='	background: '.$a['color'].' url("'.$a['img'].'") no-repeat fixed center center;'."\n";
				$this->css.='	-webkit-background-size:cover;'."\n";
				$this->css.='	-moz-background-size:cover;'."\n";
				$this->css.='	-o-background-size:cover;'."\n";
				$this->css.='	background-size:cover;'."\n";
				// No clue if this will actually work since I don't use IE (or even have it).
				// The above method however works in IE9+ (as well as pretty much everything else).
				$this->css.='	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.$a['img'].'",sizingMethod="scale");'."\n";
				$this->css.='	-ms-filter:"progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.$a['img'].'\',sizingMethod=\'scale\')";'."\n";
				$this->css.='}';
			break;
			default: // Everything else is default now
				$this->css.='html body,'."\n";
				$this->css.='html body.custom-background,'."\n";
				$this->css.='html body.customize-support{'."\n";
				$this->css.='	background-color: '.$a['color'].';';
				$this->css.='	background-image: url("'.$a['img'].'");';
				$this->css.='	background-repeat:'.$repeat.';';
				
				if($a['attachment']=='parallax' || $a['attachment']=='slide'){
					$this->css.='	background-attachment:fixed;';
				}else{
					$this->css.='	background-attachment:'.$a['attachment'].';';
				}
				// Slide does not have a horizontal attachment, because it has to get changed on the fly
				if($a['attachment']=='slide'){
					$a['hpos'] = 0;
					$a['vpos'] = 0;
				}
				if($a['hpos']=='stretch' && ($a['attachment']=='parallax' || $a['attachment']=='fixed' || $a['attachment']=='scroll') ){
					$this->css.='	background-size: 100% auto;'."\n";
					$this->css.='	background-position: center '.$a['vpos'].';'."\n";
					// And because this overrides retina...
					$a['retinize'] = 'no';
				}else{
					$this->css.='	background-position: '.$a['hpos'].' '.$a['vpos'].';'."\n";
				}
				if($a['retinize']=='yes'){
					$is=getimagesize($a['img']);
					$this->css.='	background-size: '.($is[0]/2).'px '.($is[1]/2).'px;'."\n";
				}
			break;
		}
	}
	function css($a){
		if($a['application']=='body'){
			$this->css.='html body,';
			$this->css.='html body.custom-background,';
			$this->css.='html body.customize-support{';
		}else{
			$this->css.='#backdrop_funky_foot{';
			$this->css.='	position:fixed;'."\n";
			$this->css.='	top:0px;'."\n";
			$this->css.='	bottom:0px;'."\n";
			$this->css.='	left:0px;'."\n";
			$this->css.='	right:0px;'."\n";
			$this->css.='	z-index:'.$this->zindex.';'."\n";
		}
		$this->css.=$a['css'];
		$this->css.='}';
	}
	function write(){
		if(!is_dir('../wp-content/backdrop')){
			mkdir('../wp-content/backdrop');
			file_put_contents('../wp-content/backdrop/about_this_directory.txt','This directory is created by the Backdrop plugin to store your generated CSS and JS files in a location safe from plugin upgrades.'."\n\n".'If you are no longer using Backdrop you can safely remove this directory.');
		}
		file_put_contents('../wp-content/backdrop/generated.css',trim($this->css));
		file_put_contents('../wp-content/backdrop/generated.js',trim($this->js));
	}
}