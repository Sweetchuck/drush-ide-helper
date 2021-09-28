<?php

/**
 * @file
 * Delete fields.
 */

declare(strict_types = 1);

$vocabularyId = 'ide_helper_tags';

$etm = \Drupal::entityTypeManager();

$tags = $etm
  ->getStorage('taxonomy_term')
  ->loadByProperties(['vid' => $vocabularyId]);
foreach ($tags as $tag) {
  $tag->delete();
}

$vocabulary = $etm
  ->getStorage('taxonomy_vocabulary')
  ->load($vocabularyId);
if ($vocabulary) {
  $vocabulary->delete();
}
