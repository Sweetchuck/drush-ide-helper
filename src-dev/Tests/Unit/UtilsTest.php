<?php

namespace Drupal\ide_helper\Tests\Unit;

use Drupal\ide_helper\Utils;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Drupal\ide_helper\Utils
 *
 * @group IdeHelperUnit
 */
class UtilsTest extends IdeHelperTestBase {

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
        [
          'intersect' => 2,
          'diff' => 1,
        ],
        'FooBarBase',
        'FooBar',
      ],
    ];
  }

  /**
   * @dataProvider casesNumOfWordMatches
   */
  public function testNumOfWordMatches(array $expected, string $aText, string $bText): void {
    $this->assertEquals($expected, Utils::numOfWordMatches($aText, $bText));
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
    $structure = [
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
    ];

    return [
      'cwd is the project root' => [
        'project-01',
        'project-01',
        $structure,
      ],
      'cwd is under the project root' => [
        'foo/bar/project-02',
        'foo/bar/project-02/d/e/f',
        $structure,
      ],
      'there is no .idea in the parent directories' => [
        NULL,
        'foo/bar/not-a-project',
        $structure,
      ],
    ];
  }

  /**
   * @dataProvider casesAutodetectIdeaProjectRoot
   */
  public function testAutodetectIdeaProjectRoot(?string $expected, string $cwd, array $dirStructure): void {
    $rootDir = $this->vfsRootDirFromMethod(__METHOD__);
    $vfs = vfsStream::setup($rootDir, NULL, $dirStructure);
    $rootDirUrl = $vfs->url();

    $expected = $expected ? "$rootDirUrl/$expected" : $expected;
    $cwd = "$rootDirUrl/$cwd";

    $this->assertEquals($expected, Utils::autodetectIdeaProjectRoot($cwd));
  }

  public function casesGetServiceHandlerInterface(): array {
    return [
      'Drupal\node\NodeStorage' => [
        'Drupal\node\NodeStorageInterface',
        'Drupal\node\NodeStorage',
        'Storage',
      ],
      'Drupal\node\NodeAccessControlHandler' => [
        'Drupal\node\NodeAccessControlHandlerInterface',
        'Drupal\node\NodeAccessControlHandler',
        'AccessControlHandler',
      ],
      'Drupal\node\NodeListBuilder' => [
        'Drupal\Core\Entity\EntityListBuilderInterface',
        'Drupal\node\NodeListBuilder',
        'ListBuilder',
      ],
      'Drupal\node\NodeViewBuilder' => [
        'Drupal\Core\Entity\EntityViewBuilderInterface',
        'Drupal\node\NodeViewBuilder',
        'ViewBuilder',
      ],
      'Drupal\Core\Template\Loader\FilesystemLoader' => [
        'Twig_LoaderInterface',
        'Drupal\Core\Template\Loader\FilesystemLoader',
        '',
      ],
      'Drupal\Core\Breadcrumb\BreadcrumbManager' => [
        'Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface',
        'Drupal\Core\Breadcrumb\BreadcrumbManager',
        '',
      ],
    ];
  }

  /**
   * @dataProvider casesGetServiceHandlerInterface
   */
  public function testGetServiceHandlerInterface($expected, string $fqn, string $base): void {
    $this->assertEquals($expected, Utils::getServiceHandlerInterface($fqn, $base));
  }

}
