$(document).ready(sic_init_global);
var site_url = '/';

function sic_init_global(){
  site_url = $('body').attr('data-site-url');
  sic_check_main_item_inited();
}

function kb_add_hover_class(){
  $(this).addClass('hover');
}
function kb_remove_hover_class(){
  $(this).removeClass('hover');
}

var c = 1;

function sic_main_menu_item_clicked(){
  var load_view = $(this).attr('data-sic-load-view');
  $('#sic-main-item-main-menu').hide();
  $('#sic-wrapper').load(load_view);
}

function sic_init_main_menu_item(){
 $(this).click(sic_main_menu_item_clicked);
}

function sic_init_main_menu(p){
  $('#sic-main-menu li').each(sic_init_main_menu_item);
}

function sic_check_main_item_inited(){
  $('#sic-wrapper .sic-main-item-wrapper:not([data-sic-inited])').each(sic_init_new_main_item);
}

function sic_init_new_main_item(){
  $(this).attr('data-sic-inited',true);
  var f = $(this).attr('data-sic-init-function');
  window[f]();
}


window.log=function(){log.history=log.history||[];log.history.push(arguments);if(this.console){arguments.callee=arguments.callee.caller;var a=[].slice.call(arguments);(typeof console.log==="object"?log.apply.call(console.log,console,a):console.log.apply(console,a))}};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());