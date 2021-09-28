<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'cache.dynamic_page_cache' => \Drupal\Core\Cache\CacheBackendInterface::class,
      'dynamic_page_cache_deny_admin_routes' => \Drupal\Core\PageCache\ResponsePolicyInterface::class,
      'dynamic_page_cache_request_policy' => \Drupal\Core\PageCache\RequestPolicyInterface::class,
      'dynamic_page_cache_response_policy' => \Drupal\Core\PageCache\ChainResponsePolicyInterface::class,
      'dynamic_page_cache_subscriber' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
    ])
  );

  override(
    \Drupal::service(0),
    map([
      'cache.dynamic_page_cache' => \Drupal\Core\Cache\CacheBackendInterface::class,
      'dynamic_page_cache_deny_admin_routes' => \Drupal\Core\PageCache\ResponsePolicyInterface::class,
      'dynamic_page_cache_request_policy' => \Drupal\Core\PageCache\RequestPolicyInterface::class,
      'dynamic_page_cache_response_policy' => \Drupal\Core\PageCache\ChainResponsePolicyInterface::class,
      'dynamic_page_cache_subscriber' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
    ])
  );

}
