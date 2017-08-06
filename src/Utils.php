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

  public static function numOfWordMatches(string $camelCaseA, string $camelCaseB): int {
    $aWords = static::splitCamelCase($camelCaseA);
    $bWords = static::splitCamelCase($camelCaseB);

    return count(array_intersect($aWords, $bWords));
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
      $isClass = strpos($type, '\\') !== FALSE
        || class_exists($type)
        || interface_exists($type);

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

  public static function autodetectIdeaProjectRoot(string $cwd): string {
    while (is_dir($cwd)) {
      if (is_dir("$cwd/.idea")) {
        return $cwd;
      }

      $parent = Path::join($cwd, '..');
      if ($parent === $cwd) {
        return '';
      }

      $cwd = $parent;
    }

    return '';
  }

}
