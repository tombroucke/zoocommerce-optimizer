<?php
namespace Otomaties\ZooCommerceOptimizer;

/**
 * Plugin Name:       ZooCommerce Optimizer
 * Description:       Disable WooCommerce admin features, admin notices, marketplace suggestions, extensions submenu, and more.
 * Author:            Tom Broucke
 * Author URI:        https://tombroucke.be
 * Version:           2.0.0
 * License:           GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/app/Optimizer.php';

$analyticsRestRequestNeedle = '/wc-analytics';
$wcAdminNeedle = 'wc-admin';
$shippingZones = 'page=wc-settings&tab=shipping';

Optimizer::instance()
    ->excludeRequestUriPatterns($analyticsRestRequestNeedle, $wcAdminNeedle, $shippingZones)
    ->removeFeatures()
    ->suppressAdminNotices()
    ->disableMarketplaceSuggestions()
    ->disableExtensionsSubmenu()
    ->disableWidgets();
