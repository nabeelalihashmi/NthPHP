<!-- image public/logo.php -->


# NthPHP Framework
![](public/logo.png)

NthPHP is a fast, optimized PHP framework designed to simplify web development using modern PHP features. It provides high performance, leveraging **Swoole** for asynchronous, coroutine-based processing, allowing your application to handle thousands of requests concurrently. Additionally, it can run in a traditional PHP environment with **Apache**, **Nginx**, **LiteSpeed**, or any other similar web server, offering flexibility depending on your performance needs. The same code works seamlessly across all these environments without any changes.

## Key Features

- **Fast & Optimized**: NthPHP is built for speed, providing an efficient routing system and optimized performance.
- **PHP Attributes for Routing**: Routes are defined using PHP attributes, allowing for a clean, modern approach to routing.
- **Automatic Routing**: Routes are automatically collected from controllers, reducing the need for manual route registration.
- **Blade Templating**: Uses **BladeOne** as the templating engine, with automatic routes generation from views stored in a specific folder (`/app/Views/_pages`).
- **RedBeanPHP**: Uses **RedBeanPHP** for database interactions. No need for models, but models can be used if desired.
- **Swoole Integration**: NthPHP can utilize **Swoole** for asynchronous, coroutine-based performance, dramatically improving scalability and handling of concurrent requests. It can run in **Swoole** for better performance without changing the core code base.
- **Compatibility with Apache/Nginx/LiteSpeed**: NthPHP is fully compatible with traditional web servers like **Apache**, **Nginx**, and **LiteSpeed**. Whether you deploy on **Swoole** for concurrency and performance boosts or use a traditional web server, the same code works seamlessly across all environments, making the framework extremely flexible.

## Configuration

The configuration is stored in `app/config.php` and includes settings for routing, controllers, views, and caching.

```
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


    'database' => [
        'dsn' => 'mysql:host=localhost;dbname=test',
        'username' => 'root',
        'password' => ''
    ],

    'swoole_static' => '/(partytown|.well-known|public|favicon.ico|sitemap.xml|sitemap.min.xml|robots.txt|BingSiteAuth.xml|ads.txt)($|\/)/',
];
```

### Automatic Views Directory

The framework uses **Blade** templating with the default directory for views located at `/app/Views`.

If there are blade view files in `/app/Views/_pages`, they are considered as routes.

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

```
#[Route(method, uri, middleware)]

#[Route(['GET', 'POST'], '/user/{name}', [AuthMiddleware::class])]

```

2. Views in `app/views/_pages` are automatically rendered based on routes, with Blade templating.

3. Database interactions can be handled using **RedBeanPHP**, with or without models.

4. The framework can run on **Swoole**, **Apache**, **Nginx**, **LiteSpeed**, or any other similar server environment without any changes to your code.

```
php server.php
```

## Caching

Please note that when routes are changed, and caching is set to true in the config, please delete `cache/routes/collector.cache` and `cache/routes/routes.cache` files.

## Developer

This framework is developed by **Nabeel Ali**. You can connect with the developer on LinkedIn:

- [Nabeel Ali](https://linkedin.com/in/nabeelalihashmi)

---

This is just the beginning. NthPHP is a work in progress and continuously evolving.
