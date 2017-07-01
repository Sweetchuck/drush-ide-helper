<?php

namespace Drupal\ide_helper\Tests\Unit;

use Drupal\ide_helper\Utils;

/**
 * @covers \Drupal\ide_helper\Utils
 *
 * @group IdeHelperUnit
 */
class UtilsTest extends \PHPUnit_Framework_TestCase {

  public function casesExtensionNameFromFqn(): array {
    return [
      'basic' => [
        'b',
        'A\b\C',
      ],
      'global' => [
        '_global',
        '\SplString',
      ],
    ];
  }

  /**
   * @dataProvider casesExtensionNameFromFqn
   */
  public function testExtensionNameFromFqn(string $expected, string $fqn): void {
    $this->assertEquals($expected, Utils::extensionNameFromFqn($fqn));
  }

  public function casesClassNameFromFqn(): array {
    return [
      'basic' => [
        'C',
        '\A\B\C',
      ],
      'global' => [
        'SplString',
        '\SplString',
      ],
    ];
  }

  /**
   * @dataProvider casesClassNameFromFqn
   */
  public function testClassNameFromFqn(string $expected, string $fqn): void {
    $this->assertEquals($expected, Utils::classNameFromFqn($fqn));
  }

  public function casesNumOfWordMatches(): array {
    return [
      'basic' => [
        2,
        'FooBar',
        'FooBarInterface',
      ],
    ];
  }

  /**
   * @dataProvider casesNumOfWordMatches
   */
  public function testNumOfWordMatches(int $expected, string $a, string $b): void {
    $this->assertEquals($expected, Utils::numOfWordMatches($a, $b));
  }

  public function casesServiceClass(): array {
    return [
      'empty' => [
        '',
        [
          'parent' => 'none',
        ],
        [],
      ],
      'parent' => [
        '\Foo\Bar',
        [
          'parent' => 's1',
        ],
        [
          's1' => [
            'parent' => 's2',
          ],
          's2' => [
            'class' => '\Foo\Bar',
          ],
        ],
      ],
    ];
  }

  /**
   * @dataProvider casesServiceClass
   */
  public function testServiceClass(string $expected, array $service, array $allService): void {
    $this->assertEquals($expected, Utils::serviceClass($service, $allService));
  }

}
