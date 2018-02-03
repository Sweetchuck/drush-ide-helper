<?php

namespace Drupal\ide_helper\Robo\Task;

use Drupal\ide_helper\Utils;
use Robo\Contract\InflectionInterface;
use Robo\Result;
use Robo\Task\BaseTask as RoboBaseTask;
use Robo\TaskAccessor;
use Robo\TaskInfo;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseTask extends RoboBaseTask implements
    ContainerAwareInterface {

  use ContainerAwareTrait;
  use TaskAccessor;

  /**
   * @var string
   */
  protected $taskName = '';

  /**
   * @var array
   */
  protected $assets = [];

  /**
   * @var string
   */
  protected $action = '';

  /**
   * @var string
   */
  protected $assetNamePrefix = '';

  public function getAssetNamePrefix(): string {
    return $this->assetNamePrefix;
  }

  /**
   * @return $this
   */
  public function setAssetNamePrefix(string $value) {
    $this->assetNamePrefix = $value;

    return $this;
  }

  public function getContainer(): ContainerInterface {
    return $this->container;
  }

  protected function getOptions(): array {
    return [];
  }

  /**
   * @return $this
   */
  public function setOptions(array $options) {
    foreach ($options as $name => $value) {
      switch ($name) {
        case 'assetNamePrefix':
          $this->setAssetNamePrefix($value);
          break;

      }
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function run(): Result {
    return $this
      ->runPrepare()
      ->runHeader()
      ->runAction()
      ->runProcessOutputs()
      ->runReturn();
  }

  /**
   * @return $this
   */
  protected function runPrepare() {
    return $this;
  }

  /**
   * @return $this
   */
  protected function runHeader() {
    $this->printTaskDebug('');

    return $this;
  }

  /**
   * @return $this
   */
  abstract protected function runAction();

  /**
   * @return $this
   */
  protected function runProcessOutputs() {
    return $this;
  }

  protected function runReturn(): Result {
    $assetNamePrefix = $this->getAssetNamePrefix();

    return new Result(
      $this,
      $this->getTaskExitCode(),
      $this->getTaskExitMessage(),
      ($assetNamePrefix ? Utils::prefixArrayKeys($assetNamePrefix, $this->assets) : $this->assets)
    );
  }

  protected function getTaskExitCode(): int {
    return 0;
  }

  protected function getTaskExitMessage(): string {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function inflect(InflectionInterface $parent) {
    parent::inflect($parent);
    if ($parent instanceof ContainerAwareInterface
      && $parent->getContainer()
      && $this instanceof ContainerAwareInterface
      && !$this->getContainer()
    ) {
      $this->setContainer($parent->getContainer());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function injectDependencies(InflectionInterface $child) {
    parent::injectDependencies($child);

    if ($this instanceof ContainerAwareInterface
      && $this->getContainer()
      && $child instanceof ContainerAwareInterface
      && !$child->getContainer()
    ) {
      $child->setContainer($this->getContainer());
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function getTaskContext($context = NULL) {
    if (!$context) {
      $context = [];
    }

    if (empty($context['name'])) {
      $context['name'] = $this->getTaskName();
    }

    return parent::getTaskContext($context);
  }

  public function getTaskName(): string {
    return $this->taskName ?: TaskInfo::formatTaskName($this);
  }

}
