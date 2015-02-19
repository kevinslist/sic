<?php
require dirname(dirname(__FILE__)) . '/config.php';
$config['KB_MATRIX_IP']                   = '192.168.1.72';
$config['KB_DENON_IP']                    = '192.168.1.129';
$config['KB_ITACH_IP']                    = '192.168.1.70';
$config['KB_MEMCACHE_PORT']               = '11211';
$config['KB_MASTER_HOSTNAME']             = 'k';
$config['KB_QUEUE_NEW_SIGNAL_URL']        = 'https://k/cron.php/remote/queue/';
$config['KB_SIGNAL_CHECK_SPECIAL_URL']    = 'https://k/cron.php/remote/signal/check_special';
$config['KB_RTL_433_PROCESSOR_SCRIPT_PATH'] = 'php ' . KB_APP_PATH . '/cron.php remote/rtl_433/start '; // dongle_index appended to this, then  2>&1
$config['KB_CRON_TASKS_SCRIPT_PATH']        = 'php ' . KB_APP_PATH . '/cron.php remote/cron_tasks/start  2>&1';
$config['CRON_TASKS_GLOBAL_USLEEP']         = 500000; // 1/2 second

$config['KB_CONFIG_ROUTER_INFO_SEM_LOCK_PORT']          = '123323'; // 1/2 second
$config['KB_SIGNAL_QUEUE_KEY']                          = 'kb_config_router_signal_queue';
