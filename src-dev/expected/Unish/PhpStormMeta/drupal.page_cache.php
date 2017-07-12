<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'http_middleware.page_cache' => \Symfony\Component\HttpKernel\HttpKernelInterface::class,
    ])
  );

  override(
    \Drupal::service(0),
    map([
      'http_middleware.page_cache' => \Symfony\Component\HttpKernel\HttpKernelInterface::class,
    ])
  );

}
