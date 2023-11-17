<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# LaraCrud

LaraCrud is a Laravel package designed to simplify the process of generating complete CRUD (Create, Read, Update, Delete) operations in your Laravel applications. With LaraCrud, you can quickly scaffold the necessary files for a CRUD module, allowing you to focus on building your application's unique features.

## Installation

You can install LaraCrud via Composer. Open a terminal window and run the following command:

```composer require ishakib/lara-crud```

After installation, publish the package assets:

Go to config/app.php and find providers array
At the bottom providers array add two lines given below

```
laracrud\LaraCrudServiceProvider::class,
//App\Providers\RepositoryRegisterProvider::class
```

```php artisan vendor:publish --tag=laracrud-publish```

Again Go to config/app.php and find providers array
At the bottom providers array add two lines given below
```
laracrud\LaraCrudServiceProvider::class,
App\Providers\RepositoryRegisterProvider::class
```
```php artisan vendor:publish --tag=laracrud-publish```


## Usage
Once installed, you can use the LaraCrud Artisan command to generate a complete CRUD module. Here's a simple example:

```php artisan lara:crud```

##Features
Generate View Files: Automatically create views for your CRUD operations.
Generate Controller: Generate a controller with CRUD methods.
Generate Model: Create an Eloquent model for your resource.
Generate Validation Files: Automatically generate validation files for form requests.
Generate Migration Files: Create database migration files for your resource.
Generate Routes: Add CRUD routes to your web.php file.

## Configuration
Customize LaraCrud by editing the configuration file located at config/lara-crud.php. Adjust the settings according to your project requirements.

## License
This package is open-sourced software licensed under the MIT license.

Author
A K M Shakib Hossain
Contributions are welcome! If you find any issues or have suggestions for improvement, feel free to open an issue or create a pull request.

Happy coding!
