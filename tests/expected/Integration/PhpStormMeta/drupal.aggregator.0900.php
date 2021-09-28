<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getStorage(0),
    map([
      'aggregator_feed' => \Drupal\aggregator\FeedStorageInterface::class,
      'aggregator_item' => \Drupal\aggregator\ItemStorageInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'aggregator_feed' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
      'aggregator_item' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getViewBuilder(0),
    map([
      'aggregator_feed' => \Drupal\Core\Entity\EntityViewBuilderInterface::class,
      'aggregator_item' => \Drupal\Core\Entity\EntityViewBuilderInterface::class,
    ])
  );

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'aggregator.items.importer' => \Drupal\aggregator\ItemsImporterInterface::class,
      'logger.channel.aggregator' => \Drupal\Core\Logger\LoggerChannelInterface::class,
      'plugin.manager.aggregator.fetcher' => \Drupal\Component\Plugin\PluginManagerInterface::class,
      'plugin.manager.aggregator.parser' => \Drupal\Component\Plugin\PluginManagerInterface::class,
      'plugin.manager.aggregator.processor' => \Drupal\Component\Plugin\PluginManagerInterface::class,
    ])
  );

  override(
    \Drupal::service(0),
    map([
      'aggregator.items.importer' => \Drupal\aggregator\ItemsImporterInterface::class,
      'logger.channel.aggregator' => \Drupal\Core\Logger\LoggerChannelInterface::class,
      'plugin.manager.aggregator.fetcher' => \Drupal\Component\Plugin\PluginManagerInterface::class,
      'plugin.manager.aggregator.parser' => \Drupal\Component\Plugin\PluginManagerInterface::class,
      'plugin.manager.aggregator.processor' => \Drupal\Component\Plugin\PluginManagerInterface::class,
    ])
  );

  override(
    \Drupal\Core\Url::fromRoute(0),
    map([
      'aggregator.admin_overview' => \Drupal\Core\Url::class,
      'aggregator.admin_settings' => \Drupal\Core\Url::class,
      'aggregator.feed_add' => \Drupal\Core\Url::class,
      'aggregator.feed_items_delete' => \Drupal\Core\Url::class,
      'aggregator.feed_refresh' => \Drupal\Core\Url::class,
      'aggregator.opml_add' => \Drupal\Core\Url::class,
      'aggregator.page_last' => \Drupal\Core\Url::class,
    ])
  );

  override(
    \Drupal\Core\Link::createFromRoute(1),
    map([
      'aggregator.admin_overview' => \Drupal\Core\Link::class,
      'aggregator.admin_settings' => \Drupal\Core\Link::class,
      'aggregator.feed_add' => \Drupal\Core\Link::class,
      'aggregator.feed_items_delete' => \Drupal\Core\Link::class,
      'aggregator.feed_refresh' => \Drupal\Core\Link::class,
      'aggregator.opml_add' => \Drupal\Core\Link::class,
      'aggregator.page_last' => \Drupal\Core\Link::class,
    ])
  );

}
