<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Symfony\Component\HttpKernel\HttpKernelInterface;

  override(
    ContainerInterface::get(0),
    map([
      'http_middleware.page_cache' => HttpKernelInterface::class,
    ])
  );

}