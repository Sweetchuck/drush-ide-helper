<?php

namespace Drupal\ide_helper\Handlers;

use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ide_helper\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webmozart\PathUtil\Path;

class PhpStormMetaCollector implements ContainerInjectionInterface {

  /**
   * @var array
   */
  protected $extensions = [];

  /**
   * @var array
   */
  protected $phpStormMeta = [];

  /**
   * @var array
   */
  protected $allServices = [];

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $fieldStorageConfigStorage;

  /**
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypePluginManager;

  /**
   * @var \Drupal\Component\Serialization\SerializationInterface
   */
  protected $yaml;

  /**
   * @var string
   */
  protected $drupalRoot = '';

  /**
   * @var string
   */
  protected $packageNamePrefix = 'drupal';

  public function getDrupalRoot(): string {
    return $this->drupalRoot;
  }

  protected function getDrupalRootWithFallback(): string {
    $drupalRoot = $this->getDrupalRoot();
    if (!$drupalRoot && defined('DRUPAL_ROOT')) {
      $drupalRoot = DRUPAL_ROOT;
    }

    return $drupalRoot ?: '.';
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
  public static function create(ContainerInterface $container) {
    $entityTypeManager = $container->get('entity_type.manager');

    return new static(
      $container->get('module_handler'),
      $entityTypeManager,
      $entityTypeManager->getStorage('field_storage_config'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('serialization.yaml')
    );
  }

  public function __construct(
    ModuleHandlerInterface $moduleHandler,
    EntityTypeManagerInterface $entityTypeManager,
    ConfigEntityStorageInterface $fieldStorageConfigStorage,
    FieldTypePluginManagerInterface $fieldTypePluginManager,
    SerializationInterface $yaml
  ) {
    $this->moduleHandler = $moduleHandler;
    $this->entityTypeManager = $entityTypeManager;
    $this->fieldStorageConfigStorage = $fieldStorageConfigStorage;
    $this->fieldTypePluginManager = $fieldTypePluginManager;
    $this->yaml = $yaml;
  }

  public function collect(): array {
    return $this
      ->reset()
      ->initExtensions()
      ->processExtensions()
      ->processFields()
      ->phpStormMeta;
  }

  /**
   * @return $this
   */
  protected function reset() {
    $this->extensions = [];
    $this->phpStormMeta = [];

    return $this;
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
    foreach ($this->entityTypeManager->getDefinitions() as $entityTypeDefinition) {
      $originalClass = $entityTypeDefinition->getOriginalClass();
      $extensionNameFull = $this->getExtensionNameFull(Utils::extensionNameFromFqn($originalClass));
      $this->extensions[$extensionNameFull]['entityTypes'][$entityTypeDefinition->id()] = $entityTypeDefinition;
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function initExtensionsServices() {
    $drupalRoot = $this->getDrupalRootWithFallback();
    $services = $this->yaml->decode(file_get_contents("$drupalRoot/core/core.services.yml"));
    $coreNameFull = $this->getExtensionNameFull('Core');
    $this->extensions[$coreNameFull]['services'] = $services['services'];
    $this->allServices = $services['services'];
    foreach ($this->moduleHandler->getModuleList() as $extension) {
      $path = $extension->getPath();
      $extensionName = $extension->getName();
      $extensionNameFull = $this->getExtensionNameFull($extensionName);
      $fileName = Path::join($drupalRoot, $path, "$extensionName.services.yml");
      if (!file_exists($fileName)) {
        continue;
      }

      $services = $this->yaml->decode(file_get_contents($fileName));
      if (!empty($services['services'])) {
        $this->allServices += $services['services'];
        $this->extensions[$extensionNameFull]['services'] = $services['services'];
      }
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtensions() {
    foreach (array_keys($this->extensions) as $extensionNameFull) {
      $this->processExtension($extensionNameFull);
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtension(string $extensionNameFull) {
    return $this
      ->processExtensionEntityTypes($extensionNameFull)
      ->processExtensionServices($extensionNameFull)
      ->processExtensionRouteNames($extensionNameFull);
  }

  /**
   * @return $this
   */
  protected function initExtensionRouteNames() {
    $drupalRoot = $this->getDrupalRoot();
    foreach ($this->moduleHandler->getModuleList() as $extension) {
      $path = $extension->getPath();
      $extensionName = $extension->getName();
      $extensionNameFull = $this->getExtensionNameFull($extensionName);
      $fileName = "$drupalRoot/$path/$extensionName.routing.yml";
      if (!file_exists($fileName)) {
        continue;
      }

      $this->extensions[$extensionNameFull]['routing'] = $this->yaml->decode(file_get_contents($fileName));
      unset($this->extensions[$extensionNameFull]['routing']['route_callbacks']);
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtensionEntityTypes(string $extensionNameFull) {
    if (empty($this->extensions[$extensionNameFull]['entityTypes'])) {
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

    /** @var \Drupal\Core\Entity\EntityTypeInterface $entityType */
    foreach ($this->extensions[$extensionNameFull]['entityTypes'] as $entityType) {
      foreach ($handlers as $handler) {
        if (!$entityType->hasHandlerClass($handler['name'])) {
          continue;
        }

        $handlerClass = $entityType->getHandlerClass($handler['name']);
        $handlerInterface = Utils::getServiceHandlerInterface($handlerClass, $handler['base']);
        $this->addOverride(
          $extensionNameFull,
          EntityTypeManagerInterface::class,
          $handler['method'],
          [
            $entityType->id() => $handlerInterface ?: $handlerClass,
          ]
        );
      }
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtensionServices(string $extensionNameFull) {
    if (empty($this->extensions[$extensionNameFull]['services'])) {
      return $this;
    }

    foreach ($this->extensions[$extensionNameFull]['services'] as $serviceName => $service) {
      $serviceClass = Utils::serviceClass($service, $this->allServices);
      if ($serviceClass) {
        $serviceClassName = Utils::classNameFromFqn($serviceClass);
        $this->addOverride(
          $extensionNameFull,
          ContainerInterface::class,
          'get(0)',
          [
            $serviceName => Utils::getServiceHandlerInterface($serviceClass, $serviceClassName) ?: $serviceClass,
          ]
        );

        $this->addOverride(
          $extensionNameFull,
          \Drupal::class,
          'service(0)',
          [
            $serviceName => Utils::getServiceHandlerInterface($serviceClass, $serviceClassName) ?: $serviceClass,
          ]
        );
      }
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function processExtensionRouteNames(string $extensionNameFull) {
    if (empty($this->extensions[$extensionNameFull]['routing'])) {
      return $this;
    }

    foreach (array_keys($this->extensions[$extensionNameFull]['routing']) as $routeName) {
      $this->addOverride(
        $extensionNameFull,
        Url::class,
        'fromRoute(0)',
        [
          $routeName => Url::class,
        ]
      );

      $this->addOverride(
        $extensionNameFull,
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
  protected function processFields() {
    /** @var \Drupal\field\Entity\FieldStorageConfig[] $fields */
    $fields = $this->fieldStorageConfigStorage->loadMultiple();
    $entityTypes = $this->entityTypeManager->getDefinitions();
    $fieldTypes = $this->fieldTypePluginManager->getDefinitions();

    $defaultFieldItemListInterface = FieldItemListInterface::class;
    foreach ($fields as $field) {
      $entityType = $entityTypes[$field->getTargetEntityTypeId()];
      $fieldName = $field->getName();
      $fieldType = $fieldTypes[$field->getType()];

      $entityTypeClass = $entityType->getClass();
      $entityTypeInterface = Utils::getServiceHandlerInterface($entityTypeClass, '');

      $fieldItemListInterface = Utils::getServiceHandlerInterface($fieldType['list_class'], '');
      if ($fieldItemListInterface === $defaultFieldItemListInterface) {
        continue;
      }

      $extensionNameFull = $this->getExtensionNameFull('field.fields');

      $this->addOverride(
        $extensionNameFull,
        $entityTypeInterface,
        'get(0)',
        [
          $fieldName => $fieldItemListInterface,
        ]
      );
    }

    return $this;
  }

  /**
   * @return $this
   */
  protected function addOverride(string $extensionNameFull, string $class, string $method, array $map) {
    $key = "$class::$method";
    if (!isset($this->phpStormMeta[$extensionNameFull]['overrides'][$key])) {
      $this->phpStormMeta[$extensionNameFull]['overrides'][$key] = [
        'class' => $class,
        'method' => $method,
        'map' => [],
      ];
    }

    $this->phpStormMeta[$extensionNameFull]['overrides'][$key]['map'] += $map;

    return $this;
  }

  protected function getExtensionNameFull(string $extensionName): string {
    return "{$this->packageNamePrefix}.{$extensionName}";
  }

}
