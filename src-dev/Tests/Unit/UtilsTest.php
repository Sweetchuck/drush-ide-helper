<?php

namespace Drupal\ide_helper\Tests\Unit;

use Drupal\ide_helper\Utils;
use org\bovigo\vfs\vfsStream;

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

  public function casesPrefixFqnWithBackslash(): array {
    return [
      'basic' => [
        '\Foo\Bar',
        'Foo\Bar',
      ],
      'already prefixed' => [
        '\Foo\Bar',
        '\Foo\Bar',
      ],
    ];
  }

  /**
   * @dataProvider casesPrefixFqnWithBackslash
   */
  public function testPrefixFqnWithBackslash(string $expected, string $fqn): void {
    $this->assertEquals($expected, Utils::prefixFqnWithBackslash($fqn));
  }

  public function casesSuffixFqnWithClass(): array {
    return [
      'plain' => ['A\B::class', 'A\B'],
      'already' => ['A\B::class', 'A\B::class'],
      'array' => ['A\B[]', 'A\B[]'],
    ];
  }

  /**
   * @dataProvider casesSuffixFqnWithClass
   */
  public function testSuffixFqnWithClass(string $expected, string $fqn): void {
    $this->assertEquals($expected, Utils::suffixFqnWithClass($fqn));
  }

  public function casesOverrideMapTypeHint(): array {
    return [
      'empty' => [
        '',
        [],
      ],
      'basic' => [
        '\A\B::class',
        [
          'A\B',
        ],
      ],
      'array' => [
        "'\A\B[]'",
        [
          'A\B[]',
        ],
      ],
      'multi-1' => [
        "'\A\B|\C\D'",
        [
          'A\B',
          'C\D',
        ],
      ],
      'multi-2' => [
        "'SplObject|\C\D'",
        [
          'SplObject',
          'C\D',
        ],
      ],
    ];
  }

  /**
   * @dataProvider casesOverrideMapTypeHint
   */
  public function testOverrideMapTypeHint($expected, array $types): void {
    $this->assertEquals($expected, Utils::overrideMapTypeHint($types));
  }

  public function casesAutodetectIdeaProjectRoot(): array {
    $rootDir = vfsStream::setup(__FUNCTION__);
    $rootDirUrl = vfsStream::url($rootDir->getName());

    vfsStream::create(
      [
        'project-01' => [
          'a' => [
            'b' => [
              'c' => [],
            ],
          ],
          '.idea' => [],
        ],
        'foo' => [
          'bar' => [
            'project-02' => [
              'd' => [
                'e' => [
                  'f' => [],
                ],
              ],
              '.idea' => [],
            ],
            'not-a-project' => [],
          ],
        ],
      ],
      $rootDir
    );

    return [
      'cwd is the project root' => [
        "$rootDirUrl/project-01",
        "$rootDirUrl/project-01",
      ],
      'cwd is under the project root' => [
        "$rootDirUrl/foo/bar/project-02",
        "$rootDirUrl/foo/bar/project-02/d/e/f",
      ],
      'there is no .idea in the parent directories' => [
        '',
        "$rootDirUrl/foo/bar/not-a-project",
      ],
    ];
  }

  /**
   * @dataProvider casesAutodetectIdeaProjectRoot
   */
  public function testAutodetectIdeaProjectRoot(string $expected, string $cwd): void {
    $this->assertEquals($expected, Utils::autodetectIdeaProjectRoot($cwd));
  }

}
