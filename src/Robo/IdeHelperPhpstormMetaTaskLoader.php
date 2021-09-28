<?php

declare(strict_types = 1);

namespace Drupal\ide_helper\Robo;

use Drupal\ide_helper\Robo\Task\PhpstormMetaCollectorTask;
use Drupal\ide_helper\Robo\Task\PhpstormMetaRendererTask;
use Robo\Collection\CollectionBuilder;

trait IdeHelperPhpstormMetaTaskLoader {

  /**
   * @return \Drupal\ide_helper\Robo\Task\PhpstormMetaCollectorTask|\Robo\Collection\CollectionBuilder
   */
  public function taskIdeHelperPhpstormMetaCollector(array $options = []): CollectionBuilder {
    /** @var \Drupal\ide_helper\Robo\Task\PhpstormMetaCollectorTask|\Robo\Collection\CollectionBuilder $task */
    $task = $this->task(PhpstormMetaCollectorTask::class);
    $task->setOptions($options);

    return $task;
  }

  /**
   * @return \Drupal\ide_helper\Robo\Task\PhpstormMetaRendererTask|\Robo\Collection\CollectionBuilder
   */
  public function taskIdeHelperPhpstormMetaRenderer(array $options = []): CollectionBuilder {
    /** @var \Drupal\ide_helper\Robo\Task\PhpstormMetaRendererTask|\Robo\Collection\CollectionBuilder $task */
    $task = $this->task(PhpstormMetaRendererTask::class);
    $task->setOptions($options);

    return $task;
  }

}
