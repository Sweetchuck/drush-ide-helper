<?php

namespace Drupal\ide_helper\Composer;

use Cheppers\GitHooks\Main as GitHooksMain;
use Composer\Script\Event;

/**
 * Class Scripts.
 *
 * @package Drupal\phpstorm\Composer
 */
class Scripts {

  /**
   * Composer event callback.
   */
  public static function postInstallCmd(Event $event) {
    GitHooksMain::deploy($event);

    return 0;
  }

  /**
   * Composer event callback.
   */
  public static function postUpdateCmd(Event $event) {
    GitHooksMain::deploy($event);

    return 0;
  }

}
