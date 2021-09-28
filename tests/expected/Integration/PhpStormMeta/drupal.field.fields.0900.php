<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Drupal\taxonomy\TermInterface::get(0),
    map([
      'ide_helper_owner' => \Drupal\Core\Field\EntityReferenceFieldItemListInterface::class,
    ])
  );

}
