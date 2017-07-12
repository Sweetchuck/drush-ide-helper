<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'access_check.update.manager_access' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'update.fetcher' => \Drupal\update\UpdateFetcherInterface::class,
      'update.manager' => \Drupal\update\UpdateManagerInterface::class,
      'update.processor' => \Drupal\update\UpdateProcessorInterface::class,
      'update.root' => \SplString::class,
      'update.root.factory' => \Drupal\update\UpdateRootFactory::class,
    ])
  );

  override(
    \Drupal::service(0),
    map([
      'access_check.update.manager_access' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'update.fetcher' => \Drupal\update\UpdateFetcherInterface::class,
      'update.manager' => \Drupal\update\UpdateManagerInterface::class,
      'update.processor' => \Drupal\update\UpdateProcessorInterface::class,
      'update.root' => \SplString::class,
      'update.root.factory' => \Drupal\update\UpdateRootFactory::class,
    ])
  );

}
