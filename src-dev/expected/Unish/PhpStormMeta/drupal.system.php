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

  override(
    \Drupal::service(0),
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

  override(
    \Drupal\Core\Url::fromRoute(0),
    map([
      '<current>' => \Drupal\Core\Url::class,
      '<front>' => \Drupal\Core\Url::class,
      '<nolink>' => \Drupal\Core\Url::class,
      '<none>' => \Drupal\Core\Url::class,
      'entity.date_format.collection' => \Drupal\Core\Url::class,
      'entity.date_format.delete_form' => \Drupal\Core\Url::class,
      'entity.date_format.edit_form' => \Drupal\Core\Url::class,
      'system.401' => \Drupal\Core\Url::class,
      'system.403' => \Drupal\Core\Url::class,
      'system.404' => \Drupal\Core\Url::class,
      'system.4xx' => \Drupal\Core\Url::class,
      'system.admin' => \Drupal\Core\Url::class,
      'system.admin_compact_page' => \Drupal\Core\Url::class,
      'system.admin_config' => \Drupal\Core\Url::class,
      'system.admin_config_content' => \Drupal\Core\Url::class,
      'system.admin_config_development' => \Drupal\Core\Url::class,
      'system.admin_config_media' => \Drupal\Core\Url::class,
      'system.admin_config_regional' => \Drupal\Core\Url::class,
      'system.admin_config_search' => \Drupal\Core\Url::class,
      'system.admin_config_services' => \Drupal\Core\Url::class,
      'system.admin_config_system' => \Drupal\Core\Url::class,
      'system.admin_config_ui' => \Drupal\Core\Url::class,
      'system.admin_config_workflow' => \Drupal\Core\Url::class,
      'system.admin_content' => \Drupal\Core\Url::class,
      'system.admin_index' => \Drupal\Core\Url::class,
      'system.admin_reports' => \Drupal\Core\Url::class,
      'system.admin_structure' => \Drupal\Core\Url::class,
      'system.batch_page.html' => \Drupal\Core\Url::class,
      'system.batch_page.json' => \Drupal\Core\Url::class,
      'system.cron' => \Drupal\Core\Url::class,
      'system.cron_settings' => \Drupal\Core\Url::class,
      'system.csrftoken' => \Drupal\Core\Url::class,
      'system.date_format_add' => \Drupal\Core\Url::class,
      'system.db_update' => \Drupal\Core\Url::class,
      'system.entity_autocomplete' => \Drupal\Core\Url::class,
      'system.file_system_settings' => \Drupal\Core\Url::class,
      'system.files' => \Drupal\Core\Url::class,
      'system.image_toolkit_settings' => \Drupal\Core\Url::class,
      'system.logging_settings' => \Drupal\Core\Url::class,
      'system.machine_name_transliterate' => \Drupal\Core\Url::class,
      'system.modules_list' => \Drupal\Core\Url::class,
      'system.modules_list_confirm' => \Drupal\Core\Url::class,
      'system.modules_list_experimental_confirm' => \Drupal\Core\Url::class,
      'system.modules_uninstall' => \Drupal\Core\Url::class,
      'system.modules_uninstall_confirm' => \Drupal\Core\Url::class,
      'system.performance_settings' => \Drupal\Core\Url::class,
      'system.php' => \Drupal\Core\Url::class,
      'system.prepare_modules_entity_uninstall' => \Drupal\Core\Url::class,
      'system.private_file_download' => \Drupal\Core\Url::class,
      'system.regional_settings' => \Drupal\Core\Url::class,
      'system.rss_feeds_settings' => \Drupal\Core\Url::class,
      'system.run_cron' => \Drupal\Core\Url::class,
      'system.site_information_settings' => \Drupal\Core\Url::class,
      'system.site_maintenance_mode' => \Drupal\Core\Url::class,
      'system.status' => \Drupal\Core\Url::class,
      'system.temporary' => \Drupal\Core\Url::class,
      'system.theme_install' => \Drupal\Core\Url::class,
      'system.theme_set_default' => \Drupal\Core\Url::class,
      'system.theme_settings' => \Drupal\Core\Url::class,
      'system.theme_settings_theme' => \Drupal\Core\Url::class,
      'system.theme_uninstall' => \Drupal\Core\Url::class,
      'system.themes_page' => \Drupal\Core\Url::class,
      'system.timezone' => \Drupal\Core\Url::class,
    ])
  );

  override(
    \Drupal\Core\Link::createFromRoute(1),
    map([
      '<current>' => \Drupal\Core\Link::class,
      '<front>' => \Drupal\Core\Link::class,
      '<nolink>' => \Drupal\Core\Link::class,
      '<none>' => \Drupal\Core\Link::class,
      'entity.date_format.collection' => \Drupal\Core\Link::class,
      'entity.date_format.delete_form' => \Drupal\Core\Link::class,
      'entity.date_format.edit_form' => \Drupal\Core\Link::class,
      'system.401' => \Drupal\Core\Link::class,
      'system.403' => \Drupal\Core\Link::class,
      'system.404' => \Drupal\Core\Link::class,
      'system.4xx' => \Drupal\Core\Link::class,
      'system.admin' => \Drupal\Core\Link::class,
      'system.admin_compact_page' => \Drupal\Core\Link::class,
      'system.admin_config' => \Drupal\Core\Link::class,
      'system.admin_config_content' => \Drupal\Core\Link::class,
      'system.admin_config_development' => \Drupal\Core\Link::class,
      'system.admin_config_media' => \Drupal\Core\Link::class,
      'system.admin_config_regional' => \Drupal\Core\Link::class,
      'system.admin_config_search' => \Drupal\Core\Link::class,
      'system.admin_config_services' => \Drupal\Core\Link::class,
      'system.admin_config_system' => \Drupal\Core\Link::class,
      'system.admin_config_ui' => \Drupal\Core\Link::class,
      'system.admin_config_workflow' => \Drupal\Core\Link::class,
      'system.admin_content' => \Drupal\Core\Link::class,
      'system.admin_index' => \Drupal\Core\Link::class,
      'system.admin_reports' => \Drupal\Core\Link::class,
      'system.admin_structure' => \Drupal\Core\Link::class,
      'system.batch_page.html' => \Drupal\Core\Link::class,
      'system.batch_page.json' => \Drupal\Core\Link::class,
      'system.cron' => \Drupal\Core\Link::class,
      'system.cron_settings' => \Drupal\Core\Link::class,
      'system.csrftoken' => \Drupal\Core\Link::class,
      'system.date_format_add' => \Drupal\Core\Link::class,
      'system.db_update' => \Drupal\Core\Link::class,
      'system.entity_autocomplete' => \Drupal\Core\Link::class,
      'system.file_system_settings' => \Drupal\Core\Link::class,
      'system.files' => \Drupal\Core\Link::class,
      'system.image_toolkit_settings' => \Drupal\Core\Link::class,
      'system.logging_settings' => \Drupal\Core\Link::class,
      'system.machine_name_transliterate' => \Drupal\Core\Link::class,
      'system.modules_list' => \Drupal\Core\Link::class,
      'system.modules_list_confirm' => \Drupal\Core\Link::class,
      'system.modules_list_experimental_confirm' => \Drupal\Core\Link::class,
      'system.modules_uninstall' => \Drupal\Core\Link::class,
      'system.modules_uninstall_confirm' => \Drupal\Core\Link::class,
      'system.performance_settings' => \Drupal\Core\Link::class,
      'system.php' => \Drupal\Core\Link::class,
      'system.prepare_modules_entity_uninstall' => \Drupal\Core\Link::class,
      'system.private_file_download' => \Drupal\Core\Link::class,
      'system.regional_settings' => \Drupal\Core\Link::class,
      'system.rss_feeds_settings' => \Drupal\Core\Link::class,
      'system.run_cron' => \Drupal\Core\Link::class,
      'system.site_information_settings' => \Drupal\Core\Link::class,
      'system.site_maintenance_mode' => \Drupal\Core\Link::class,
      'system.status' => \Drupal\Core\Link::class,
      'system.temporary' => \Drupal\Core\Link::class,
      'system.theme_install' => \Drupal\Core\Link::class,
      'system.theme_set_default' => \Drupal\Core\Link::class,
      'system.theme_settings' => \Drupal\Core\Link::class,
      'system.theme_settings_theme' => \Drupal\Core\Link::class,
      'system.theme_uninstall' => \Drupal\Core\Link::class,
      'system.themes_page' => \Drupal\Core\Link::class,
      'system.timezone' => \Drupal\Core\Link::class,
    ])
  );

}
