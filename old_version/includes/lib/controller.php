<?php

class controller{
  var $js  = array();
  var $css = array();
  
  public function __construct(){
    
  }
  
  public function add_css($css = null){
      if(is_array($css)){
        foreach($css as $c){ $this->css[] = $c; }
      }else{
        $this->css[] = $css;
      }
  }
  
  public function add_js($js = null){
      if(is_array($js)){
        foreach($js as $j){ $this->js[] = $j; }
      }else{
        $this->js[] = $js;
      }
  }
	
	static public function css($css=null){

      $tag = '<style>';
      if(is_array($css)){
        foreach($css as $c){ $tag .= app::read($css); }
      }else{
        $tag .= app::read($css);
      }
      $tag .= '</style>';
      print $tag;
    
	}
  
	static public function js($js=null){

      $tag = '<script type="text/javascript">';
      if(is_array($js)){
        foreach($js as $j){ $tag .= self::read($js); }
      }else{
        $tag .= app::read($js);
      }
      $tag .= '</script>';
      print $tag;
   
    
  }
  
  
}