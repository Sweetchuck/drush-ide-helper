<?php

namespace Drupal\ide_helper\CommandHandlers;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ide_helper\PhpStormMetaFileRenderer;
use Drupal\ide_helper\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PhpStormMeta {

  /**
   * @var array
   */
  protected $extensions = [];

  /**
   * @var array
   */
  protected $allServices = [];

  /**
   * @var \Drupal\ide_helper\PhpStormMetaFileRenderer
   */
  protected $metaFileRenderer;

  /**
   * @var string
   */
  protected $outputDir = '';

  public function getOutputDir(): string {
    return $this->outputDir;
  }

  /**
   * @return $this
   */
  public function setOutputDir(string $value) {
    $this->outputDir = $value;

    return $this;
  }

  public function __construct() {
    $this->metaFileRenderer = new PhpStormMetaFileRenderer();
  }

  public function execute() {
    $this
      ->initExtensions()
      ->processExtensions();
  }

  protected function initExtensions() {
    return $this
      ->initExtensionsEntityTypes()
      ->initExtensionsServices()
      ->initExtensionRouteNames();
  }

  /**
   * @return $this
   */
  protected function initExtensionsEntityTypes() {
    $entityTypeDefinitions = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($entityTypeDefinitions as $entityTypeDefinition) {
      $originalClass = $entityTypeDefinition->getOriginalClass();
      $extensionName = Utils::extensionNameFromFqn($originalClass);
      $this->extensions[$extensionName]['entityTypes'][$entityTypeDefinition->id()] = $entityTypeDefinition;
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function initExtensionsServices() {
    $yaml = \Drupal::service('serialization.yaml');
    $services = $yaml->decode(file_get_contents(DRUPAL_ROOT . '/core/core.services.yml'));
    $this->extensions['Core']['services'] = $services['services'];
    $this->allServices = $services['services'];
    foreach (\Drupal::moduleHandler()->getModuleList() as $extension) {
      $path = $extension->getPath();
      $extensionName = $extension->getName();
      $fileName = "$path/$extensionName.services.yml";
      if (!file_exists($fileName)) {
        continue;
      }

      $services = $yaml->decode(file_get_contents($fileName));
      if (!empty($services['services'])) {
        $this->allServices += $services['services'];
        $this->extensions[$extensionName]['services'] = $services['services'];
      }
    }

    return $this;
  }

  protected function processExtensions() {
    foreach (array_keys($this->extensions) as $extensionName) {
      $this
        ->processExtension($extensionName)
        ->dump($extensionName);
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function initExtensionRouteNames() {
    $yaml = \Drupal::service('serialization.yaml');
    foreach (\Drupal::moduleHandler()->getModuleList() as $extension) {
      $path = $extension->getPath();
      $extensionName = $extension->getName();
      $fileName = "$path/$extensionName.routing.yml";
      if (!file_exists($fileName)) {
        continue;
      }

      $this->extensions[$extensionName]['routing'] = $yaml->decode(file_get_contents($fileName));
      unset($this->extensions[$extensionName]['routing']['route_callbacks']);
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtension(string $extensionName) {
    return $this
      ->processExtensionEntityTypes($extensionName)
      ->processExtensionServices($extensionName)
      ->processExtensionRouteNames($extensionName);
  }

  /**
   * @return $this
   */
  protected function processExtensionEntityTypes(string $extensionName) {
    if (empty($this->extensions[$extensionName]['entityTypes'])) {
      return $this;
    }

    $handlers = [
      [
        'name' => 'storage',
        'base' => 'Storage',
        'method' => 'getStorage(0)',
      ],
      [
        'name' => 'access',
        'base' => 'AccessControlHandler',
        'method' => 'getAccessControlHandler(0)',
      ],
      [
        'name' => 'list_builder',
        'base' => 'ListBuilder',
        'method' => 'getListBuilder(0)',
      ],
      [
        'name' => 'view_builder',
        'base' => 'ViewBuilder',
        'method' => 'getViewBuilder(0)',
      ],
    ];

    /** @var \Drupal\Core\Entity\EntityTypeInterface $entityTypeDefinition */
    foreach ($this->extensions[$extensionName]['entityTypes'] as $entityTypeDefinition) {
      foreach ($handlers as $handler) {
        if (!$entityTypeDefinition->hasHandlerClass($handler['name'])) {
          continue;
        }

        $handlerClass = $entityTypeDefinition->getHandlerClass($handler['name']);
        $handlerInterface = $this->getServiceHandlerInterface($handlerClass, $handler['base']);
        $this->metaFileRenderer->addOverride(
          EntityTypeManagerInterface::class,
          $handler['method'],
          [
            $entityTypeDefinition->id() => $handlerInterface ?: $handlerClass,
          ]
        );
      }
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtensionServices(string $extensionName) {
    if (empty($this->extensions[$extensionName]['services'])) {
      return $this;
    }

    foreach ($this->extensions[$extensionName]['services'] as $serviceName => $service) {
      $serviceClass = Utils::serviceClass($service, $this->allServices);
      if ($serviceClass) {
        $serviceClassName = Utils::classNameFromFqn($serviceClass);
        $this->metaFileRenderer->addOverride(
          ContainerInterface::class,
          'get(0)',
          [
            $serviceName => $this->getServiceHandlerInterface($serviceClass, $serviceClassName) ?: $serviceClass,
          ]
        );

        $this->metaFileRenderer->addOverride(
          \Drupal::class,
          'service(0)',
          [
            $serviceName => $this->getServiceHandlerInterface($serviceClass, $serviceClassName) ?: $serviceClass,
          ]
        );
      }
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtensionRouteNames(string $extensionName) {
    if (empty($this->extensions[$extensionName]['routing'])) {
      return $this;
    }

    foreach (array_keys($this->extensions[$extensionName]['routing']) as $routeName) {
      $this->metaFileRenderer->addOverride(
        Url::class,
        'fromRoute(0)',
        [
          $routeName => Url::class,
        ]
      );

      $this->metaFileRenderer->addOverride(
        Link::class,
        'createFromRoute(1)',
        [
          $routeName => Link::class,
        ]
      );
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function dump(string $extensionName) {
    if (!$this->metaFileRenderer->isEmpty()) {
      $outputDir = $this->getOutputDir();
      $dir = "$outputDir/.phpstorm.meta.php";
      file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
      $extensionNameLower = Unicode::strtolower($extensionName);
      file_put_contents("$dir/drupal.$extensionNameLower.php", $this->metaFileRenderer->render());
    }

    return $this;
  }

  protected function getServiceHandlerInterface(string $fqn, string $base): string {
    $implements = $fqn === 'SplString' ? [] : class_implements($fqn);
    $interfaces = $this->prioritizeInterfaces($fqn, $base, $implements);
    $firstGroup = reset($interfaces);

    return $firstGroup ? reset($firstGroup) : '';
  }

  protected function prioritizeInterfaces(string $fqn, string $base, array $interfaces): array {
    $priorities = [];

    $ignoredInterfaceNames = [
      'ContainerAwareInterface',
    ];

    $classOwner = Utils::extensionNameFromFqn($fqn);
    $className = Utils::classNameFromFqn($fqn);
    foreach ($interfaces as $interface) {
      $interfaceOwner = Utils::extensionNameFromFqn($interface);
      $interfaceName = Utils::classNameFromFqn($interface);

      if (in_array($interfaceName, $ignoredInterfaceNames)) {
        continue;
      }

      if ($interface === "{$fqn}Interface") {
        $priority = 99;
      }
      elseif ($interfaceName === "{$className}Interface") {
        $priority = 90;
      }
      elseif ($interfaceName === "ContentEntity{$base}Interface"
        || $interfaceName === "ConfigEntity{$base}Interface"
      ) {
        $priority = 89;
      }
      elseif ($interfaceName === "Entity{$base}Interface") {
        $priority = 88;
      }
      elseif ($classOwner === $interfaceOwner) {
        $priority = 75;
      }
      else {
        $priority = 50;
      }

      $priorities[$priority][$interface] = Utils::numOfWordMatches($className, $interfaceName);
    }

    krsort($priorities, SORT_NUMERIC);
    foreach ($priorities as $weight => $priority) {
      arsort($priority, SORT_NUMERIC);
      $priorities[$weight] = array_keys($priority);
    }

    return $priorities;
  }

}
