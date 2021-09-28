<?php

declare(strict_types = 1);

namespace Drupal\Tests\ide_helper\Integration;

use Drush\TestTraits\DrushTestTrait;
use Sweetchuck\Utils\VersionNumber;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;
use weitzman\DrupalTestTraits\ExistingSiteBase;

class IntegrationTestCase extends ExistingSiteBase {

  use DrushTestTrait;

  protected string $projectName = 'project_01';

  protected string $ideHelperDir = '';

  protected Filesystem $fs;

  protected string $defaultBaseUrl = 'http://localhost:8888';

  protected ?VersionNumber $drupalVersion = NULL;

  protected function getDrupalVersion(): VersionNumber {
    if ($this->drupalVersion === NULL) {
      $this->drush(
        'core:status',
        [],
        $this->options() + ['format' => 'list', 'fields' => 'Drupal version'],
        NULL,
        $this->getDrupalRoot(),
      );

      $this->drupalVersion = VersionNumber::createFromString(trim($this->getOutput()));
    }

    return $this->drupalVersion;
  }

  public function __construct(?string $name = NULL, array $data = [], $dataName = '') {
    $this->baseUrl = getenv('DTT_BASE_URL') ?: $this->defaultBaseUrl;
    $this->fs = new Filesystem();
    $this->ideHelperDir = Path::canonicalize(Path::join(__DIR__, '..', '..', '..'));

    parent::__construct($name, $data, $dataName);
  }

  /**
   * Common Drush command options.
   */
  protected function options(): array {
    return [
      'root' => $this->getDrupalRoot(),
      'uri' => $this->baseUrl,
      'yes' => NULL,
      'include' => $this->ideHelperDir,
      'php' => PHP_BINARY,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function convertKeyValueToFlag($key, $value): string {
    if ($value === NULL) {
      return "--$key";
    }

    $options = [];

    if (!is_iterable($value)) {
      $value = [$value];
    }

    foreach ($value as $item) {
      $options[] = sprintf('--%s=%s', $key, static::escapeshellarg($item));
    }

    return implode(' ', $options);
  }

  protected function getCommonCommandLineOptions(): array {
    return [
      'config' => [
        Path::join($this->getDrupalRoot(), '..', 'drush'),
      ],
    ];
  }

  protected function getCommonCommandLineEnvVars(): array {
    return [
      'HOME' => '/dev/null',
    ];
  }

  protected function getSelfRootDir(): string {
    return dirname(__DIR__, 3);
  }

  protected function getProjectRootDir(): string {
    return dirname($this->getDrupalRoot());
  }

  protected function getDrupalRoot(): string {
    return Path::join(
      $this->getSelfRootDir(),
      "tests/fixtures/{$this->projectName}/docroot",
    );
  }

}
