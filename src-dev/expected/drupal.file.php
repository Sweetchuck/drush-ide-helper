<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  use Drupal\Core\Entity\EntityAccessControlHandlerInterface;
  use Drupal\Core\Entity\EntityTypeManagerInterface;
  use Drupal\Core\Entity\EntityViewBuilderInterface;
  use Drupal\file\FileStorageInterface;
  use Drupal\file\FileUsage\FileUsageInterface;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  override(
    EntityTypeManagerInterface::getStorage(0),
    map([
      'file' => FileStorageInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'file' => EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getViewBuilder(0),
    map([
      'file' => EntityViewBuilderInterface::class,
    ])
  );

  override(
    ContainerInterface::get(0),
    map([
      'file.usage' => FileUsageInterface::class,
    ])
  );

}