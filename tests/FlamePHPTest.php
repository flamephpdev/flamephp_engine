<?php

use Bndrmrtn\FlamephpEngine\FlamePHP;
use PHPUnit\Framework\TestCase;

class FlamePHPTest extends TestCase {
     public function testEchoTags() {
          $flame = new FlamePHP(viewsDirectory: __DIR__ . '/test_views/');
          $result = $flame->parseString('Hello {{ $dev }}', ['dev' => 'Martin']);
          $this->assertEquals('Hello Martin', $result);
     }

     public function testOperations() {
          $flame = new FlamePHP(viewsDirectory: __DIR__ . '/test_views/');
          $result = $flame->parseString('Hello @if(true)Martin@else:John@endif');
          $flame->tidy();
          $this->assertEquals('Hello Martin', $result);
     }

     public function testFileParsing() {
          $flame = new FlamePHP(viewsDirectory: __DIR__ . '/test_views/');
          $result = $flame->parseFile('test');
          $this->assertEquals(true, file_exists($result));
          $flame->tidy();
     }
}