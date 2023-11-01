<?php

namespace Bndrmrtn\FlamephpEngine\FlamePHP;

use Bndrmrtn\FlamephpEngine\Exception\FlameException;

class FlameMethods {
     private string $viewsDirectory;
     private string $cacheDirectory;

     public function __construct(string $viewsDirectory, string $cacheDirectory) {
          if(!$this->createPath($viewsDirectory)) throw new FlameException('Failed to create the `views` directory, please check the path and the permissions');
          if(!$this->createPath($cacheDirectory)) throw new FlameException('Failed to create the `cache` directory, please check the path and the permissions');
          $this->viewsDirectory = $viewsDirectory;
          $this->cacheDirectory = $cacheDirectory;
     }

     /**
      * @param string $path A directory path like /dir/to/anywhere
      * @return bool The directory is created or not, and writable or not
      */
     public function createPath(string $path): bool {
          if(is_dir($path)) return true;
          $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
          $return = $this->createPath($prev_path);
          return ($return && is_writable($prev_path)) ? mkdir($path) : false;
     }

     public function cache(string $file): string {
          return $this->cacheDirectory . (str_ends_with($this->cacheDirectory, '/') ? '' : '/') . (str_starts_with($file, '/') ? substr($file, 1) : $file);
     }

     public function views(string $file): string {
          return $this->viewsDirectory . (str_ends_with($this->viewsDirectory, '/') ? '' : '/') . (str_starts_with($file, '/') ? substr($file, 1) : $file);
     }

     public function strStartSlash(string $s): string {
          return str_starts_with($s, '/') ? $s : '/' . $s;
     }

     public function endStartSlash(string $s): string {
          return str_ends_with($s, '/') ? $s : $s . '/';
     }

     public function stringBetween(string $string, string $start, string $end): string {
          $string = ' ' . $string;
          $ini = strpos($string, $start);
          if ($ini == 0) return '';
          $ini += strlen($start);
          $len = strpos($string, $end, $ini) - $ini;
          return substr($string, $ini, $len);
     }

     public function removeIndex($s, $n){ 
          return substr($s,0,$n).substr($s,$n+1,strlen($s)-$n);
     }

     public function strReplaceFirst(string $search, string $replace, string $subject): string {
          $search = '/'.preg_quote($search, '/').'/';
          return preg_replace($search, $replace, $subject, 1);
     }

     public function deleteDir(string $dirPath) {
          if (!is_dir($dirPath)) {
               throw new InvalidArgumentException("$dirPath must be a directory");
          }
          if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
               $dirPath .= '/';
          }
          $files = glob($dirPath . '*', GLOB_MARK);
          foreach ($files as $file) {
               if (is_dir($file)) {
                    self::deleteDir($file);
               } else {
                    unlink($file);
               }
          }
          rmdir($dirPath);
     }
}