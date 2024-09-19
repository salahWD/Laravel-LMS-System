<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class ConfigHelper {
  public static function updateDashboardConfig(array $newSettings) {
    $file = config_path('settings.php');

    // Get the existing settings
    $config = config('settings');

    // Merge the new settings
    $updatedConfig = array_merge($config, $newSettings);

    // Prepare the content to write back to the config file
    $content = '<?php return ' . var_export($updatedConfig, true) . ';';

    // Write the updated config to the file
    File::put($file, $content);
  }
}
