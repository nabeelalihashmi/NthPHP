# NthPHP Framework

NthPHP is a fast, optimized PHP framework that simplifies web development by leveraging modern PHP features. It uses PHP attributes, automatic routing, Blade templating, and RedBeanPHP for database management.

## Key Features

- **Fast & Optimized**: NthPHP is built for speed, providing an efficient routing system and optimized performance.
- **PHP Attributes for Routing**: Routes are defined using PHP attributes, allowing for a clean, modern approach to routing.
- **Automatic Routing**: Routes are automatically collected from controllers, reducing the need for manual route registration.
- **Blade Templating**: Uses **BladeOne** as the templating engine, with automatic routes generation from views stored in a specific folder (`/app/Views/_pages`).
- **RedBeanPHP**: Uses **RedBeanPHP** for database interactions. No need for models, but models can be used if desired.

## Configuration

The configuration is stored in `app/config.php` and includes settings for routing, controllers, views, and caching.

<pre>
<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;

return [
    'active_controllers' => [
        HomeController::class,
        UserController::class
    ],

    'not_found' => [
        HomeController::class, 'notFound'
    ],

    'method_not_allowed' => [
        HomeController::class, 'methodNotAllowed'
    ],

    'routes_cache_enabled' => false,
    'routes_cache_file' => __DIR__ . '/../cache/routes/routes.cache',
    'route_collection_cache_file' => __DIR__ . '/../cache/routes/collector.cache',
    'automatic_routes' => true,
];
</pre>

### Automatic Views Directory

The framework uses **Blade** templating with the default directory for views located at `/app/Views`.

If there are blade view files in `/app/Views/_pages`, they are considered as route.

## Documentation

- **Blade**: [Laravel Blade Documentation](https://laravel.com/docs/8.x/blade)
- **BladeOne**: [BladeOne GitHub Repository](https://github.com/EFTEC/BladeOne)
- **RedBeanPHP**: [RedBeanPHP Documentation](https://redbeanphp.com/)

## Installation

1. Clone the repository or download the framework.
2. Run `composer install` to install the required dependencies.
3. Configure your database and other settings as needed.
4. Start developing your application by creating controllers and views.

## Usage

1. Define routes in your controllers using PHP attributes.
2. Views are automatically rendered based on routes, with Blade templating.
3. Database interactions can be handled using **RedBeanPHP**, with or without models.

## Caching

Please note that when routes are changed, and caching is set to true on config, please delete `cache/routes/collector.cache` and `cache/routes/routes.cache` files.

## Developer

This framework is developed by **Nabeel Ali**. You can connect with the developer on LinkedIn:

- [Nabeel Ali](https://linkedin.com/in/nabeelalihashmi)

---

This is just the beginning. NthPHP is a work in progress and continuously evolving.
