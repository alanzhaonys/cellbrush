<?php

namespace AlanZhao\Cellbrush\Tests;

use AlanZhao\Cellbrush\Html\Multiple\StaticAttributesMap;

class AttributesMapTest extends \PHPUnit_Framework_TestCase {

  function testMerge() {
    $a = StaticAttributesMap::create(
      ['c0' => ['id' => 'c0id']],
      ['c0' => ['c0class' => 'c0class']]);
    $b = StaticAttributesMap::create(
      ['c0' => ['id' => 'c0id_b']],
      ['c0' => ['c0class_b' => 'c0class_b']]);
  }
}
