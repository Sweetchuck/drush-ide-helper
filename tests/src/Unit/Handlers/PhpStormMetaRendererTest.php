<?php

declare(strict_types = 1);

namespace Drupal\Tests\ide_helper\Unit\Handlers;

use Drupal\ide_helper\Handlers\PhpStormMetaRenderer;
use Drupal\Tests\ide_helper\Unit\IdeHelperTestBase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

/**
 * @covers \Drupal\ide_helper\Handlers\PhpStormMetaRenderer
 *
 * @group ide_helper
 */
class PhpStormMetaRendererTest extends IdeHelperTestBase {

  protected string $ideHelperDir = '.';

  public function __construct($name = NULL, array $data = [], $dataName = '') {
    parent::__construct($name, $data, $dataName);

    $this->ideHelperDir = Path::join(__DIR__, '..', '..', '..', '..');
  }

  public function testIsEmpty(): void {
    $renderer = new PhpStormMetaRenderer();
    $this->assertTrue($renderer->isEmpty());
    $renderer->addOverride('\Foo\Bar', 'baz', ['ab' => '\AbInterface']);
    $this->assertFalse($renderer->isEmpty());
  }

  public function casesRender(): array {
    $dir = "{$this->ideHelperDir}/tests/expected/Unit/PhpStormMetaFileRenderer";
    $files = (new Finder())
      ->in($dir)
      ->name('*.yml');

    $cases = [];
    /** @var \Symfony\Component\Finder\SplFileInfo $ymlFile */
    foreach ($files as $ymlFile) {
      $phpFileName = preg_replace('/\.yml$/', '.php', $ymlFile->getPathname());
      $cases[$ymlFile->getBasename('.yml')] = [
        file_get_contents($phpFileName),
        Yaml::parse($ymlFile->getContents()),
      ];
    }

    return $cases;
  }

  /**
   * @dataProvider casesRender
   */
  public function testRender(string $expected, array $overrides): void {
    $renderer = new PhpStormMetaRenderer();
    foreach ($overrides as $override) {
      $renderer->addOverride(...$override);
    }

    $this->assertEquals($expected, $renderer->render());
  }

}
