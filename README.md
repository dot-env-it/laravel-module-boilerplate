# Laravel Module Boilerplate

[](https://www.google.com/search?q=https://github.com/dot-env-it/laravel-module-boilerplate)
[](https://opensource.org/licenses/MIT)

A high-performance modular architecture generator designed for Laravel. This package allows you to quickly scaffold entire modules (Controllers, Models, Services, etc.) within a `modules/` directory.

-----

## 🚀 Installation

This package is intended for **local development only**. Do not install it in production.

### 1\. Require the Package

Install via Composer using the `--dev` flag:

```bash
composer require dot-env-it/laravel-module-boilerplate --dev
```

### 2\. Publish Stubs (Optional)

If you wish to customize the templates used for generation, publish the stubs to your project root:

```bash
php artisan vendor:publish --tag=module-boilerplate-stubs
```

The stubs will be available in `stubs/vendor/dot-env-it/`.

-----

## 🛠 Usage

### Generate a Full Module

To scaffold a complete module structure:

```bash
php artisan module:make Blog
```

### Custom JS Path

If you use a non-standard directory for your frontend assets:

```bash
php artisan module:make Blog --js-path=Assets/scripts
```

### Available Sub-Commands

You can also generate individual components within a module:

  * `php artisan module:controller {name} --module={module}`
  * `php artisan module:model {name} --module={module}`
  * `php artisan module:service {name} --module={module}`
  * `php artisan module:request {name} --module={module}`
  * *(And 14+ other specialized commands)*

-----

## 📂 Module Structure

Generated modules follow this clean architecture:

```text
app/
├──Modules/
│  └── Blog/
│        ├── Actions/
│        ├── DataTables/
│        ├── Http/
│        │   ├── Controllers/
│        │   ├── Payloads/
│        │   ├── Requests/
│        │   └── Resources/
│        ├── Models/
│        ├── Queries/
│        └── Services/
└──resources/
   ├──custom-js-path/modules/blog
   └──views/modules/blog
```

It also creates permissions if `spatie/laravel-permission` package is installed.

-----

## 🛡 Security & Environment

This package automatically disables its commands when `APP_ENV` is set to `production`. This ensures that your file system remain protected in live environments.

## 👥 Credits

  - **dot-env-it** ([dotenvit@gmail.com](mailto:dotenvit@gmail.com))

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

-----
