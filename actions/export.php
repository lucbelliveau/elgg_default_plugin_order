<?php

if (elgg_default_plugin_order_export_config() !== false) {
  system_message(elgg_echo('plugin_loader:exported_success'));
} else {
  system_message(elgg_echo('plugin_loader:exported_failed'));
}