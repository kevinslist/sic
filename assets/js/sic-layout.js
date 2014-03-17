$(document).ready(sic_init_layout_global);
var site_url = '/';

function sic_init_layout_global(){
}

function sic_init_layout_global_Old(){
  sic_init_default_layout();
  sic_init_sections();
}

function sic_init_sections(){
  $('#layout-header').load(sic_url('header'));
  $('#navigation-helper').load(sic_url('sidebar_right'));
}

function sic_init_default_layout(){
  	myLayout = $('body').layout({

		//	reference only - these options are NOT required because 'true' is the default
			closable:					true	// pane can open & close
		,	resizable:					true	// when open, pane can be resized 
		,	slidable:					true	// when closed, pane can 'slide' open over other panes - closes on mouse-out
		,	livePaneResizing:			false
		,	spacing_open:		2		// no resizer-bar when open (zero height)
		,	spacing_closed:		4		// no resizer-bar when open (zero height)
    , togglerLength_open: '100%'
    , togglerLength_closed: '100%'
		//	some resizing/toggling settings
		,	north__slidable:			false	// OVERRIDE the pane-default of 'slidable=true'
		,	north__resizable:			false	// OVERRIDE the pane-default of 'resizable=true'
		,	north__minSize:				50



		//	enable showOverflow on west-pane so CSS popups will overlap north pane
		,	west__showOverflowOnHover:	true

		//	enable state management
		,	stateManagement__enabled:	true // automatic cookie load & save enabled by default

		,	showDebugMessages:			false // log and/or display messages from debugging & testing code
		});
/*
 * 
		//	some pane-size settings
		,	west__minSize:				100
		,	east__size:					300
		,	east__minSize:				200
		,	east__maxSize:				.5 // 50% of layout width
		,	center__minWidth:			100
 */
}

function sic_url(path){
  return site_url + path;
}
