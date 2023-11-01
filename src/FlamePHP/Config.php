<?php

namespace Bndrmrtn\FlamephpEngine\FlamePHP;

class Config {
     public readonly string $storeDir;

     public function __construct(
          public readonly array $tags = array(
               0 => '{{', // The parser start tag
               1 => '}}', // The parser end tag
               2 => '*', // Disable the echo mode
               3 => '!', // Attention mode, htmlspecialchars auto applied
               4 => '--', // Create a comment with php
          ),
          public readonly string $fileExtension = '.flame.{ext}',
          public array $replace = array(
               '@else:' => 'else:',
          )
     ) {}
}