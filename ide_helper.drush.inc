<?php

/**
 * @file
 * Drush command hooks and callbacks.
 */

use Drupal\ide_helper\CommandHandlers\PhpStormMeta;
use Drupal\ide_helper\Utils;

/**
 * Implements hook_drush_command().
 */
function ide_helper_drush_command() {
  $commands = [];

  $commands['ide-helper-phpstorm-meta'] = [
    'aliases' => ['ihpm'],
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'description' => dt('Some helpful description.'),
    'required-arguments' => TRUE,
    'arguments' => [],
    'options' => [
      'output-dir' => [
        'description' => dt('Output directory name, without ".phpstorm.meta.php"'),
        'example-value' => '/foo/bar',
      ],
    ],
    'allow-additional-options' => FALSE,
    'examples' => [
      'drush ide-helper-foo' => dt('Description of what this command will do.'),
    ],
    'core' => ['8|8+'],
  ];

  return $commands;
}

/**
 * Implements drush_COMMAND_init().
 */
function drush_ide_helper_phpstorm_meta_init() {
  if (!class_exists('\Drupal\ide_helper\PhpStormMetaFileRenderer')) {
    $classes = [
      'CommandHandlers/PhpStormMeta',
      'PhpStormMetaFileRenderer',
      'Utils',
    ];
    foreach ($classes as $class) {
      require_once __DIR__ . "/src/$class.php";
    }
  }
}

/**
 * Implements drush_COMMAND_validate().
 */
function drush_ide_helper_phpstorm_meta_validate() {
  $outputDir = drush_get_option('output-dir');
  if (!$outputDir) {
    $outputDir = Utils::autodetectIdeaProjectRoot(getcwd());
    if (!$outputDir) {
      drush_set_error(
        'IDE_HELPER_OUTPUT_DIR_MISSING',
        dt('The output directory cannot be detected automatically.')
      );

      return;
    }

    drush_set_option('output-dir', $outputDir, 'ide_helper:default_values');
  }

  if (file_exists($outputDir) && !is_dir($outputDir)) {
    drush_set_error(
      'IDE_HELPER_OUTPUT_DIR_IS_NOT_DIR',
      dt(
        "The given path '@path' cannot be used as output directory, because it is exists but not a directory.",
        [
          '@path' => $outputDir,
        ]
      )
    );

    return;
  }
}

/**
 * Implements drush_COMMAND().
 */
function drush_ide_helper_phpstorm_meta() {
  $outputDir = drush_get_option('output-dir') ?: drush_get_option('output-dir', NULL, 'ide_helper:default_values');

  $handler = new PhpStormMeta();
  $handler
    ->setOutputDir($outputDir)
    ->execute();
}
