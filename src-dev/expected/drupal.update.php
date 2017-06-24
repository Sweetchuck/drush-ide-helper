<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  use Drupal\Core\Routing\Access\AccessInterface;
  use Drupal\update\UpdateFetcherInterface;
  use Drupal\update\UpdateManagerInterface;
  use Drupal\update\UpdateProcessorInterface;
  use Drupal\update\UpdateRootFactory;
  use SplString;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  override(
    ContainerInterface::get(0),
    map([
      'access_check.update.manager_access' => AccessInterface::class,
      'update.fetcher' => UpdateFetcherInterface::class,
      'update.manager' => UpdateManagerInterface::class,
      'update.processor' => UpdateProcessorInterface::class,
      'update.root' => SplString::class,
      'update.root.factory' => UpdateRootFactory::class,
    ])
  );

}