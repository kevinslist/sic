

$(function(){
  $('input.start-collection-scanner-button').click(start_scanning);
});

function start_scanning(){
  $.getJSON(home + 'collection_scanner/start', scanning_started);
}

function scanning_started(j){
  for(i in j){
    alert(i + ':' + j[i]);
  }
}