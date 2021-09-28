<?php

declare(strict_types = 1);

namespace Drupal\Tests\ide_helper\Unit;

use Drupal\Tests\UnitTestCase;

class IdeHelperTestBase extends UnitTestCase {

  protected function vfsRootDirFromMethod(string $method): string {
    $method = str_replace('::', '\\', $method);
    $method = array_slice(explode('\\', $method), 4);
    $method[] = uniqid();

    return implode('_', $method);
  }

}
