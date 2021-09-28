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
      'update.root' => 'string',
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
      'update.root' => 'string',
      'update.root.factory' => \Drupal\update\UpdateRootFactory::class,
    ])
  );

  override(
    \Drupal\Core\Url::fromRoute(0),
    map([
      'update.confirmation_page' => \Drupal\Core\Url::class,
      'update.manual_status' => \Drupal\Core\Url::class,
      'update.module_install' => \Drupal\Core\Url::class,
      'update.module_update' => \Drupal\Core\Url::class,
      'update.report_install' => \Drupal\Core\Url::class,
      'update.report_update' => \Drupal\Core\Url::class,
      'update.settings' => \Drupal\Core\Url::class,
      'update.status' => \Drupal\Core\Url::class,
      'update.theme_install' => \Drupal\Core\Url::class,
      'update.theme_update' => \Drupal\Core\Url::class,
    ])
  );

  override(
    \Drupal\Core\Link::createFromRoute(1),
    map([
      'update.confirmation_page' => \Drupal\Core\Link::class,
      'update.manual_status' => \Drupal\Core\Link::class,
      'update.module_install' => \Drupal\Core\Link::class,
      'update.module_update' => \Drupal\Core\Link::class,
      'update.report_install' => \Drupal\Core\Link::class,
      'update.report_update' => \Drupal\Core\Link::class,
      'update.settings' => \Drupal\Core\Link::class,
      'update.status' => \Drupal\Core\Link::class,
      'update.theme_install' => \Drupal\Core\Link::class,
      'update.theme_update' => \Drupal\Core\Link::class,
    ])
  );

}
