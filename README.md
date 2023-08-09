# ZooCommerce Optimize

This plugin disables WooCommerce packages. Usefull if you are developing a custom theme and don't want to load all the packages. Some features are not needed in the admin area.

## Installation

### Composer

```bash
composer require tombroucke/zoocommerce-optimize
```

## Enable analytics

```php
add_filter('zoocommerce_optimize_remove_admin_features', function ($removeFeatures) {
    return array_filter($removeFeatures, function ($feature) {
        return $feature !== 'analytics';
    });
});
```

## Why ZooCommerce

This plugin needs to load later than WooCommerce. Z > W.
