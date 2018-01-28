<?php

namespace Drupal\ide_helper\Robo\Task;

use Drupal\ide_helper\Handlers\PhpStormMetaCollector;

class PhpstormMetaCollectorTask extends BaseTask {

  /**
   * @var string
   */
  protected $drupalRoot = '';

  public function getDrupalRoot(): string {
    return $this->drupalRoot;
  }

  /**
   * @return $this
   */
  public function setDrupalRoot(string $value) {
    $this->drupalRoot = $value;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  protected function runAction() {
    $this->assets['phpStormMeta'] = $this
      ->getContainer()
      ->get('class_resolver')
      ->getInstanceFromDefinition(PhpStormMetaCollector::class)
      ->setDrupalRoot($this->getDrupalRoot())
      ->collect();

    return $this;
  }

}
