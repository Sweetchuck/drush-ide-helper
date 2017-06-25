<?php

/**
 * @file
 * Drush command hooks and callbacks.
 */

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements hook_drush_command().
 */
function ide_helper_drush_command() {

  $commands = [];

  $commands['ide-helper-phpstorm-meta'] = [
    'aliases' => ['ihpm'],
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'description' => dt('Some helpful description.'),
    'required-arguments' => TRUE,
    'arguments' => [],
    'options' => [
      'output-dir' => [
        'description' => dt('Output directory name, without ".phpstorm.meta.php"'),
        'example-value' => '/foo/bar',
      ],
    ],
    'allow-additional-options' => FALSE,
    'examples' => [
      'drush ide-helper-foo' => dt('Description of what this command will do.'),
    ],
    'core' => ['8|8+'],
  ];

  return $commands;
}

/**
 * Implements drush_COMMAND_validate().
 */
function drush_ide_helper_phpstorm_meta_validate() {
  $outputDir = drush_get_option('output-dir');
  if (!$outputDir) {
    $outputDir = ide_helper_autodetect_output_dir();
    if (!$outputDir) {
      drush_set_error(
        'IDE_HELPER_OUTPUT_DIR_MISSING',
        dt('The output directory cannot be detected automatically.')
      );

      return;
    }

    drush_set_option('output-dir', $outputDir, 'ide_helper:default_values');
  }

  if (file_exists($outputDir) && !is_dir($outputDir)) {
    drush_set_error(
      'IDE_HELPER_OUTPUT_DIR_IS_NOT_DIR',
      dt(
        "The given path '@path' cannot be used as output directory, because it is exists but not a directory.",
        [
          '@path' => $outputDir,
        ]
      )
    );

    return;
  }
}

/**
 * Implements drush_COMMAND().
 */
function drush_ide_helper_phpstorm_meta() {
  $outputDir = drush_get_option('output-dir');
  if (!$outputDir) {
    $outputDir = drush_get_option('output-dir', NULL, 'ide_helper:default_values');
  }

  $extensions = [];

  $entityTypeDefinitions = \Drupal::entityTypeManager()->getDefinitions();
  foreach ($entityTypeDefinitions as $entityTypeDefinition) {
    $originalClass = $entityTypeDefinition->getOriginalClass();
    $extensionName = ide_helper_extension_name_from_fqn($originalClass);
    $extensions[$extensionName]['entityTypes'][$entityTypeDefinition->id()] = $entityTypeDefinition;
  }

  /** @var \Drupal\Component\Serialization\SerializationInterface $yaml */
  $yaml = \Drupal::service('serialization.yaml');
  $services = $yaml->decode(file_get_contents(DRUPAL_ROOT . '/core/core.services.yml'));
  $extensions['Core']['services'] = $services['services'];
  foreach (\Drupal::moduleHandler()->getModuleList() as $extension) {
    $path = $extension->getPath();
    $extensionName = $extension->getName();
    $fileName = "$path/$extensionName.services.yml";
    if (!file_exists($fileName)) {
      continue;
    }

    $services = $yaml->decode(file_get_contents($fileName));
    if (!empty($services['services'])) {
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

  $meta = new IdeHelperPhpStormMeta();
  foreach ($extensions as $extensionName => $items) {
    if (isset($items['entityTypes'])) {
      /** @var \Drupal\Core\Entity\EntityTypeInterface $entityTypeDefinition */
      foreach ($items['entityTypes'] as $entityTypeDefinition) {
        foreach ($handlers as $handler) {
          if (!$entityTypeDefinition->hasHandlerClass($handler['name'])) {
            continue;
          }

          $handlerClass = $entityTypeDefinition->getHandlerClass($handler['name']);
          $handlerInterface = ide_helper_get_interface($handlerClass, $handler['base']);
          $meta->addOverride(
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
        $serviceClass = ide_helper_service_class($service);
        if ($serviceClass) {
          $serviceClassName = ide_helper_class_name_from_fqn($serviceClass);
          $meta->addOverride(
            ContainerInterface::class,
            'get',
            [
              $serviceName => ide_helper_get_interface($serviceClass, $serviceClassName) ?: $serviceClass,
            ]
          );
        }
      }
    }

    if (!$meta->isEmpty()) {
      $dir = "$outputDir/.phpstorm.meta.php";
      file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
      $extensionNameLower = Unicode::strtolower($extensionName);
      file_put_contents("$dir/drupal.$extensionNameLower.php", $meta->render());
    }
  }
}

function ide_helper_extension_name_from_fqn(string $fqn): string {
  $parts = explode('\\', trim($fqn, '\\'));

  return $parts[1] ?? '_global';
}

function ide_helper_class_name_from_fqn(string $fqn) {
  $parts = explode('\\', trim($fqn, '\\'));

  return end($parts);
}

function ide_helper_get_interface(string $fqn, string $base): string {
  $implements = $fqn === 'SplString' ? [] : class_implements($fqn);
  $interfaces = ide_helper_prioritize_interfaces($fqn, $base, $implements);
  $firstGroup = reset($interfaces);

  return $firstGroup ? reset($firstGroup) : '';
}

function ide_helper_service_class(array $service): string {
  if (!empty($service['class'])) {
    return $service['class'];
  }

  // @todo Get parent.
  return '';
}

function ide_helper_autodetect_output_dir(): string {
  $cwd = getcwd();
  $suggestions = [
    $cwd,
    "$cwd/..",
  ];

  foreach ($suggestions as $dir) {
    if (is_dir("$dir/.idea")) {
      return $dir;
    }
  }

  return '';
}

function ide_helper_prioritize_interfaces(string $fqn, string $base, array $interfaces): array {
  $priorities = [];

  $ignoredInterfaceNames = [
    'ContainerAwareInterface',
  ];

  $classOwner = ide_helper_extension_name_from_fqn($fqn);
  $className = ide_helper_class_name_from_fqn($fqn);
  foreach ($interfaces as $interface) {
    $interfaceOwner = ide_helper_extension_name_from_fqn($interface);
    $interfaceName = ide_helper_class_name_from_fqn($interface);

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

    $priorities[$priority][$interface] = ide_helper_num_of_word_matches($className, $interfaceName);
  }

  krsort($priorities, SORT_NUMERIC);
  foreach ($priorities as $weight => $priority) {
    arsort($priority, SORT_NUMERIC);
    $priorities[$weight] = array_keys($priority);
  }

  return $priorities;
}

function ide_helper_num_of_word_matches(string $className, string $interfaceName): int {
  $classNameWords = ide_helper_split_camel_case($className);
  $interfaceNameWords = ide_helper_split_camel_case($interfaceName);

  return count(array_intersect($classNameWords, $interfaceNameWords));
}

function ide_helper_split_camel_case(string $camelCase): array {
  return preg_split('/(?<=[a-z])(?=[A-Z])/', $camelCase);
}

/**
 * Class IdeHelperPhpStormMeta.
 */
class IdeHelperPhpStormMeta {

  protected $overrides = [];

  protected $tplFile = <<<'PHP'
<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  {{ overrides }}

}
PHP;

  protected $tplOverride = <<<'PHP'
  override(
    {{ class }}::{{ method }}(0),
    map([
      {{ pairs }}
    ])
  );
PHP;

  public function isEmpty(): bool {
    return !$this->overrides;
  }

  public function addOverride(string $class, string $method, array $map) {
    $key = "$class::$method";
    if (!isset($this->overrides[$key])) {
      $this->overrides[$key] = [
        'class' => $class,
        'method' => $method,
        'map' => [],
      ];
    }

    $this->overrides[$key]['map'] += $map;
  }

  public function render(): string {
    $vars = [
      '{{ overrides }}' => $this->renderOverrides(),
    ];

    $this->reset();

    return strtr($this->tplFile, $vars);
  }

  protected function reset() {
    $this->overrides = [];

    return $this;
  }

  protected function renderOverrides(): string {
    $overrides = [];
    foreach ($this->overrides as $override) {
      $overrides[] = $this->renderOverride($override);
    }

    return ltrim(implode("\n\n", $overrides));
  }

  protected function renderOverride(array $override): string {
    $pairs = [];
    ksort($override['map']);
    foreach ($override['map'] as $name => $fqn) {
      $fqn = $this->prefixWithBackslash($fqn);
      $pairs[] = "'$name' => $fqn::class";
    }

    return strtr(
      $this->tplOverride,
      [
        '{{ class }}' => $this->prefixWithBackslash($override['class']),
        '{{ method }}' => $override['method'],
        '{{ pairs }}' => implode(",\n      ", $pairs) . ',',
      ]
    );
  }

  protected function prefixWithBackslash(string $fqn): string {
    return (Unicode::substr($fqn, 0, 1) !== '\\') ? "\\$fqn" : $fqn;
  }

}
