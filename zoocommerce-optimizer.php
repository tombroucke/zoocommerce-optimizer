<?php
namespace Otomaties\ZooCommerceOptimizer;

/**
* Plugin Name: ZooCommerce Optimizer
* Description: Disable WooCommerce packages, admin features, admin notices, marketplace suggestions, extensions submenu, and more.
* Author: Tom Broucke
* Author URI: https://tombroucke.be
 Version:           1.0.1
* License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/app/Optimizer.php';

$analyticsRestRequestNeedle = '/wc-analytics';
$wcAdminNeedle = 'wc-admin';

Optimizer::instance()
    ->excludeRequestUriPatterns($analyticsRestRequestNeedle, $wcAdminNeedle)
    ->disablePackages()
    ->removeFeatures()
    ->suppressAdminNotices()
    ->disableMarketplaceSuggestions()
    ->disableExtensionsSubmenu()
    ->disableWidgets();
