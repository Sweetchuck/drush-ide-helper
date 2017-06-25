<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getStorage(0),
    map([
      'action' => \Drupal\Core\Config\Entity\ConfigEntityStorageInterface::class,
      'menu' => \Drupal\Core\Config\Entity\ConfigEntityStorageInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'action' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
      'menu' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'access_check.cron' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'access_check.db_update' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'path_processor.files' => \Drupal\Core\PathProcessor\InboundPathProcessorInterface::class,
      'system.admin_path.route_subscriber' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
      'system.breadcrumb.default' => \Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface::class,
      'system.config_cache_tag' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
      'system.config_subscriber' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
      'system.manager' => \Drupal\system\SystemManager::class,
      'theme.negotiator.system.batch' => \Drupal\Core\Theme\ThemeNegotiatorInterface::class,
      'theme.negotiator.system.db_update' => \Drupal\Core\Theme\ThemeNegotiatorInterface::class,
    ])
  );

}