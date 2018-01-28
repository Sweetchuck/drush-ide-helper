<?php

namespace Drupal\ide_helper\Handlers;

use Drupal\ide_helper\Utils;

class PhpStormMetaRenderer {

  /**
   * @var array
   */
  protected $overrides = [];

  /**
   * @var string
   */
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
    {{ class }}::{{ method }},
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
    foreach ($override['map'] as $name => $types) {
      if (is_string($types)) {
        $types = [$types];
      }

      $typeHint = Utils::overrideMapTypeHint($types);
      if ($typeHint) {
        $pairs[] = "'$name' => $typeHint";
      }
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
