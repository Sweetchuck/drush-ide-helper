<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getStorage(0),
    map([
      'file' => \Drupal\file\FileStorageInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'file' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getViewBuilder(0),
    map([
      'file' => \Drupal\Core\Entity\EntityViewBuilderInterface::class,
    ])
  );

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'file.usage' => \Drupal\file\FileUsage\FileUsageInterface::class,
    ])
  );

  override(
    \Drupal::service(0),
    map([
      'file.usage' => \Drupal\file\FileUsage\FileUsageInterface::class,
    ])
  );

  override(
    \Drupal\Core\Url::fromRoute(0),
    map([
      'file.ajax_progress' => \Drupal\Core\Url::class,
    ])
  );

  override(
    \Drupal\Core\Link::createFromRoute(1),
    map([
      'file.ajax_progress' => \Drupal\Core\Link::class,
    ])
  );

}
