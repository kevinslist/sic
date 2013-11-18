<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * MY_Loader
 *
 * Extended the core MY_Loader class in order to force a different naming
 * convention for controllers.
 *
 */
class my_loader extends kb_loader
{
    public function __construct()
    {
        parent::__construct();
    }
}