<?php

namespace Drupal\ide_helper\CommandHandlers;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ide_helper\PhpStormMetaFileRenderer;
use Drupal\ide_helper\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PhpStormMeta {

  // region Option - outputDir.
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
  // endregion

  public function execute() {
    $extensions = [];

    $entityTypeDefinitions = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($entityTypeDefinitions as $entityTypeDefinition) {
      $originalClass = $entityTypeDefinition->getOriginalClass();
      $extensionName = Utils::extensionNameFromFqn($originalClass);
      $extensions[$extensionName]['entityTypes'][$entityTypeDefinition->id()] = $entityTypeDefinition;
    }

    /** @var \Drupal\Component\Serialization\SerializationInterface $yaml */
    $yaml = \Drupal::service('serialization.yaml');
    $services = $yaml->decode(file_get_contents(DRUPAL_ROOT . '/core/core.services.yml'));
    $extensions['Core']['services'] = $services['services'];
    $allServices = $services['services'];
    foreach (\Drupal::moduleHandler()->getModuleList() as $extension) {
      $path = $extension->getPath();
      $extensionName = $extension->getName();
      $fileName = "$path/$extensionName.services.yml";
      if (!file_exists($fileName)) {
        continue;
      }

      $services = $yaml->decode(file_get_contents($fileName));
      if (!empty($services['services'])) {
        $allServices += $services;
        $extensions[$extensionName]['services'] = $services['services'];
      }
    }

    $handlers = [
      [
        'name' => 'storage',
        'base' => 'Storage',
        'method' => 'getStorage',
      ],
      [
        'name' => 'access',
        'base' => 'AccessControlHandler',
        'method' => 'getAccessControlHandler',
      ],
      [
        'name' => 'list_builder',
        'base' => 'ListBuilder',
        'method' => 'getListBuilder',
      ],
      [
        'name' => 'view_builder',
        'base' => 'ViewBuilder',
        'method' => 'getViewBuilder',
      ],
    ];

    $metaFileRenderer = new PhpStormMetaFileRenderer();
    foreach ($extensions as $extensionName => $items) {
      if (isset($items['entityTypes'])) {
        /** @var \Drupal\Core\Entity\EntityTypeInterface $entityTypeDefinition */
        foreach ($items['entityTypes'] as $entityTypeDefinition) {
          foreach ($handlers as $handler) {
            if (!$entityTypeDefinition->hasHandlerClass($handler['name'])) {
              continue;
            }

            $handlerClass = $entityTypeDefinition->getHandlerClass($handler['name']);
            $handlerInterface = $this->getServiceHandlerInterface($handlerClass, $handler['base']);
            $metaFileRenderer->addOverride(
              EntityTypeManagerInterface::class,
              $handler['method'],
              [
                $entityTypeDefinition->id() => $handlerInterface ?: $handlerClass,
              ]
            );
          }
        }
      }

      if (isset($items['services'])) {
        foreach ($items['services'] as $serviceName => $service) {
          $serviceClass = Utils::serviceClass($service, $allServices);
          if ($serviceClass) {
            $serviceClassName = Utils::classNameFromFqn($serviceClass);
            $metaFileRenderer->addOverride(
              ContainerInterface::class,
              'get',
              [
                $serviceName => $this->getServiceHandlerInterface($serviceClass, $serviceClassName) ?: $serviceClass,
              ]
            );
          }
        }
      }

      if (!$metaFileRenderer->isEmpty()) {
        $outputDir = $this->getOutputDir();
        $dir = "$outputDir/.phpstorm.meta.php";
        file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
        $extensionNameLower = Unicode::strtolower($extensionName);
        file_put_contents("$dir/drupal.$extensionNameLower.php", $metaFileRenderer->render());
      }
    }

    return null;
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
