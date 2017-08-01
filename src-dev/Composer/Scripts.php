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

  protected static function phpcsConfigSet(): bool {
    $cmdPattern = '%s --config-set installed_paths %s';
    /** @var \Composer\Config $config */
    $config = static::$event->getComposer()->getConfig();
    $cmdArgs = [
      escapeshellcmd($config->get('bin-dir') . '/phpcs'),
      escapeshellarg($config->get('vendor-dir') . '/drupal/coder/coder_sniffer'),
    ];

    $process = new Process(vsprintf($cmdPattern, $cmdArgs));
    $process->run(static::$processCallbackWrapper);

    return $process->getExitCode() === 0;
  }

  protected static function processCallback(string $type, string $buffer) {
    if ($type === Process::OUT) {
      static::$event->getIO()->write($buffer);
    }
    else {
      static::$event->getIO()->writeError($buffer);
    }
  }

}
