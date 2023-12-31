# FlamePHP, a Laravel inspired view rendering engine

## How to add to a project?

Install it via composer and add it to your file.

```bash
composer require bndrmrtn/flamephp_engine:dev-main
```

```php
require_once 'vendor/autoload.php';
```

Configure or use the default config
```php
use Bndrmrtn\FlamephpEngine\FlamePHP;
use Bndrmrtn\FlamephpEngine\FlamePHP\Config;

$flame = new FlamePHP(
     viewsDirectory: '...', // The directory where you store your views
     useDevelopmentMode: true, // Your project is in development mode or not
     cacheDirectory: '...',  // The directory where you want to store your cache data
     config: new Config, // Custom config, or use the default
);
```

# How to use it?

You can use it with `*.flame.php` files with a directory named views in your root folder, or the `parseString` option that doesn't requires any file. (Except the cache files that auto generated by this tool).

## Code examples

### With the textParser option

```php
use Bndrmrtn\FlamephpEngine\FlamePHP;

$flame = new FlamePHP;

$flame->parseString('
     <h1>Hello {{ $world }}</h1>
', ['world' => 'Developer'],
);

// <h1>Hello Developer</h1>
```

### With the file option

```php
// ...
$flame->includeFile(
     'filename', // without the .flame.php extension!
     ['name' => 'John'] // add props to it
);
```

Actually you can get the path of the file's cache with the `parseFile` method, like this:
```php
// ...

$parsed_file = $flame->parseFile(
     'filename', // without the .flame.php extension!
     // No props here
);

echo $parsed_file; // output: C:\...\your_project\flamephp_engine\cache\views\filename.flame.php
```

### Demo

You can check the `/tests` folder and run the tests with PHPUnit

### Documentation

Here [FlameCore Official Views Documentation](https://flamephp.mrtn.vip/docs/v1/views/getting-started) you can find a small documentation of how it works, but I must remind you that it documents a modified version and that you should follow the default settings as described above!

## Credits

[Martin Binder](https://mrtn.vip), FullStack Web Developer, 4 years of experience with PHP, 3 with Laravel.
Currently I am learning GoLang ;)
