<?php

elgg_register_event_handler('init', 'system', 'plugin_order_init');

function plugin_order_init() {
    elgg_register_action(
      'admin/plugin_loader/import',
      dirname(__FILE__) . '/actions/import.php',
      'admin'
    );
    elgg_register_action(
      'admin/plugin_loader/export',
      dirname(__FILE__) . '/actions/export.php',
      'admin'
    );
    elgg_register_admin_menu_item(
      'configure',
      'plugin_order',
      'configure_utilities'
    );
}

function elgg_default_plugin_order_export_config() {
  $plugins = elgg_get_plugins('all');
  $config_file = elgg_get_config('path')."plugin_config.ini";
  $data =
    "# GCconnex plugin order.\n".
    "# See mod/elgg_default_plugin_order/start.php\n\n";
  foreach ($plugins as $plugin) {
    $enabled = (is_plugin_enabled($plugin->title)) ? 'enabled' : 'disabled';
    $data .= "{$plugin->title}={$enabled}\n";
  }
  return file_put_contents($config_file, $data);
}

function elgg_default_plugin_order_load_config(){
	$config_settings = array();
	$config_file = elgg_get_config('path')."plugin_config.ini";
	if(file_exists($config_file)){
	   $config_settings = parse_ini_file($config_file);
	}
	$config_hash = md5(serialize($config_settings));
	$old_config = get_plugin_setting('config','elgg_default_plugin_order');
	if($old_config != $config_hash){
		set_plugin_setting('config',$config_hash,'elgg_default_plugin_order');
		return $config_settings;
	}
	return $config_settings;
}
function elgg_default_plugin_order_reorder(){
	$final_order = array();
	$sequence = 10;
	$config_settings = elgg_default_plugin_order_load_config();
	if(!empty($config_settings)){
		foreach($config_settings as $plugin => $status){
			$final_order[$sequence] = $plugin;
			$sequence+=10;
		}
		regenerate_plugin_list($final_order);
		elgg_filepath_cache_reset();
	}

}
function elgg_default_plugin_order_set_status(){
	$config_settings = elgg_default_plugin_order_load_config();
	if(!empty($config_settings)){
		foreach($config_settings as $plugin => $status){
			switch($status){
				case 'enabled':
					if(!is_plugin_enabled($plugin)){
						enable_plugin($plugin);
					}
				break;
				case 'disabled':
					if(is_plugin_enabled($plugin)){
						disable_plugin($plugin);
					}
				break;
			}
		}
		elgg_filepath_cache_reset();
	}
}


register_elgg_event_handler('init','system','elgg_default_plugin_order');

?>
