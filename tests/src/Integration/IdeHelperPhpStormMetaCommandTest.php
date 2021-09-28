<?php

declare(strict_types = 1);

namespace Drupal\Tests\ide_helper\Integration;

use Sweetchuck\Utils\VersionNumber;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\PathUtil\Path;

/**
 * @group ide_helper
 */
class IdeHelperPhpStormMetaCommandTest extends IntegrationTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this
      ->deleteTestArtifacts()
      ->setUpPrepare();
  }

  protected function setUpPrepare() {
    $options = $this->options();

    $this->drush(
      'pm:enable',
      ['aggregator', 'taxonomy'],
      $options
    );

    $resourcesDir = Path::join($this->getSelfRootDir(), 'tests', 'resources');

    $this->drush(
      'php-script',
      ['ide_helper_tags.setup'],
      ['script-path' => $resourcesDir] + $options,
    );

    $this->drush('cache-rebuild', [], $options);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this
      ->deleteTestArtifacts()
      ->tearDownRestore();

    parent::tearDown();
  }

  protected function tearDownRestore() {
    $resourcesDir = Path::join($this->getSelfRootDir(), 'tests', 'resources');
    $options = $this->options();

    $this->drush(
      'php-script',
      ['ide_helper_tags.teardown'],
      ['script-path' => $resourcesDir] + $options,
    );

    return $this;
  }

  /**
   * Clean .phpstorm.meta.php directory.
   *
   * @return $this
   */
  protected function deleteTestArtifacts() {
    $drupalRoot = $this->getDrupalRoot();

    $this->fs->remove("$drupalRoot/.idea");
    $this->fs->remove("$drupalRoot/.phpstorm.meta.php");
    $this->fs->remove("$drupalRoot/../.idea");
    $this->fs->remove("$drupalRoot/../.phpstorm.meta.php");

    return $this;
  }

  public function testFailOutputDirNotExists() {
    $nonExistsDir = uniqid('non-exists');
    $this->drush(
      'ide-helper:phpstorm-meta',
      [
        "--outputDir=$nonExistsDir",
      ],
      $this->options(),
      NULL,
      NULL,
      2
    );

    static::assertStringContainsString(
      "The given path '$nonExistsDir' is not exists.",
      $this->getErrorOutput(),
      'Error message found: The given path is not exists.'
    );
  }

  public function testFailOutputDirIsFile() {
    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options() + ['outputDir' => 'index.php'],
      NULL,
      NULL,
      3,
      NULL,
      ['COLUMNS' => 120],
    );

    static::assertStringContainsString(
      "The given path 'index.php' cannot be used as output directory, because it is exists but not a directory.",
      $this->getErrorOutput(),
      "Error message found: The given path 'index.php' cannot be used ..."
    );
  }

  public function testFailOutputDirDetection() {
    if (is_dir("{$this->ideHelperDir}/.idea")) {
      $this->markTestSkipped('Output directory detection is skipped on local environment.');
    }

    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options(),
      NULL,
      NULL,
      1
    );

    static::assertStringContainsString(
      'The output directory cannot be detected automatically.',
      $this->getErrorOutput(),
      'Error message found: The output directory cannot be detected ...'
    );
  }

  public function testSuccessOutputDirExplicit() {
    $drupalRoot = $this->getDrupalRoot();

    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options() + ['outputDir' => '..']
    );

    $this->assertPhpStormMetaPhpFiles("$drupalRoot/..");
  }

  public function testSuccessOutputDirDetection() {
    $drupalRoot = $this->getDrupalRoot();
    $ideaDir = Path::join($drupalRoot, '..', '.idea');
    $this->fs->mkdir($ideaDir);

    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options(),
      NULL,
      $drupalRoot,
    );

    static::assertPhpStormMetaPhpFiles("$drupalRoot/..");
  }

  protected function assertPhpStormMetaPhpFiles(string $projectRoot) {
    $fileNames = [
      'drupal.aggregator',
      'drupal.core',
      'drupal.dynamic_page_cache',
      'drupal.field.fields',
      'drupal.file',
      'drupal.page_cache',
      'drupal.system',
      'drupal.taxonomy',
      'drupal.update',
      'drupal.user',
    ];

    $drupalVersion = $this->getDrupalVersion();
    $expectedDir = "{$this->ideHelperDir}/tests/expected/Integration/PhpStormMeta";
    foreach ($fileNames as $fileName) {
      $actualFilePath = "$projectRoot/.phpstorm.meta.php/$fileName.php";
      static::assertFileExists($actualFilePath);

      $expectedFilePath = $this->getExpectedFileName($expectedDir, $fileName, $drupalVersion);
      static::assertStringEqualsFile(
        $expectedFilePath,
        file_get_contents($actualFilePath),
        "File '$fileName'"
      );
    }
  }

  protected function getExpectedFileName(
    string $baseDir,
    string $baseName,
    VersionNumber $drupalVersion
  ): string {
    $maximum = "$baseName." . $drupalVersion->format(VersionNumber::FORMAT_MA2MI2) . '.php';

    $files = (new Finder())
      ->in($baseDir)
      ->name('/^' . preg_quote($baseName) . '\.\d+\.php$/')
      ->sortByName(TRUE)
      ->reverseSorting()
      ->filter(function (SplFileInfo $file) use ($maximum): bool {
        return $file->getFilename() <= $maximum;
      })
      ->getIterator();

    $files->rewind();

    /** @var \Symfony\Component\Finder\SplFileInfo $first */
    $first = $files->current();

    return $first->getPathname();
  }

}
