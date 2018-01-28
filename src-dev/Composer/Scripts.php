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
   * Composer event callback.
   */
  public static function postInstallCmd(Event $event) {
    static::init($event);
    GitHooks::deploy($event);
    static::phpcsConfigSet();
    static::prepareDrushSut();

    return 0;
  }

  /**
   * Composer event callback.
   */
  public static function postUpdateCmd(Event $event) {
    GitHooks::deploy($event);

    return 0;
  }

  protected static function init(Event $event) {
    static::$event = $event;
    static::$processCallbackWrapper = function (string $type, string $buffer) {
      static::processCallback($type, $buffer);
    };
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

    $cmd = vsprintf($cmdPattern, $cmdArgs);
    $process = new Process($cmd);
    $process->run(static::$processCallbackWrapper);
  }

  protected static function prepareDrushSut(): void {
    if (!static::$event->isDevMode()) {
      return;
    }

    $workingDirectory = 'src-dev/fixtures/drush-sut';
    $cmd = 'composer install';

    static::$event->getIO()->write("Run '$cmd' in '$workingDirectory'");
    $process = new Process($cmd);
    $process->setWorkingDirectory($workingDirectory);
    $process->run(static::$processCallbackWrapper);
  }

  protected static function processCallback(string $type, string $buffer): void {
    if ($type === Process::OUT) {
      static::$event->getIO()->write($buffer);
    }
    else {
      static::$event->getIO()->writeError($buffer);
    }
  }

}
