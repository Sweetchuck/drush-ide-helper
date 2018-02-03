<?php

namespace Drupal\ide_helper\Tests\Unish;

use Unish\CommandUnishTestCase;
use Webmozart\PathUtil\Path;

/**
 * @group IdeHelperUnish
 */
class IdeHelperPhpStormMetaCommandTest extends CommandUnishTestCase {

  /**
   * @var string
   */
  protected $ideHelperDir = '';

  /**
   * {@inheritdoc}
   */
  public function __construct($name = NULL, array $data = [], $dataName = '') {
    parent::__construct($name, $data, $dataName);

    $this->ideHelperDir = Path::canonicalize(Path::join(__DIR__, '..', '..', '..'));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    if (!$this->getSites()) {
      $this->setUpDrupal(1, TRUE);
    }

    parent::setUp();
    $this->deleteTestArtifacts();
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {
    $this->deleteTestArtifacts();
    parent::tearDown();
  }

  /**
   * Clean .phpstorm.meta.php directory.
   */
  protected function deleteTestArtifacts() {
    $webRoot = $this->webRoot();
    $this->recursiveDelete("$webRoot/.idea");
    $this->recursiveDelete("$webRoot/.phpstorm.meta.php");
    $this->recursiveDelete("$webRoot/../.idea");
    $this->recursiveDelete("$webRoot/../.phpstorm.meta.php");
  }

  /**
   * All in one test.
   */
  public function testAllInOne() {
    $options = $this->options();

    $this->drush(
      'pm-enable',
      ['aggregator', 'taxonomy'],
      $options
    );

    $this->drush(
      'php-script',
      ['create-fields'],
      ['script-path' => __DIR__ . '/resources'] + $options
    );

    $this->drush('cache-rebuild', [], $options);

    $this->caseFailOutputDirNotExists();
    $this->caseFailOutputDirIsFile();
    $this->caseFailOutputDirDetection();
    $this->caseSuccessOutputDirExplicit();
    $this->caseSuccessOutputDirDetection();
  }

  /**
   * @return $this
   */
  protected function caseFailOutputDirNotExists() {
    $this->deleteTestArtifacts();

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
    $this->assertContains(
      "The given path '$nonExistsDir' is not exists.",
      $this->getErrorOutput(),
      'Error message found: The given path is not exists.'
    );

    return $this;
  }

  /**
   * @return $this
   */
  protected function caseFailOutputDirIsFile() {
    $this->deleteTestArtifacts();

    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options() + ['outputDir' => 'index.php'],
      NULL,
      NULL,
      3
    );

    $this->assertContains(
      "The given path 'index.php' cannot be used as output directory, because it is exists but not a directory.",
      $this->getErrorOutput(),
      "Error message found: The given path 'index.php' cannot be used ..."
    );

    return $this;
  }

  /**
   * @return $this
   */
  protected function caseFailOutputDirDetection() {
    if (is_dir("{$this->ideHelperDir}/.idea")) {
      $this->markTestSkipped('Output directory detection is skipped on local environment.');
    }

    $this->deleteTestArtifacts();

    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options(),
      NULL,
      NULL,
      1
    );

    $this->assertContains(
      'The output directory cannot be detected automatically.',
      $this->getErrorOutput(),
      'Error message found: The output directory cannot be detected ...'
    );

    return $this;
  }

  /**
   * @return $this
   */
  protected function caseSuccessOutputDirExplicit() {
    $this->deleteTestArtifacts();

    $webRoot = $this->webroot();

    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options() + ['outputDir' => '..']
    );

    $this->assertPhpStormMetaPhpFiles("$webRoot/..");

    return $this;
  }

  /**
   * @return $this
   */
  protected function caseSuccessOutputDirDetection() {
    $this->deleteTestArtifacts();

    $webRoot = $this->webroot();
    $ideaDir = Path::join($webRoot, '..', '.idea');
    $this->assertTrue($this->mkdir($ideaDir), "MKDIR '$ideaDir'");

    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options(),
      NULL,
      $webRoot
    );

    $this->assertPhpStormMetaPhpFiles("$webRoot/..");

    return $this;
  }

  protected function assertPhpStormMetaPhpFiles(string $projectRoot) {
    $fileNames = [
      'drupal.aggregator.php',
      'drupal.core.php',
      'drupal.dynamic_page_cache.php',
      'drupal.field.fields.php',
      'drupal.file.php',
      'drupal.page_cache.php',
      'drupal.system.php',
      'drupal.taxonomy.php',
      'drupal.update.php',
      'drupal.user.php',
    ];

    foreach ($fileNames as $fileName) {
      $filePath = "$projectRoot/.phpstorm.meta.php/$fileName";
      $this->assertFileExists($filePath);
      $this->assertStringEqualsFile(
        "{$this->ideHelperDir}/src-dev/expected/Unish/PhpStormMeta/$fileName",
        file_get_contents($filePath),
        "File '$fileName'"
      );
    }
  }

  /**
   * Common CLI options.
   *
   * @return array
   *   Drush command options.
   */
  protected function options() {
    return [
      'root' => $this->webroot(),
      'uri' => key(static::getSites()),
      'yes' => NULL,
      'include' => $this->ideHelperDir,
      'php' => PHP_BINARY,
    ];
  }

}
