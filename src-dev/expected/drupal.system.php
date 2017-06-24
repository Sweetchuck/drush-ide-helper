<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
  use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
  use Drupal\Core\Entity\EntityAccessControlHandlerInterface;
  use Drupal\Core\Entity\EntityTypeManagerInterface;
  use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
  use Drupal\Core\Routing\Access\AccessInterface;
  use Drupal\Core\Theme\ThemeNegotiatorInterface;
  use Drupal\system\SystemManager;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Symfony\Component\EventDispatcher\EventSubscriberInterface;

  override(
    EntityTypeManagerInterface::getStorage(0),
    map([
      'action' => ConfigEntityStorageInterface::class,
      'menu' => ConfigEntityStorageInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'action' => EntityAccessControlHandlerInterface::class,
      'menu' => EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    ContainerInterface::get(0),
    map([
      'access_check.cron' => AccessInterface::class,
      'access_check.db_update' => AccessInterface::class,
      'path_processor.files' => InboundPathProcessorInterface::class,
      'system.admin_path.route_subscriber' => EventSubscriberInterface::class,
      'system.breadcrumb.default' => BreadcrumbBuilderInterface::class,
      'system.config_cache_tag' => EventSubscriberInterface::class,
      'system.config_subscriber' => EventSubscriberInterface::class,
      'system.manager' => SystemManager::class,
      'theme.negotiator.system.batch' => ThemeNegotiatorInterface::class,
      'theme.negotiator.system.db_update' => ThemeNegotiatorInterface::class,
    ])
  );

}