<?php
/*******************************************************************************
* Module:   Axerios wgui                                                       *
* Version:  0.01                                                               *
* Date:     2011-05-19                                                         *
* Author:   Daniel MUELLER                                                     *
*******************************************************************************/

if(!class_exists('wgui_window'))
{
define('wgui_window_VERSION','0.01');

class wgui_window extends wgui
{
	protected $windowID;
	protected $modalMode;
	protected $windowIcon;
	protected $windowTitle;
	protected $windowContent;
	protected $buttonMinimize;
	protected $buttonMaximize;
	protected $buttonClose;
	protected $resizable;
	protected $centerOnScreen;
	protected $width;
	protected $height;
	protected $fullscreen;
	protected $dockable;
	protected $onCreate;
	protected $onClose;
	protected $onRestore;
	protected $onIconize;
	protected $onLoad;
	protected $onResize;

	function wgui_window($cPluginName,$windowID,$cLanguage="de")
	{
		parent::wgui($cPluginName,"",$cLanguage); //$cTemplate ist bewust auf "" gesetzt.
		$this->windowID = $windowID;
		$this->modalMode = false;
		$this->windowIcon = "";
		$this->windowTitle = "unknown title";
		$this->windowContent = "";
		$this->buttonMinimize = true;
		$this->buttonMaximize = true;
		$this->buttonClose = true;
		$this->resizable = true;
		$this->centerOnScreen = false;
		$this->width = 300;
		$this->height = 150;
		$this->fullscreen = false;
		$this->dockable = false;
		$this->onCreate = "";
		$this->onClose = "";
		$this->onRestore = "";
		$this->onIconize = "";
		$this->onLoad = "";
		$this->onResize = "";
	}
//mb_switchAlwaisOnTop()   <-- SCHREIBFEHLER???

	function minimize($windowID="")
	{
		// In JS einbauen -> Abfrage nach State ( jQuery.fn.mb_getState(attr) ) -> wenn NICHT ICONIZED, dann mb_iconize() ausführen, sonst nicht!
		communication_interface::jsExecute('$("#'.($windowID=="" ? $this->windowID : $windowID).'").mb_iconize();');
	}

	function restore($windowID="")
	{
		// In JS einbauen -> Abfrage nach State ( jQuery.fn.mb_getState(attr) ) -> wenn ICONIZED, dann mb_iconize() ausführen, sonst nicht!
		communication_interface::jsExecute('$("#'.($windowID=="" ? $this->windowID : $windowID).'").mb_iconize();');
	}

	function bringToFront($windowID="")
	{
		communication_interface::jsExecute('$("#'.($windowID=="" ? $this->windowID : $windowID).'").mb_bringToFront();');
	}

	function close($windowID="")
	{
		communication_interface::jsExecute('$("#'.($windowID=="" ? $this->windowID : $windowID).'").mb_close();');
	}

	function modal($modalMode=false)
	{
		$this->modalMode = $modalMode ? true : false;
	}

	function fullscreen($fullscreen=false)
	{
		$this->fullscreen = $fullscreen ? true : false;
	}

	function dockable($dockable=false)
	{
		$this->dockable = $dockable ? true : false;
	}

	function centerOnScreen($centerOnScreen=false)
	{
		$this->centerOnScreen = $centerOnScreen ? true : false;
	}

	function buttonMinimize($show=true)
	{
		$this->buttonMinimize = $show ? true : false;
	}

	function buttonMaximize($show=true)
	{
		$this->buttonMaximize = $show ? true : false;
	}

	function buttonClose($show=true)
	{
		$this->buttonClose = $show ? true : false;
	}

	function resizable($resizable=true)
	{
		$this->resizable = $resizable ? true : false;
	}

	function windowIcon($windowIcon="")
	{
		$this->windowIcon = $windowIcon;
	}

	function windowTitle($windowTitle="")
	{
		$this->windowTitle = $windowTitle;
	}

	function windowWidth($width=300)
	{
		$this->width = $width;
	}

	function windowHeight($height=150)
	{
		$this->height = $height;
	}

	function addEventFunction_onCreate($fncSrc)
	{
//		$fncSrc = str_replace("'","\\'",$fncSrc);
		$this->onCreate .= $fncSrc;
	}

	function addEventFunction_onClose($fncSrc)
	{
//		$fncSrc = str_replace("'","\\'",$fncSrc);
		$this->onClose .= $fncSrc;
	}

	function addEventFunction_onRestore($fncSrc)
	{
//		$fncSrc = str_replace("'","\\'",$fncSrc);
		$this->onRestore .= $fncSrc;
	}

	function addEventFunction_onMinimize($fncSrc)
	{
//		$fncSrc = str_replace("'","\\'",$fncSrc);
		$this->onIconize .= $fncSrc;
	}

	function addEventFunction_onLoad($fncSrc)
	{
//		$fncSrc = str_replace("'","\\'",$fncSrc);
		$this->onLoad .= $fncSrc;
	}

	function addEventFunction_onResize($fncSrc)
	{
//		$fncSrc = str_replace("'","\\'",$fncSrc);
		$this->onResize .= $fncSrc;
	}

	function setContent($windowContent="")
	{
		$this->windowContent = $windowContent;
	}

	function loadContent($template,$data,$subTemplate="")
	{
		parent::setTemplate($template);
		$this->windowContent = parent::processTemplate($data,$subTemplate);
	}

	function showInfo()
	{
		$this->windowIcon = "symbol_information.png";
		$this->modalMode = true;
		$this->buttonMinimize = false;
		$this->buttonMaximize = false;
		$this->generateWindow(true);
	}

	function showAlert()
	{
		$this->windowIcon = "symbol_error.png";
		$this->modalMode = true;
		$this->buttonMinimize = false;
		$this->buttonMaximize = false;
		$this->generateWindow(true);
	}

	function showQuestion()
	{
		$this->windowIcon = "symbol_help.png";
		$this->modalMode = true;
		$this->buttonMinimize = false;
		$this->buttonMaximize = false;
		$this->generateWindow(true);
	}

	function showWindow()
	{
		if($this->modalMode) {
			$this->buttonMinimize = false;
			$this->buttonMaximize = false;
		}
		$this->generateWindow(false);
	}

	function generateWindow($generateSystemWindow=true)
	{
		if(!$this->resizable || $this->modalMode) {
			$this->buttonMaximize = false;
			$this->fullscreen = false;
		}
		if(!$this->dockable) {
			$this->buttonMinimize = false;
		}

		//i,f,c -> iconize/fullscreen/close
		$topButtons = "";
		$topButtons .= $this->buttonMinimize ? "i" : "";
		$topButtons .= $topButtons == "" ? "" : ",";
		$topButtons .= $this->buttonMaximize ? "f" : "";
		$topButtons .= $topButtons == "" ? "" : ",";
		$topButtons .= $this->buttonClose ? "c" : "";

		if($this->modalMode) {
			communication_interface::assign('modalDock', 'innerHTML', '<div style="background: rgb(0, 0, 0) none repeat scroll 0% 0%; position: fixed; width: 100%; height: 100%; top: 0px; left: 0px; filter:alpha(opacity=70); opacity: 0.7; -moz-opacity:0.7; display: block;" id="mb_overlay"></div><div id="modalContainer" class="containerPlus draggable {buttons:\''.$topButtons.'\', icon:\''.$this->windowIcon.'\', skin:\'default\', width:\''.$this->width.'\', height:\''.$this->height.'\', closed:\'false\', title:\''.$this->windowTitle.'\'}" style="position:fixed;top:100px;left:600px" fullscreen="false">'.$this->windowContent.'</div>');
			communication_interface::jsExecute('$("#modalContainer").buildContainers({ containment:"document", elementsPath:"web/img/containerPlus/", iconPath:"'.($generateSystemWindow ? 'web/img/containerPlus/' : $this->mediaPath).'", dockedIconDim:32, onCreate:function(o){'.$this->onCreate.'}, onClose:function(o){closeModal(o);'.$this->onClose.'}, onRestore:function(o){'.$this->onRestore.'}, onIconize:function(o){'.$this->onIconize.'}, effectDuration:100 });');
//			communication_interface::jsExecute('$("#mb_overlay").hide();');
			communication_interface::jsExecute('$("#modalContainer").mb_centerOnWindow(false);');
			communication_interface::jsExecute('$("#mb_overlay").mb_bringToFront();');
			communication_interface::jsExecute('$("#modalContainer").mb_bringToFront();');
//			communication_interface::jsExecute('$("#mb_overlay").fadeIn(500);');
		}else{
//		Was ist mit AlwaysOnTop??	<-- TODO
//			communication_interface::jsExecute('document.getElementById(\'mainContent\').innerHTML = document.getElementById(\'mainContent\').innerHTML + \'<div id="'.$this->windowID.'" class="containerPlus draggable'.($this->resizable ? ' resizable' : '').' {buttons:\\\''.$topButtons.'\\\', icon:\\\''.$this->windowIcon.'\\\', skin:\\\'white\\\', width:\\\''.$this->width.'\\\', height:\\\''.$this->height.'\\\',dock:\\\'dock\\\',closed:\\\'false\\\',rememberMe:false,title:\\\''.$this->windowTitle.'\\\'}" style="position:absolute;top:50px;left:50px;" fullscreen="false"></div>\'');
//			communication_interface::jsExecute('document.getElementById(\'mainContent\').innerHTML = document.getElementById(\'mainContent\').innerHTML + \'<div id="'.$this->windowID.'" class="containerPlus draggable'.($this->resizable ? ' resizable' : '').' {buttons:\\\''.$topButtons.'\\\', icon:\\\''.$this->windowIcon.'\\\', skin:\\\'white\\\', width:\\\''.$this->width.'\\\', height:\\\''.$this->height.'\\\',dock:\\\'dock\\\',closed:\\\'false\\\',rememberMe:false,title:\\\''.$this->windowTitle.'\\\'}" style="position:absolute;top:50px;left:50px;" fullscreen="false"></div>\'');
			communication_interface::jsExecute('$(\'#mainContent\').append("<div id=\\"'.$this->windowID.'\\" class=\\"containerPlus draggable'.($this->resizable ? ' resizable' : '').' {buttons:\\\''.$topButtons.'\\\', icon:\\\''.$this->windowIcon.'\\\', skin:\\\'default\\\', width:\\\''.$this->width.'\\\', height:\\\''.$this->height.'\\\',dock:\\\'dock\\\',closed:\\\'false\\\',rememberMe:false,title:\\\''.$this->windowTitle.'\\\'}\\" style=\\"position:absolute;top:50px;left:50px;\\" fullscreen=\\"false\\"></div>");');
//$('#mainContent').append('<p>Test</p>');

			communication_interface::assign($this->windowID, 'innerHTML', $this->windowContent);
//			communication_interface::jsExecute('$("#'.$this->windowID.'").buildContainers({ containment:"#mainContent", elementsPath:"web/img/containerPlus/", iconPath:"'.$this->mediaPath.'", dockedIconDim:45, onCreate:function(o){'.($this->dockable ? 'initDock(o,"dock");' : '').$this->onCreate.'}, onClose:function(o){close(o);'.$this->onClose.'}, onRestore:function(o){restore(o);'.$this->onRestore.'}, onIconize:function(o){iconize(o);'.$this->onIconize.'}, onLoad:function(o){'.$this->onLoad.'}, onResize:function(o){cp_resize(o);'.$this->onResize.'}, effectDuration:100 });');
			communication_interface::jsExecute('$("#'.$this->windowID.'").buildContainers({ containment:"#mainContent", elementsPath:"web/img/containerPlus/", iconPath:"'.$this->mediaPath.'", dockedIconDim:32, onCreate:function(o){'.($this->dockable ? 'initDock(o,"dock");' : '').$this->onCreate.'}, onClose:function(o){close(o);'.$this->onClose.'}, onRestore:function(o){restore(o);'.$this->onRestore.'}, onIconize:function(o){iconize(o);'.$this->onIconize.'}, onLoad:function(o){'.$this->onLoad.'}, onResize:function(o){'.$this->onResize.'}, effectDuration:100 });');
//			communication_interface::alert('$("#'.$this->windowID.'").buildContainers({ containment:"#mainContent", elementsPath:"web/img/containerPlus/", iconPath:"'.$this->mediaPath.'", dockedIconDim:45, onCreate:function(o){'.($this->dockable ? 'initDock(o,"dock");' : '').$this->onCreate.'}, onClose:function(o){close(o);'.$this->onClose.'}, onRestore:function(o){restore(o);'.$this->onRestore.'}, onIconize:function(o){iconize(o);'.$this->onIconize.'}, onLoad:function(o){'.$this->onLoad.'}, onResize:function(o){cp_resize(o);'.$this->onResize.'}, effectDuration:100 });');
			if($this->centerOnScreen) communication_interface::jsExecute('$("#'.$this->windowID.'").mb_centerOnWindow(false);');
			if($this->fullscreen) communication_interface::jsExecute('aafw_fullscreen($(\'#'.$this->windowID.'\'));');
		}
	}

//End of class
}

}
?>
