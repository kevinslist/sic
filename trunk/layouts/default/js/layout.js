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
    size: 20,
    spacing_open: 0
  },
  center: {
    //onresize: center_resized
    
  },
  west: {
    resizable: false
  },
  east: {
    //initClosed: true
  },
  south: {
    size: 30,
    resizable: false,
    initClosed: true
  }
};
