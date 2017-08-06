<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getStorage(0),
    map([
      'taxonomy_term' => \Drupal\taxonomy\TermStorageInterface::class,
      'taxonomy_vocabulary' => \Drupal\taxonomy\VocabularyStorageInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'taxonomy_term' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
      'taxonomy_vocabulary' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getViewBuilder(0),
    map([
      'taxonomy_term' => \Drupal\Core\Entity\EntityViewBuilderInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getListBuilder(0),
    map([
      'taxonomy_vocabulary' => \Drupal\Core\Entity\EntityListBuilderInterface::class,
    ])
  );

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'taxonomy_term.breadcrumb' => \Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface::class,
    ])
  );

  override(
    \Drupal::service(0),
    map([
      'taxonomy_term.breadcrumb' => \Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface::class,
    ])
  );

  override(
    \Drupal\Core\Url::fromRoute(0),
    map([
      'entity.taxonomy_term.add_form' => \Drupal\Core\Url::class,
      'entity.taxonomy_term.canonical' => \Drupal\Core\Url::class,
      'entity.taxonomy_term.delete_form' => \Drupal\Core\Url::class,
      'entity.taxonomy_term.edit_form' => \Drupal\Core\Url::class,
      'entity.taxonomy_vocabulary.add_form' => \Drupal\Core\Url::class,
      'entity.taxonomy_vocabulary.collection' => \Drupal\Core\Url::class,
      'entity.taxonomy_vocabulary.delete_form' => \Drupal\Core\Url::class,
      'entity.taxonomy_vocabulary.edit_form' => \Drupal\Core\Url::class,
      'entity.taxonomy_vocabulary.overview_form' => \Drupal\Core\Url::class,
      'entity.taxonomy_vocabulary.reset_form' => \Drupal\Core\Url::class,
    ])
  );

  override(
    \Drupal\Core\Link::createFromRoute(1),
    map([
      'entity.taxonomy_term.add_form' => \Drupal\Core\Link::class,
      'entity.taxonomy_term.canonical' => \Drupal\Core\Link::class,
      'entity.taxonomy_term.delete_form' => \Drupal\Core\Link::class,
      'entity.taxonomy_term.edit_form' => \Drupal\Core\Link::class,
      'entity.taxonomy_vocabulary.add_form' => \Drupal\Core\Link::class,
      'entity.taxonomy_vocabulary.collection' => \Drupal\Core\Link::class,
      'entity.taxonomy_vocabulary.delete_form' => \Drupal\Core\Link::class,
      'entity.taxonomy_vocabulary.edit_form' => \Drupal\Core\Link::class,
      'entity.taxonomy_vocabulary.overview_form' => \Drupal\Core\Link::class,
      'entity.taxonomy_vocabulary.reset_form' => \Drupal\Core\Link::class,
    ])
  );

}
