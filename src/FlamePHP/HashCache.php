<?php

namespace Bndrmrtn\FlamephpEngine\FlamePHP;

use Bndrmrtn\FlamephpEngine\FlamePHP;

class HashCache {

     private string $hash;
     private static array $hashList = [];

     public function __construct($hash) {
          FlamePHP::$methods->createPath($cache = FlamePHP::$methods->cache('/flame-engine'));
          if(FlamePHP::$class->useDevelopmentMode) {
               if(file_exists($cache . '/hash.php')) {
                    static::$hashList = require $cache . '/hash.php';
               }
          }
          $this->hash = $hash;
     }

     public function isValid() {
          return in_array($this->hash, array_keys(self::$hashList));
     }

     public function getFile() {
          return self::$hashList[$this->hash];
     }

     public static function addFile($hash, $cache_path) {
          self::$hashList[$hash] = $cache_path;
          file_put_contents(FlamePHP::$methods->cache('/flame-engine/hash.php'), "<?php\nreturn " . var_export(self::$hashList, true) . ";");
     }

}
