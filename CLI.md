# Nth CLI Documentation

The **Nth CLI** provides a convenient way to manage your application's routes and Blade view caching. Below is the list of available commands and their usage.

---

## General Usage

Run the CLI commands by executing:

```
php cli/nth [command]
```

For example:

```
php cli/nth routes:list
```

---

## Available Commands

### routes:list

**Description:**  
Lists all the routes in your application, including their method, URI, and the file path where they are defined.  

When you click on a route in the terminal (if supported by your terminal), it will open the file at the specified line number in VSCode.

**Usage:**  

```
php cli/nth routes:list
```

**Example Output:**  

```
GET     /home                   /path/to/Controller.php:15
POST    /login                  /path/to/AuthController.php:23
GET     /dashboard              /path/to/DashboardController.php:8
```

---

### views:clearcache

**Description:**  
Clears all cached Blade views from the `cache/compiled` directory. Use this command when you want to reset your view cache.

**Usage:**  

```
php cli/nth views:clearcache
```

---

### views:precompile

**Description:**  
Precompiles all Blade views in the `app/Views` directory into the `cache/compiled` directory. This helps improve performance by avoiding on-the-fly compilation during runtime.

**Usage:**  

```
php cli/nth views:precompile
```

