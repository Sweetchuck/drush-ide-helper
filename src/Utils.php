<?php

namespace Drupal\ide_helper;

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
    $bWord = static::splitCamelCase($camelCaseB);

    return count(array_intersect($aWords, $bWord));
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

  public static function autodetectIdeaProjectRoot(string $cwd): string {
    $parts = explode('/', $cwd);

    while ($parts) {
      $dir = implode('/', $parts);
      if (is_dir("$dir/.idea")) {
        return $dir;
      }

      array_pop($parts);
    }

    return '';
  }

}
