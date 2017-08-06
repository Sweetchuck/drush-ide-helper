<?php

/**
 * @file
 * Create fields.
 */

$etm = \Drupal::entityTypeManager();

$tags = $etm
  ->getStorage('taxonomy_vocabulary')
  ->create([
    'vid' => 'ide_helper_tags',
    'name' => 'IDE Helper - Tags',
  ]);
$tags->save();

$tagsOwnerStorage = $etm
  ->getStorage('field_storage_config')
  ->create([
    'id' => 'taxonomy_term.ide_helper_owner',
    'entity_type' => 'taxonomy_term',
    'field_name' => 'ide_helper_owner',
    'type' => 'entity_reference',
    'settings' => [
      'target_type' => 'user',
    ],
  ]);
$tagsOwnerStorage->save();

$tagsOwnerInstance = $etm
  ->getStorage('field_config')
  ->create([
    'field_storage' => $tagsOwnerStorage,
    'bundle' => $tags->id(),
    'label' => 'IDE Helper - Owner',
  ]);
$tagsOwnerInstance->save();
