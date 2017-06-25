<?php

namespace Drupal\ide_helper\Tests;

use Unish\CommandUnishTestCase;
use Webmozart\PathUtil\Path;

/**
 * Class IdeHelperPhpStormMetaCommandTest.
 *
 * @package Drupal\ide_helper\Tests
 */
class IdeHelperPhpStormMetaCommandTest extends CommandUnishTestCase {

  protected $ideHelperDir = '';

  /**
   * {@inheritdoc}
   */
  public function __construct($name = NULL, array $data = [], $dataName = '') {
    parent::__construct($name, $data, $dataName);

    $this->ideHelperDir = Path::canonicalize(Path::join(__DIR__, '..', '..'));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->setUpDrupal(1, TRUE);
    $this->drush(
      'pm-enable',
      ['aggregator'],
      $this->options() + ['yes' => NULL]
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {
    parent::tearDown();

    $this->cleanDirPhpStormMetaPhp();
  }

  /**
   * Clean .phpstorm.meta.php directory.
   */
  protected function cleanDirPhpStormMetaPhp() {
    unish_file_delete_recursive(Path::join($this->webroot(), '.phpstorm.meta.php'));
  }

  /**
   * All in one test.
   */
  public function testAllInOne() {
    $this->drush(
      'ide-helper-phpstorm-meta',
      [],
      $this->options(),
      NULL,
      NULL,
      1
    );
    $this->assertContains('The output directory cannot be detected automatically.', $this->getErrorOutput());

    $this->drush(
      'ide-helper-phpstorm-meta',
      [],
      $this->options() + ['output-dir' => 'index.php'],
      NULL,
      NULL,
      1
    );
    $this->assertContains(
      "The given path 'index.php' cannot be used as output directory, because it is exists but not a directory.",
      $this->getErrorOutput()
    );

    $this->drush(
      'ide-helper-phpstorm-meta',
      [],
      $this->options() + ['output-dir' => '.']
    );

    $fileNames = [
      'drupal.aggregator.php',
      'drupal.core.php',
      'drupal.dynamic_page_cache.php',
      'drupal.file.php',
      'drupal.page_cache.php',
      'drupal.system.php',
      'drupal.update.php',
      'drupal.user.php',
    ];
    foreach ($fileNames as $fileName) {
      $filePath = Path::join($this->webroot(), '.phpstorm.meta.php', $fileName);
      $this->assertFileExists($filePath);
      $this->assertStringEqualsFile(
        "{$this->ideHelperDir}/src-dev/expected/$fileName",
        file_get_contents($filePath),
        "File '$fileName'"
      );
    }

    $this->cleanDirPhpStormMetaPhp();
    $this->mkdir(Path::join($this->webroot(), '.idea'));
    $this->drush(
      'ide-helper-phpstorm-meta',
      [],
      $this->options()
    );
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
      'uri' => key($this->getSites()),
      'yes' => NULL,
      'include' => $this->ideHelperDir,
    ];
  }

}
