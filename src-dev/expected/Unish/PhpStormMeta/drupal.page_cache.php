<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'cache.page' => \Drupal\Core\Cache\CacheBackendInterface::class,
      'http_middleware.page_cache' => \Symfony\Component\HttpKernel\HttpKernelInterface::class,
    ])
  );

  override(
    \Drupal::service(0),
    map([
      'cache.page' => \Drupal\Core\Cache\CacheBackendInterface::class,
      'http_middleware.page_cache' => \Symfony\Component\HttpKernel\HttpKernelInterface::class,
    ])
  );

}
