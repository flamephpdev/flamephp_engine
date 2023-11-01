<?php

namespace Bndrmrtn\FlamephpEngine\FlamePHP;

use Bndrmrtn\FlamephpEngine\FlamePHP;

class FileData {

     public static function get(string $file, string $views_dir, string $view__autorender_file, string $store_dir): array {
          $renderFile = false;
          $v_p = $views_dir;
          $atp = '';

          $file_ext = 'php';
          if(str_contains($file, '.')) {
               $ext = explode('.', $file);
               $file_ext = $ext[array_key_last($ext)];
               unset($ext[array_key_last($ext)]);
               $file = implode('.', $ext);
          }
          $varf = str_replace('{ext}', $file_ext, $view__autorender_file);
          if(file_exists($v_p . FlamePHP::$methods->strStartSlash($file) . $varf)) {
               $view_file = $v_p . FlamePHP::$methods->strStartSlash($file) . $varf;
               $cached_file = $store_dir . $atp . FlamePHP::$methods->strStartSlash($file) . $varf;
               $renderFile = true;
               
          } else {
               $view_file = $v_p . FlamePHP::$methods->strStartSlash($file) . '.' . $file_ext;
               $cached_file = $store_dir . $atp . FlamePHP::$methods->strStartSlash($file) . '.' . $file_ext;
          }
          if($file_ext !== 'php' && $renderFile) {
               $cached_file .= '.php';
          }
          return [
               'renderFile' => $renderFile,
               'view_file' => $view_file,
               'cached_file' => $cached_file,
               'file_ext' => $file_ext,
          ];
     }

}