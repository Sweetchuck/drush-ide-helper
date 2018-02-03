<?php

namespace Drupal\ide_helper\Composer;

use Composer\Script\Event;
use Symfony\Component\Process\Process;
use Sweetchuck\GitHooks\Composer\Scripts as GitHooks;

class Scripts {

  /**
   * Current event.
   *
   * @var \Composer\Script\Event
   */
  protected static $event;

  /**
   * CLI process callback.
   *
   * @var \Closure
   */
  protected static $processCallbackWrapper;

  /**
   * @var string
   */
  protected static $drushSutRoot = 'src-dev/fixtures/drush-sut';

  /**
   * Composer event callback.
   */
  public static function postInstallCmd(Event $event): int {
    static::init($event);
    static::gitHooksDeploy();
    static::phpcsConfigSet();
    static::prepareDrushSut();

    return 0;
  }

  /**
   * Composer event callback.
   */
  public static function postUpdateCmd(Event $event): int {
    static::init($event);
    static::gitHooksDeploy();

    return 0;
  }

  protected static function init(Event $event) {
    static::$event = $event;
    if (!static::$processCallbackWrapper) {
      static::$processCallbackWrapper = function (string $type, string $buffer) {
        static::processCallback($type, $buffer);
      };
    }
  }

  protected static function gitHooksDeploy(): void {
    if (!static::$event->isDevMode()) {
      return;
    }

    GitHooks::deploy(static::$event);
  }

  protected static function phpcsConfigSet(): void {
    if (!static::$event->isDevMode()) {
      return;
    }

    /** @var \Composer\Config $config */
    $config = static::$event->getComposer()->getConfig();
    $cmdPattern = '%s --config-set installed_paths %s';
    $cmdArgs = [
      escapeshellcmd($config->get('bin-dir') . '/phpcs'),
      escapeshellarg($config->get('vendor-dir') . '/drupal/coder/coder_sniffer'),
    ];

    static::processRun('.', vsprintf($cmdPattern, $cmdArgs));
  }

  protected static function prepareDrushSut(): void {
    if (!static::$event->isDevMode()) {
      return;
    }

    static::prepareDrushSutComposerInstall();
    static::prepareDrushSutDrupalScaffold();
  }

  protected static function prepareDrushSutComposerInstall(): void {
    static::processRun(static::$drushSutRoot, 'composer install --no-progress');
  }

  protected static function prepareDrushSutDrupalScaffold(): void {
    static::processRun(static::$drushSutRoot, 'composer run drupal-scaffold');
  }

  protected static function processRun(string $workingDirectory, string $command): Process {
    static::$event->getIO()->write("Run '$command' in '$workingDirectory'");
    $process = new Process($command);
    $process->setWorkingDirectory($workingDirectory);
    $process->run(static::$processCallbackWrapper);

    return $process;
  }

  protected static function processCallback(string $type, string $buffer): void {
    if ($type === Process::OUT) {
      static::$event->getIO()->write($buffer);
    }
    elseif ($type === Process::ERR) {
      static::$event->getIO()->writeError($buffer);
    }
  }

}
