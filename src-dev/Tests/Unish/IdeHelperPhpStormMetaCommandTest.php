<?php

namespace Drupal\ide_helper\Tests\Unish;

use Symfony\Component\Filesystem\Filesystem;
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
    $fs = new Filesystem();
    $fs->remove(Path::join($this->webroot(), '.phpstorm.meta.php'));
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

    $this->caseFailOutputDirIsFile();
    $this->caseSuccessOutputDirExplicit();
    $this->caseSuccessOutputDirDetection();
  }

  /**
   * @todo Activate this test.
   *
   * @return $this
   */
  protected function caseFailOutputDirDetection() {
    $this->drush(
      'ide-helper:phpstorm-meta',
      [
        '--outputDir=',
      ],
      $this->options(),
      NULL,
      '/tmp',
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
  protected function caseFailOutputDirIsFile() {
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
  protected function caseSuccessOutputDirExplicit() {
    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options() + ['outputDir' => '.']
    );

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
      $filePath = Path::join($this->webroot(), '.phpstorm.meta.php', $fileName);
      $this->assertFileExists($filePath);
      $this->assertStringEqualsFile(
        "{$this->ideHelperDir}/src-dev/expected/Unish/PhpStormMeta/$fileName",
        file_get_contents($filePath),
        "File '$fileName'"
      );
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function caseSuccessOutputDirDetection() {
    $this->cleanDirPhpStormMetaPhp();
    $this->mkdir(Path::join($this->webroot(), '.idea'));
    $this->drush(
      'ide-helper:phpstorm-meta',
      [],
      $this->options()
    );

    return $this;
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
