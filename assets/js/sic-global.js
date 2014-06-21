$(document).ready(sic_init_global);
var site_url = '/';

function sic_init_global(){
  site_url = $('body').attr('data-site-url');
  log('site_url:'+site_url);
}

function kb_add_hover_class(){
  $(this).addClass('hover');
}
function kb_remove_hover_class(){
  $(this).removeClass('hover');
}

window.log=function(){log.history=log.history||[];log.history.push(arguments);if(this.console){arguments.callee=arguments.callee.caller;var a=[].slice.call(arguments);(typeof console.log==="object"?log.apply.call(console.log,console,a):console.log.apply(console,a))}};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();){b[a]=b[a]||c}})((function(){try
{console.log();return window.console;}catch(err){return window.console={};}})());