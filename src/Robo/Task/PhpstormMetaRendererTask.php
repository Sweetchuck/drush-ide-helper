<?php

namespace Drupal\ide_helper\Robo\Task;

use Drupal\ide_helper\Handlers\PhpStormMetaRenderer;

class PhpstormMetaRendererTask extends BaseTask {

  /**
   * @var array
   */
  protected $phpStormMeta = [];

  public function getPhpStormMeta(): array {
    return $this->phpStormMeta;
  }

  /**
   * @return $this
   */
  public function setPhpStormMeta(array $value) {
    $this->phpStormMeta = $value;

    return $this;
  }

  /**
   * @var bool
   */
  protected $multipleFiles = TRUE;

  public function getMultipleFiles(): bool {
    return $this->multipleFiles;
  }

  /**
   * @return $this
   */
  public function setMultipleFiles(bool $value) {
    $this->multipleFiles = $value;

    return $this;
  }

  /**
   * @var \Drupal\ide_helper\Handlers\PhpStormMetaRenderer
   */
  protected $phpStormMetaRenderer;

  /**
   * {@inheritdoc}
   */
  public function setOptions(array $options) {
    parent::setOptions($options);

    if (array_key_exists('phpStormMeta', $options)) {
      $this->setPhpStormMeta($options['phpStormMeta']);
    }

    if (array_key_exists('multipleFiles', $options)) {
      $this->setPhpStormMeta($options['multipleFiles']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function runAction() {
    $this->phpStormMetaRenderer = $this
      ->getContainer()
      ->get('class_resolver')
      ->getInstanceFromDefinition(PhpStormMetaRenderer::class);

    $this->getMultipleFiles() ? $this->runActionMultipleFiles() : $this->runActionSingleFile();

    return $this;
  }

  /**
   * @return $this
   */
  protected function runActionMultipleFiles() {
    foreach ($this->getPhpStormMeta() as $extensionNameFull => $phpStormMeta) {
      $extensionNameFullLower = mb_strtolower($extensionNameFull);
      $this->addPhpStormMetaToRenderer($phpStormMeta);
      $this->assets['phpStormMetaFiles'][".phpstorm.meta.php/$extensionNameFullLower.php"] = $this->phpStormMetaRenderer->render();
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function runActionSingleFile() {
    foreach ($this->getPhpStormMeta() as $phpStormMeta) {
      $this->addPhpStormMetaToRenderer($phpStormMeta);
    }
    $this->assets['phpStormMetaFiles']['.phpstorm.meta.php'] = $this->phpStormMetaRenderer->render();

    return $this;
  }

  /**
   * @return $this
   */
  protected function addPhpStormMetaToRenderer(array $phpStormMeta) {
    if (!empty($phpStormMeta['overrides'])) {
      foreach ($phpStormMeta['overrides'] as $override) {
        $this->phpStormMetaRenderer->addOverride(
          $override['class'],
          $override['method'],
          $override['map']
        );
      }
    }

    return $this;
  }

}
