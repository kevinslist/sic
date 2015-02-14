<?php
/*
 * itach api id: 057d6b19-5f2c-4deb-bd7c-31f659caaf4e
 * for web interface: https://irdatabase.globalcache.com
 * https://irdatabase.globalcache.com/api/v1/057d6b19-5f2c-4deb-bd7c-31f659caaf4e/manufacturers
 */
class signal_controller extends my_controller {
  
  public function __construct() {
    parent::__construct();
  }
  public function index($base_64_command = NULL){
    
    //print 'SIGNAL.INDEX.:' . $base_64_command;
    $c = unserialize(base64_decode(urldecode($base_64_command)));
    if(!$c['is-repeat']){
      var_export($c);
      signal_controller::do_send_signal('#'.$c['header-string'], $c['remote-string'], $c['last-signal']);
    }
  }
  
  static function do_send_signal($remote_code, $current_signal, $current_time){
    $signal_name = signal_controller::$channel_codes[$current_signal];
    //$signal_sent_diff = $current_time - itach::$remotes[$remote_code]['last-sent'];
    itach::$remotes[$remote_code]['repeat'] = 0;
    itach::$remotes[$remote_code]['previous-signal'] = $current_signal;
    itach::$remotes[$remote_code]['last-sent'] = $current_time;
    //self::$previous_remote_code = $remote_code;
    itach::send_signal($remote_code, $signal_name);
  }
  
  static $channel_codes = array(
      "0101000000000110" => "cable_power",
      "1101000000001010" => "cable_channel_up",
      "0011000000000010" => "cable_channel_down",
      "1010100000000101" => "cable_favorite",
      "1011110000000000" => "cable_my_dvr",
      "0101100000001010" => "cable_on_demmand",
      "0101110000001100" => "cable_page_up",
      "1101110000000100" => "cable_page_down",
      "1000100000000111" => "cable_ok_select",
      "0010110000001001" => "cable_up_arrow",
      "1110110000000110" => "cable_right_arrow",
      "1010110000000001" => "cable_down_arrow",
      "0110110000001110" => "cable_left_arrow",
      "0111100000001000" => "cable_rewind",
      "1011100000000100" => "cable_fast_forward",
      "1101100000000010" => "cable_play",
      "0011100000001100" => "cable_stop",
      "1111100000000000" => "cable_pause",
      "1000110000000011" => "cable_record",
      "0111110000001111" => "cable_live",
      "0011110000001000" => "cable_jump_back",
      "0000110000001011" => "cable_guide",
      "1100110000000101" => "cable_info",
      "1001100000000110" => "cable_menu",
      "0100100000001011" => "cable_exit",
      "0100110000001101" => "cable_help",
      "1100100000000011" => "cable_last",
      "1000000000001111" => "cable_1",
      "0100000000000111" => "cable_2",
      "1100000000001011" => "cable_3",
      "0010000000000011" => "cable_4",
      "1010000000001101" => "cable_5",
      "0110000000000101" => "cable_6",
      "1110000000001001" => "cable_7",
      "0001000000000001" => "cable_8",
      "1001000000001110" => "cable_9",
      "1111111111111111" => "cable_0",
      "0010100000001101" => "cable_tv_vcr",
      "0000001000000011" => "cable_hd_zoom",
      "0100010000000011" => "cable_pip_on_off",
      "1100010000001101" => "cable_pip_swap",
      "0010010000000101" => "cable_pip_move",
      "1010010000001001" => "cable_pip_channel_up",
      "0110010000000001" => "cable_pip_channel_up",
      "0110100000001001" => "cable_lock",
      "1001110000000010" => "cable_day_minus",
      "0001110000001010" => "cable_day_plus",
      "00100000110111110001000011101111" => "aux_power",
      "00100000110111111110000000011111" => "aux_left_arrow",
      "00100000110111111011000001001111" => "aux_down_arrow",
      "00100000110111110011000011001111" => "aux_up_right_arrow",
      "00100000110111110011001011001101" => "aux_ok_select",
      "00100000110111110111000010001111" => "aux_menu",
      "00100000110111110101100010100111" => "aux_last",
      "00100000110111111111101000000101" => "aux_help",
      "00100000110111111101100000100111" => "aux_info",
      "00100000110111111001000001101111" => "aux_tv_vcr",
      "00100000110111110100000010111111" => "aux_volume_up",
      "00100000110111111100000000111111" => "aux_volume_down",
      "00100000110111110000000011111111" => "aux_channel_up",
      "00100000110111111000000001111111" => "aux_channel_down",
      "00100000110111110101000010101111" => "aux_mute",
      "00100000110111111000101001110101" => "aux_pip_channel_down",
      "00100000110111111000100001110111" => "aux_1",
      "00100000110111110100100010110111" => "aux_2",
      "00100000110111111100100000110111" => "aux_3",
      "00100000110111110010100011010111" => "aux_4",
      "00100000110111111010100001010111" => "aux_5",
      "00100000110111110110100010010111" => "aux_6",
      "00100000110111111110100000010111" => "aux_7",
      "00100000110111110001100011100111" => "aux_8",
      "00100000110111111001100001100111" => "aux_9",
      "00100000110111110000100011110111" => "aux_0",
      
      "00000010111111010101100010100111" => "tv_volume_up",
      "00000010111111010111100010000111" => "tv_volume_down",
      "00000010111111010000100011110111" => "tv_volume_mute",
      "00000010111111010100100010110111" => "tv_power",
      "00000010111111010000000111111110" => "tv_menu",
      "00000010111111010011100011000111" => "tv_info",
      "00000010111111010100000110111110" => "tv_up_arrow",
      "00000010111111011100000100111110" => "tv_down_arrow",
      "00000010111111011001100001100111" => "tv_right_arrow",
      "00000010111111011011100001000111" => "tv_left_arrow",
      "00000010111111010001101011100101" => "tv_exit",
      "00000010111111011111000000001111" => "tv_tv_vcr",
      "00000010111111011100101000110101" => "tv_pip_channel_swap",
      "00000010111111011101001000101101" => "tv_pip_channel_down",
      "00000010111111010101001010101101" => "tv_pip_channel_up",
      "00000010111111011000101001110101" => "tv_pip_on_off",
       

  );

}