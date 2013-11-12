<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Router extends kb_router {
	private $_suffix = "_controller";
	public function __construct() {
			parent::__construct();
	}

}