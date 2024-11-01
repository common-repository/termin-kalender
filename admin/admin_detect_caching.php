<?php
defined('WPINC') || die;
 //$page_url = TER_KAL_TK_HEADLINE;  // 'post_title' => TER_KAL_TK_HEADLINE, // $page_url = '/my easy Termin-Kalender calendar';  // kein folgendes / weil -2/ -3/ usw. möglich
function ter_kal_detect_caching_plugins_get_instructions($page_url) {
  // Check active plugins via transient for efficiency
 $caching_plugins = get_transient('ter_kal_caching_plugins');
if (!$caching_plugins) {
     $caching_plugins = [
      'wp-rocket/wp-rocket.php', 'w3-total-cache/w3-total-cache.php', 'cache-enabler/cache-enabler.php', 'wp-super-cache/wp-super-cache.php',
      'wp-fastest-cache/wp-fastest-cache.php', 'breeze/breeze.php', 'litespeed-cache/litespeed-cache.php', 'swift-performance/swift-performance.php',
      'hummingbird/hummingbird.php', 'wp-optimize/wp-optimize.php', 'clear-cache/clear-cache.php', 'wp-cache-by-hostpapa/wp-cache-by-hostpapa.php',
      'wp-pagecache/wp-pagecache.php', 'wp-cp-super-cache/wp-cp-super-cache.php', 'wp-engine-smartcache/wp-engine-smartcache.php', 'cachepress/cachepress.php',
      'fastcgi-cache-control/fastcgi-cache-control.php', 'speed-booster-rocket/speed-booster-rocket.php', 'wphc/wphc.php', 'wp-total-cache-free/wp-total-cache-free.php',
    ];
    set_transient('ter_kal_caching_plugins', $caching_plugins, HOUR_IN_SECONDS);
	// delete_transient('caching_plugins');
}

  $messages = [];
  $active_caching_plugins = [];
  $active_plugins = get_option('active_plugins');


    foreach ($caching_plugins as $plugin) {
      if (in_array($plugin, $active_plugins)) {
        $active_caching_plugins[] = $plugin;
      }
    }


  // Return instructions based on found plugins
  foreach ($active_caching_plugins as $plugin) {

    switch ($plugin) {
case 'wp-rocket/wp-rocket.php':
        $messages[] = '<div class="admin_red">'. esc_html__( "WP Rocket: Enable 'Bypass cache for logged-in users' or create a 'Purge Cache' rule for ", 'termin-kalender' ) . $page_url ."</div>";
        break;
case 'w3-total-cache/w3-total-cache.php':
        $messages[] = '<div class="admin_red">'. esc_html__( "W3 Total Cache: Use the 'Empty Page Cache' button or create a 'Don't Cache URL' rule for ", 'termin-kalender' ) . $page_url  ."</div>";
        break;
case 'cache-enabler/cache-enabler.php':
        $messages[] = '<div class="admin_red">'. esc_html__( "Cache Enabler: Disable 'Page Caching' or create a 'Don't Cache URL' rule for ", 'termin-kalender' ) . $page_url  ."</div>";
        break;
case 'litespeed-cache/litespeed-cache.php':
        $messages[] = '<div class="admin_red">'. esc_html__( "Litespeed Cache: Create a 'Don't Cache URL' rule for ", 'termin-kalender' ) . $page_url  ."</div>";
        break;
default:
        $messages[] = '<div class="admin_red">'. esc_html__( "This page appears to be cached by a plugin. If you have difficulties saving changes, contact plugin support for disabling caching for ", 'termin-kalender' ) .  $page_url  ."</div>";
        break;
    }
  }

 // Analyze core caching functions (fast & safe)
  if (function_exists('wp_cache_get')) {
    $messages[] = '<div class="admin_green" >' . esc_html__('WordPress wp_cache_get function detected. This should be fine.', 'termin-kalender') . '</div>';
  }  elseif (function_exists('wp_cache_flush')) {
    $messages[] = '<div class="admin_green" >' . esc_html__('WordPress wp_cache_flush function detected. This should be fine.', 'termin-kalender') . '</div>';
  }

  $messages = implode("<br>", $messages );

    $allowed_tags = array(
        'div' => array(
            'class' => true
        ),
        'hr' => array(),
        'br' => array()
    );

  echo '<hr>' . wp_kses($messages, $allowed_tags);
  // return;
}


