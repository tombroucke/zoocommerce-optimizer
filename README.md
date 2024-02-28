# ZooCommerce Optimizer

This plugin will remove bloat from your WooCommerce website. Currently, there is no way to disable WooCommerce blocks completely, without breaking other functionality.

## Installation

### Composer

```bash
composer require tombroucke/zoocommerce-optimizer
```

## Enable analytics

```php
add_filter('zoocommerce_optimizer_remove_admin_features', function ($removeFeatures) {
    return array_filter($removeFeatures, function ($feature) {
        return $feature !== 'analytics';
    });
});
```

## Why ZooCommerce

This plugin needs to load later than WooCommerce. Z > W.
