<?php

namespace Drupal\ide_helper;

class PhpStormMetaFileRenderer {

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

  /**
   * @return $this
   */
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

    return $this;
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
      $fqn = Utils::prefixFqnWithBackslash($fqn);
      $pairs[] = "'$name' => $fqn::class";
    }

    return strtr(
      $this->tplOverride,
      [
        '{{ class }}' => Utils::prefixFqnWithBackslash($override['class']),
        '{{ method }}' => $override['method'],
        '{{ pairs }}' => implode(",\n      ", $pairs) . ',',
      ]
    );
  }

}
