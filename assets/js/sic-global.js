$(document).ready(sic_init_global);
var site_url = '/';

function sic_init_global(){
  site_url = $('body').attr('data-site-url');
  sic_init_default_layout();
  sic_init_header();
  
}

function sic_init_header(){
  $('#layout-header').load(sic_url('header'))
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


window.log=function(){log.history=log.history||[];log.history.push(arguments);if(this.console){arguments.callee=arguments.callee.caller;var a=[].slice.call(arguments);(typeof console.log==="object"?log.apply.call(console.log,console,a):console.log.apply(console,a))}};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());

function kb_add_hover_class(){
  $(this).addClass('hover');
}
function kb_remove_hover_class(){
  $(this).removeClass('hover');
}
