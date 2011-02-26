var layout = undefined;

$(function(){
 layout = $('body').layout(main_layout_options);
});


var main_layout_options = {
  defaults :{
    //applyDefaultStyles: true
    reizeWhileDragging:		true,
    useStateCookie:				true,
    spacing_open: 3,
    spacing_closed: 3

  },
  north: {
    resizable: false,
    size: 30,
    spacing_open: 0
  },
  center: {
    //onresize: center_resized
    onresize : layout_center_panel_resized
    
  },
  west: {
    resizable: false,
    onresize : layout_west_panel_resized
  },
  east: {
    //initClosed: true
    size: 900
  },
  south: {
    size: 30,
    resizable: true,
    initClosed: true
  }
};


function layout_west_panel_resized(){
  $('#application-navigation').trigger('west_resized');
  return true;
}
function layout_center_panel_resized(){
  $('#layout-playlist').trigger('center_resized');
  return true;
}