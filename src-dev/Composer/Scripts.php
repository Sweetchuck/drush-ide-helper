<?php

namespace Drupal\ide_helper\Composer;

use Cheppers\GitHooks\Main as GitHooksMain;
use Composer\Script\Event;
use Symfony\Component\Process\Process;

/**
 * Class Scripts.
 *
 * @package Drupal\phpstorm\Composer
 */
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
    GitHooksMain::deploy($event);
    static::phpcsConfigSet();

    return 0;
  }

  /**
   * Composer event callback.
   */
  public static function postUpdateCmd(Event $event) {
    GitHooksMain::deploy($event);

    return 0;
  }

  /**
   * Initialize.
   */
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
