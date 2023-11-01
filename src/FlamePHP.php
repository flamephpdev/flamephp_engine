<?php

namespace Bndrmrtn\FlamephpEngine;

use Bndrmrtn\FlamephpEngine\FlamePHP\Config;
use Bndrmrtn\FlamephpEngine\FlamePHP\FlameMethods;
use Bndrmrtn\FlamephpEngine\FlamePHP\Generator;

class FlamePHP {
     public static FlamePHP $class;
     public static FlameMethods $methods;
     /**
      * @param string $viewsDirectory The directory where you want to store your .flame.php files
      * @param bool $useDevelopmentMode Use the development mode or your app is ready for production (mainly for performance)
      * @param string $cacheDirectory The directory where this script can make directorys, files, and etc for caching (make sure all the permissions given)
      */
     public function __construct(
          private string $viewsDirectory = __DIR__ . '/../views/',
          public readonly bool $useDevelopmentMode = true,
          public readonly string $cacheDirectory = __DIR__ . '/../cache/',
          public readonly Config $config = new Config,
     ) {
          static::$class = $this;
          $this->config->replace['@dev'] = 'if(\Bndrmrtn\FlamephpEngine\FlamePHP::$class->useDevelopmentMode):';
          $this->config->replace['@enddev'] = 'endif';
          static::$methods = new FlameMethods(
               $this->viewsDirectory,
               $this->cacheDirectory
          );
     }

     /**
      * @param string $content The string you want to convert into plain php
      * @param array $props All the props you want to apply to the content, like the content is {{ $hi }} and the props are ['hi' => 'Hello world!']
      * @param bool $execute This means your generated output will be eval'd, and the output given back, not just the raw generated PHP code
      * @param bool $cacheAll Cache the given props too or not
      * @return string The generated raw PHP code or the eval'd, usable code
      */
     public function parseString(string $content, array $props = array(), bool $execute = true, bool $cacheAll = false): string {
          return Generator::generateFromString($content, $props, $execute, $cacheAll);
     }

     /**
      * @param string $fileName The file path without the `.flame.php` extension and without the root `$viewsDirectory` folder
      * @param array $props All the props you want to apply to the content, like the content is {{ $hi }} and the props are ['hi' => 'Hello world!']
      * @return string Returns the generated file's path from the cache directory
      */
     public function parseFile(string $fileName): string {
          if(str_ends_with($fileName, '/') || str_ends_with($fileName, '\\')) $fileName .= 'index';
          return Generator::generateFromFile($fileName, $this->viewsDirectory);
     }

     /**
      * @param string $fileName The file path without the `.flame.php` extension and without the root `$viewsDirectory` folder
      * @param array $props All the props you want to apply to the content, like the content is {{ $hi }} and the props are ['hi' => 'Hello world!']
      * @return void Includes the file with the given props and include's it
      */
     public function includeFile(string $fileName, array $props = array()): void {
          if(!empty($props)){
               foreach($props as $var => $content){
                    if(is_string($var)){
                         ${$var} = $content;
                         $_bag[$var] = $content;
                    } else {
                         $_bag[] = $content;
                    }
               }
          }
          include $this->parseFile($fileName);
     }

     /**
      * @return bool The cache folder is cleared or not
      */
     public function tidy(): bool {
          return Generator::clearCache();
     }
}