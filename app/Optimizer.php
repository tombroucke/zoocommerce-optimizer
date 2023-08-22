<?php

namespace Otomaties\ZooCommerceOptimizer;

class Optimizer
{
    private static $instance = null;
    private array $excludePatterns = [];

    public function excludeRequestUriPatterns(...$patterns) : Optimizer
    {
        $this->excludePatterns = array_merge($this->excludePatterns, $patterns);
        return $this;
    }

    private function requestIsExcluded() : bool
    {
        foreach ($this->excludePatterns as $excludePattern) {
            if (strpos(urldecode($_SERVER['REQUEST_URI']), $excludePattern) !== false) {
                return true;
            }
        }
        return false;
    }

    public function disablePackages() : Optimizer
    {
        if ($this->requestIsExcluded()) {
            return $this;
        }

        remove_action('plugins_loaded', 'Automattic\WooCommerce\Packages::on_init');
        add_action('plugins_loaded', function () {
            if (!is_admin()) {
                return;
            }
            // Load necessary scripts and constants
            \Automattic\WooCommerce\Internal\Admin\FeaturePlugin::instance()->init();
        });

        $this->disableAdminPointers();
        return $this;
    }

    /**
     * Disabling admin pointers is necessary when disabling packages
     * because admin pointer scripts rely on woocommerce block scripts
     *
     * @return void
     */
    private function disableAdminPointers() : void
    {
        // Disable WooCommerce Admin Pointers
        add_action('init', function () {
            global $wp_filter;
            foreach ($wp_filter['admin_enqueue_scripts']->callbacks[10] as $key => $callback) {
                if (!is_array($callback['function'])) {
                    continue;
                }
                
                if ($callback['function'][0] instanceof \WC_Admin_Pointers && $callback['function'][1] == 'setup_pointers_for_screen') {
                    unset($wp_filter['admin_enqueue_scripts']->callbacks[10][$key]);
                }
            }
        }, 11);
    }

    public function removeFeatures() : Optimizer
    {
        if ($this->requestIsExcluded()) {
            return $this;
        }

        add_filter('woocommerce_admin_features', function (array $features) : array {
            $removeFeatures = apply_filters('zoocommerce_optimizer_remove_admin_features', [
                'activity-panels',
                'analytics',
                'product-block-editor',
                'coupons',
                'core-profiler',
                'customer-effort-score-tracks',
                'experimental-fashion-sample-products',
                'import-products-task',
                'shipping-smart-defaults',
                'shipping-setting-tour',
                'homescreen',
                'marketing',
                'mobile-app-banner',
                'navigation',
                'onboarding',
                'onboarding-tasks',
                'remote-inbox-notifications',
                'remote-free-extensions',
                'payment-gateway-suggestions',
                'shipping-label-banner',
                'subscriptions',
                'store-alerts',
                //'transient-notices',
                'woo-mobile-welcome',
                'wc-pay-promotion',
                'wc-pay-welcome-page'
            ]);
            
            foreach ($removeFeatures as $removeFeature) {
                if (($key = array_search($removeFeature, $features)) !== false) {
                    unset($features[$key]);
                }
            }
            return $features;
        }, 90);

        return $this;
    }

    public function suppressAdminNotices() : Optimizer
    {
        add_filter('woocommerce_helper_suppress_admin_notices', function () {
            return apply_filters('zoocommerce_optimizer_suppress_admin_notices', true);
        }, 999);

        return $this;
    }

    public function disableMarketplaceSuggestions() : Optimizer
    {
        add_filter('woocommerce_allow_marketplace_suggestions', function () {
            return apply_filters('zoocommerce_optimizer_allow_marketplace_suggestions', false);
        }, 999);

        return $this;
    }

    public function disableExtensionsSubmenu() : Optimizer
    {
        add_action('admin_menu', function () {
            if (apply_filters('zoocommerce_optimizer_disable_wc_addons', true)) {
                remove_submenu_page('woocommerce', 'wc-addons');
            }
            
            if (apply_filters('zoocommerce_optimizer_disable_wc_addons_helper', true)) {
                remove_submenu_page('woocommerce', 'wc-addons&section=helper');
            }
        }, 999);

        return $this;
    }

    public function disableWidgets() : Optimizer
    {
        add_action('widgets_init', function () {
            $unregisterWidgets = apply_filters('zoocommerce_optimizer_unregister_widgets', [
                'WC_Widget_Products',
                'WC_Widget_Product_Categories',
                'WC_Widget_Product_Tag_Cloud',
                'WC_Widget_Cart',
                'WC_Widget_Layered_Nav',
                'WC_Widget_Layered_Nav_Filters',
                'WC_Widget_Price_Filter',
                'WC_Widget_Product_Search',
                'WC_Widget_Recently_Viewed',
                'WC_Widget_Recent_Reviews',
                'WC_Widget_Top_Rated_Products',
                'WC_Widget_Rating_Filter',
            ]);
            
            foreach ($unregisterWidgets as $unregisterWidget) {
                unregister_widget($unregisterWidget);
            }
        });

        return $this;
    }

    
    public static function instance() : Optimizer
    {
        if (self::$instance == null) {
            self::$instance = new Optimizer();
        }
        return self::$instance;
    }
}
