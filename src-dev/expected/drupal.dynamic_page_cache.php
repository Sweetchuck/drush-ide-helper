<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  use Drupal\Core\Cache\CacheBackendInterface;
  use Drupal\Core\PageCache\ChainResponsePolicyInterface;
  use Drupal\Core\PageCache\RequestPolicyInterface;
  use Drupal\Core\PageCache\ResponsePolicyInterface;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Symfony\Component\EventDispatcher\EventSubscriberInterface;

  override(
    ContainerInterface::get(0),
    map([
      'cache.dynamic_page_cache' => CacheBackendInterface::class,
      'dynamic_page_cache_deny_admin_routes' => ResponsePolicyInterface::class,
      'dynamic_page_cache_request_policy' => RequestPolicyInterface::class,
      'dynamic_page_cache_response_policy' => ChainResponsePolicyInterface::class,
      'dynamic_page_cache_subscriber' => EventSubscriberInterface::class,
    ])
  );

}