<?php
//if uninstall not called from WordPress exit
if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
//delete wp_option db record
delete_option('samurai'); 
?>
