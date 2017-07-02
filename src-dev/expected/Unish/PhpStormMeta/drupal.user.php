<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getStorage(0),
    map([
      'user' => \Drupal\user\UserStorageInterface::class,
      'user_role' => \Drupal\user\RoleStorageInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'user' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
      'user_role' => \Drupal\Core\Entity\EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getListBuilder(0),
    map([
      'user' => \Drupal\Core\Entity\EntityListBuilderInterface::class,
      'user_role' => \Drupal\Core\Entity\EntityListBuilderInterface::class,
    ])
  );

  override(
    \Drupal\Core\Entity\EntityTypeManagerInterface::getViewBuilder(0),
    map([
      'user' => \Drupal\Core\Entity\EntityViewBuilderInterface::class,
    ])
  );

  override(
    \Symfony\Component\DependencyInjection\ContainerInterface::get(0),
    map([
      'access_check.permission' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'access_check.user.login_status' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'access_check.user.register' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'access_check.user.role' => \Drupal\Core\Routing\Access\AccessInterface::class,
      'theme.negotiator.admin_theme' => \Drupal\Core\Theme\ThemeNegotiatorInterface::class,
      'user.auth' => \Drupal\user\UserAuthInterface::class,
      'user.authentication.cookie' => \Drupal\Core\Authentication\AuthenticationProviderInterface::class,
      'user.current_user_context' => \Drupal\Core\Plugin\Context\ContextProviderInterface::class,
      'user.data' => \Drupal\user\UserDataInterface::class,
      'user.permissions' => \Drupal\user\PermissionHandlerInterface::class,
      'user.private_tempstore' => \Drupal\user\PrivateTempStoreFactory::class,
      'user.shared_tempstore' => \Drupal\user\SharedTempStoreFactory::class,
      'user_access_denied_subscriber' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
      'user_last_access_subscriber' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
      'user_maintenance_mode_subscriber' => \Symfony\Component\EventDispatcher\EventSubscriberInterface::class,
    ])
  );

}
