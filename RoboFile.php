<?php

use League\Container\ContainerInterface;
use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Robo\Collection\CollectionBuilder;
use Sweetchuck\LintReport\Reporter\BaseReporter;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Sweetchuck\Robo\Phpcs\PhpcsTaskLoader;
use Sweetchuck\Robo\PhpMessDetector\PhpmdTaskLoader;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Webmozart\PathUtil\Path;

class RoboFile extends Tasks {

  use GitTaskLoader;
  use PhpcsTaskLoader;
  use PhpmdTaskLoader;

  /**
   * @var array
   */
  protected $composerInfo = [];

  /**
   * @var string
   */
  protected $packageVendor = '';

  /**
   * @var string
   */
  protected $packageName = '';

  /**
   * @var string
   */
  protected $binDir = 'vendor/bin';

  /**
   * @var string
   */
  protected $gitHook = '';

  /**
   * @var string
   */
  protected $envVarNamePrefix = '';

  /**
   * Allowed values: dev, ci, prod.
   *
   * @var string
   */
  protected $environmentType = '';

  /**
   * Allowed values: local, jenkins, travis, circleci.
   *
   * @var string
   */
  protected $environmentName = '';

  /**
   * RoboFile constructor.
   */
  public function __construct() {
    putenv('COMPOSER_DISABLE_XDEBUG_WARN=1');
    $this
      ->initComposerInfo()
      ->initEnvVarNamePrefix()
      ->initEnvironmentTypeAndName();
  }

  /**
   * {@inheritdoc}
   */
  public function setContainer(ContainerInterface $container) {
    if (!$container->has('lintCheckstyleReporter')) {
      BaseReporter::lintReportConfigureContainer($container);
    }

    return parent::setContainer($container);
  }

  /**
   * Git "pre-commit" hook callback.
   */
  public function githookPreCommit(): CollectionBuilder {
    $this->gitHook = 'pre-commit';

    return $this
      ->collectionBuilder()
      ->addTask($this->taskComposerValidate())
      ->addTask($this->getTaskPhpcsLint());
  }

  /**
   * Run code style checkers.
   */
  public function lint(): CollectionBuilder {
    return $this
      ->collectionBuilder()
      ->addTask($this->taskComposerValidate())
      ->addTask($this->getTaskPhpcsLint())
      ->addTask($this->getTaskPhpmdLint());
  }

  public function lintPhpcs(): CollectionBuilder {
    return $this->getTaskPhpcsLint();
  }

  public function lintPhpmd(): CollectionBuilder {
    return $this->getTaskPhpmdLint();
  }

  protected function errorOutput(): ?OutputInterface {
    $output = $this->output();

    return ($output instanceof ConsoleOutputInterface) ? $output->getErrorOutput() : $output;
  }

  /**
   * @return $this
   */
  protected function initEnvVarNamePrefix() {
    $this->envVarNamePrefix = strtoupper(str_replace('-', '_', $this->packageName));

    return $this;
  }

  /**
   * @return $this
   */
  protected function initEnvironmentTypeAndName() {
    $this->environmentType = getenv($this->getEnvVarName('environment_type'));
    $this->environmentName = getenv($this->getEnvVarName('environment_name'));

    if (!$this->environmentType) {
      if (getenv('CI') === 'true') {
        // Travis, GitLab and CircleCI.
        $this->environmentType = 'ci';
      }
      elseif (getenv('JENKINS_HOME')) {
        $this->environmentType = 'ci';
        if (!$this->environmentName) {
          $this->environmentName = 'jenkins';
        }
      }
    }

    if (!$this->environmentName && $this->environmentType === 'ci') {
      if (getenv('GITLAB_CI') === 'true') {
        $this->environmentName = 'gitlab';
      }
      elseif (getenv('TRAVIS') === 'true') {
        $this->environmentName = 'travis';
      }
      elseif (getenv('CIRCLECI') === 'true') {
        $this->environmentName = 'circleci';
      }
    }

    if (!$this->environmentType) {
      $this->environmentType = 'dev';
    }

    if (!$this->environmentName) {
      $this->environmentName = 'local';
    }

    return $this;
  }

  protected function getEnvVarName(string $name): string {
    return "{$this->envVarNamePrefix}_" . strtoupper($name);
  }

  protected function getPhpExecutable(): string {
    return getenv($this->getEnvVarName('php_executable')) ?: PHP_BINARY;
  }

  protected function getPhpdbgExecutable(): string {
    return getenv($this->getEnvVarName('phpdbg_executable')) ?: Path::join(PHP_BINDIR, 'phpdbg');
  }

  /**
   * @return $this
   */
  protected function initComposerInfo() {
    if ($this->composerInfo || !is_readable('composer.json')) {
      return $this;
    }

    $this->composerInfo = json_decode(file_get_contents('composer.json'), TRUE);
    list($this->packageVendor, $this->packageName) = explode('/', $this->composerInfo['name']);

    if (!empty($this->composerInfo['config']['bin-dir'])) {
      $this->binDir = $this->composerInfo['config']['bin-dir'];
    }

    return $this;
  }

  protected function getTaskPhpcsLint(): CollectionBuilder {
    $options = [
      'failOn' => 'warning',
      'lintReporters' => [
        'lintVerboseReporter' => NULL,
      ],
    ];

    if ($this->environmentType === 'ci' && $this->environmentName === 'jenkins') {
      $options['failOn'] = 'never';
      $options['lintReporters']['lintCheckstyleReporter'] = $this
        ->getContainer()
        ->get('lintCheckstyleReporter')
        ->setDestination('tests/_output/machine/checkstyle/phpcs.psr2.xml');
    }

    if ($this->gitHook === 'pre-commit') {
      return $this
        ->collectionBuilder()
        ->addTask($this
          ->taskPhpcsParseXml()
          ->setAssetNamePrefix('phpcsXml.'))
        ->addTask($this
          ->taskGitReadStagedFiles()
          ->setCommandOnly(TRUE)
          ->setWorkingDirectory('.')
          ->deferTaskConfiguration('setPaths', 'phpcsXml.files'))
        ->addTask($this
          ->taskPhpcsLintInput($options)
          ->deferTaskConfiguration('setFiles', 'files')
          ->deferTaskConfiguration('setIgnore', 'phpcsXml.exclude-patterns'));
    }

    return $this->taskPhpcsLintFiles($options);
  }

  protected function getTaskPhpmdLint(): TaskInterface {
    $ruleSetName = 'custom';

    $task = $this
      ->taskPhpmdLintFiles()
      ->setInputFile("./rulesets/$ruleSetName.include-pattern.txt")
      ->setRuleSetFileNames([$ruleSetName])
      ->setOutput($this->output());

    $excludeFileName = "./rulesets/$ruleSetName.exclude-pattern.txt";
    if (file_exists($excludeFileName)) {
      $task->addExcludePathsFromFile($excludeFileName);
    }

    return $task;
  }

  protected function isPhpExtensionAvailable(string $extension): bool {
    $command = sprintf('%s -m', escapeshellcmd($this->getPhpExecutable()));

    $process = new Process($command);
    $exitCode = $process->run();
    if ($exitCode !== 0) {
      throw new \RuntimeException('@todo');
    }

    return in_array($extension, explode("\n", $process->getOutput()));
  }

  protected function isPhpDbgAvailable(): bool {
    $command = sprintf('%s -qrr', escapeshellcmd($this->getPhpdbgExecutable()));

    return (new Process($command))->run() === 0;
  }

}
