<?php

namespace Bndrmrtn\FlamephpEngine\FlamePHP;

use Bndrmrtn\FlamephpEngine\Exception\FlameException;
use Bndrmrtn\FlamephpEngine\FlamePHP;

class Generator {
     
     public static function generateFromFile(string $file, string $viewsDirectory) {
          // Render start time
          $startTime = microtime(true);
          $storeDir = FlamePHP::$methods->endStartSlash(FlamePHP::$class->cacheDirectory) . '/views';

          $fileData = FileData::get(
               $file,
               $viewsDirectory,
               FlamePHP::$class->config->fileExtension,
               $storeDir
          );

          // is parser enabled
          $renderFile = $fileData['renderFile'];
          // the file path
          $view_file = $fileData['view_file'];
          // the cached file path
          $cached_file = $fileData['cached_file'];
          // the file extension
          $file_ext = $fileData['file_ext'];

          if(!file_exists($cached_file) || (FlamePHP::$class->useDevelopmentMode)) {
               if(!file_exists($view_file)){
                    throw new FlameException('Trying to import a non-existing file (' . $file . ')');
               }

               // get the content of the file
               $view_data = file_get_contents($view_file);

               if(!FlamePHP::$methods->createPath(dirname($cached_file))) throw new FlameException('Failed to create directory: ' . dirname($cached_file));

               $hash = NULL;

               $is_static = false;

               if($renderFile) {
                    $st = '@static';
                    if(str_starts_with($view_data, $st)){
                         $is_static = true;
                         $view_data = substr($view_data, strlen($st));
                         $nl = "\n";
                         if(str_starts_with($view_data, $nl)) $view_data = substr($view_data, strlen($nl));
                    }

                    // while the file has an extend tag
                    while(str_starts_with($view_data, '@extends(')) {
                         $view_data = FlameExtend::extended(
                              $view_data,
                              $viewsDirectory,
                              FlamePHP::$class->config->fileExtension,
                              $storeDir
                         );
                    }

                    $hash = hash('md5', $view_data);
                    $checkHash = new HashCache($hash);
                    if($checkHash->isValid()) return $checkHash->getFile();

                    $view_data_real = $view_data;

                    $ignore = new FlameIgnores(
                         '#flame-engine.ignore:start',
                         '#flame-engine.ignore:end',
                         '#flame-engine.ignore:next-line'
                    );

                    $view_data = $ignore->createAndIgnoreHTML($view_data);

                    $view_data = FlameParser::auto_tags($view_data, FlamePHP::$class->config->replace);
               
                    $view_data = FlameParser::inline_operators($view_data, FlamePHP::$class->config->tags);

                    // create a new flame operation parser
                    $fo = new FlameOperations;
                    // add the full source
                    $fo->addFullSource($view_data);
                    // configure
                    $fo->configureParser('@','(',')');
                    // parse
                    $fo->parseFile();
                    // get the parsed content string
                    $view_data = $fo->getParsed();

                    // now parse back the ignored content
                    $view_data = $ignore->getRealContent($view_data);

                    $view_data .= "<?php\n/*\nGenerated at: " . date('Y-m-d H:i:s') .  "\nMD5 File Hash: " . md5($view_data_real) . "\nRender Time: " . microtime(true) - $startTime . "s\nFlame Engine ALPHA v1.0\n*/\n?>";
               }

               if($is_static) {
                    ob_start();
                    eval('?>' . $view_data);
                    $view_data = ob_get_contents();
                    ob_end_clean();
               }

               // save the file data
               file_put_contents($cached_file, $view_data);

               if($hash) HashCache::addFile($hash, $cached_file);
          }

          return $cached_file;
     }

     public static function generateFromString(string $text, array $variable_pack = array(), bool $eval = false, bool $cache_evald_data = false) {
          $textHash = hash('md5', $text);
          FlamePHP::$methods->createPath(FlamePHP::$methods->cache('/intimeParser'));

          $cfile = FlamePHP::$methods->cache('/intimeParser' . FlamePHP::$methods->strStartSlash($textHash . '.text-content.php'));

          $varPack = '';
          if(!empty($variable_pack)) {
               $varPack = "<?php\n";
               foreach($variable_pack as $var => $val) {
                    $varPack .= '$' . $var . '=' . var_export($val,true) . ';';
               }
               $varPack .= "\n?>";
          }

          if(file_exists($cfile)) {
               $data = require $cfile;
               $data = $varPack . $data;
               if($eval) $data = self::eval($varPack . $data, $cache_evald_data);
               return $data;
          }

          $ignore = new FlameIgnores(
               '#flame-engine.ignore:start',
               '#flame-engine.ignore:end',
               '#flame-engine.ignore:next-line'
          );
          $text = $ignore->createAndIgnoreHTML($text);

          $text = FlameParser::auto_tags($text, FlamePHP::$class->config->replace);

          $text = FlameParser::inline_operators($text, FlamePHP::$class->config->tags);
     
          // create a new flame operation parser
          $fo = new FlameOperations;
          // add the full source
          $fo->addFullSource($text);
          // configure
          $fo->configureParser('@','(',')');
          // parse
          $fo->parseFile();
          // get the parsed content string
          $text = $fo->getParsed();

          // now parse back the ignored content
          $text = $ignore->getRealContent($text);

          file_put_contents($cfile, "<?php\nreturn " . var_export($text, true) . ";");

          $data = $varPack . $text;
          if($eval) $data = self::eval($varPack . $data, $cache_evald_data);
          return $data;
     }

     // helpers
     public static function eval(string $data, bool $cache_evald_data = false) {
          if($cache_evald_data) {
               $cfile = FlamePHP::$methods->cache('/intimeParser' . FlamePHP::$methods->strStartSlash(hash('md5', $data) . '.text-data-eval.php'));
               if(file_exists($cfile)) return require $cfile;
          }
          ob_start();
          eval('?>' . $data);
          $content = ob_get_contents();
          ob_end_clean();
          if($cache_evald_data) file_put_contents($cfile, "<?php\nreturn " . var_export($content, true) . ";");
          return $content;
     }

     public static function clearCache(): bool {
          try {
               FlamePHP::$methods->deleteDir(FlamePHP::$class->cacheDirectory);
          } catch(Exception $e) {
               echo $e->getMessage();
               return false;
          }
          return true;
     }

}