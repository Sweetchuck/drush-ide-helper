<?php

namespace Drupal\ide_helper;

use Webmozart\PathUtil\Path;

class Utils {

  public static function extensionNameFromFqn(string $fqn): string {
    $parts = explode('\\', trim($fqn, '\\'));

    return $parts[1] ?? '_global';
  }

  public static function classNameFromFqn(string $fqn) {
    $parts = explode('\\', trim($fqn, '\\'));

    return end($parts);
  }

  public static function splitCamelCase(string $camelCase): array {
    return preg_split('/(?<=[a-z])(?=[A-Z])/', $camelCase);
  }

  public static function numOfWordMatches(string $camelCaseA, string $camelCaseB): array {
    $camelCaseA = str_replace('_', '', $camelCaseA);
    $camelCaseB = str_replace('_', '', $camelCaseB);
    $aWords = static::splitCamelCase($camelCaseA);
    $bWords = static::splitCamelCase($camelCaseB);

    return [
      'intersect' => count(array_intersect($aWords, $bWords)),
      'diff' => count(array_diff($aWords, $bWords)) + count(array_diff($bWords, $aWords)),
    ];
  }

  public static function serviceClass(array $service, array $allServices): string {
    if (!empty($service['class'])) {
      return $service['class'];
    }

    if (!empty($service['parent']) && isset($allServices[$service['parent']])) {
      return static::serviceClass($allServices[$service['parent']], $allServices);
    }

    return '';
  }

  public static function prefixFqnWithBackslash(string $fqn): string {
    return (mb_substr($fqn, 0, 1) !== '\\') ? "\\$fqn" : $fqn;
  }

  public static function suffixFqnWithClass(string $fqn): string {
    return (preg_match('@(::class|\[\])$@', $fqn)) ? $fqn : "$fqn::class";
  }

  public static function overrideMapTypeHint(array $types): string {
    $typeHint = [];
    $numOfClasses = 0;
    foreach ($types as $type) {
      $isClass = static::isClass($type);
      $isArray = preg_match('@\[\]$@', $type);
      if ($isClass) {
        $type = Utils::prefixFqnWithBackslash($type);
      }

      if ($isClass && !$isArray) {
        $numOfClasses++;
      }

      $typeHint[] = $type;
    }

    if ($typeHint) {
      return (count($typeHint) === 1 && $numOfClasses === 1) ?
        Utils::suffixFqnWithClass(reset($typeHint))
        : "'" . implode('|', $typeHint) . "'";
    }

    return '';
  }

  public static function isClass(string $identifier): bool {
    return strpos($identifier, '\\') !== FALSE
      || class_exists($identifier)
      || interface_exists($identifier);
  }

  public static function autodetectIdeaProjectRoot(string $cwd): ?string {
    while (is_dir($cwd)) {
      if (is_dir("$cwd/.idea")) {
        return $cwd;
      }

      $parent = Path::join($cwd, '..');
      if ($parent === $cwd) {
        return NULL;
      }

      $cwd = $parent;
    }

    return NULL;
  }

  public static function getServiceHandlerInterface(string $fqn, string $base): string {
    $implements = $fqn === 'SplString' ? ['string'] : class_implements($fqn);
    $interfaces = static::prioritizeInterfaces($fqn, $base, $implements);
    $firstGroup = reset($interfaces);

    return $firstGroup ? reset($firstGroup) : '';
  }

  /**
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   */
  public static function prioritizeInterfaces(string $fqn, string $base, array $interfaces): array {
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

      $priority = 50;
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

      $numOfWords = Utils::numOfWordMatches(
        $className,
        preg_replace('@Interface$@', '', $interfaceName)
      );

      $priorities[$priority][$interface] = $numOfWords['intersect'] - ($numOfWords['diff'] * 0.2);
    }

    krsort($priorities, SORT_NUMERIC);
    foreach ($priorities as $weight => $priority) {
      arsort($priority, SORT_NUMERIC);
      $priorities[$weight] = array_keys($priority);
    }

    return $priorities;
  }

  public static function prefixArrayKeys(string $prefix, array $array): array {
    $return = [];
    foreach ($array as $key => $value) {
      $return["{$prefix}{$key}"] = $value;
    }

    return $return;
  }

}
