<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  use Drupal\Core\Authentication\AuthenticationProviderInterface;
  use Drupal\Core\Entity\EntityAccessControlHandlerInterface;
  use Drupal\Core\Entity\EntityListBuilderInterface;
  use Drupal\Core\Entity\EntityTypeManagerInterface;
  use Drupal\Core\Entity\EntityViewBuilderInterface;
  use Drupal\Core\Plugin\Context\ContextProviderInterface;
  use Drupal\Core\Routing\Access\AccessInterface;
  use Drupal\Core\Theme\ThemeNegotiatorInterface;
  use Drupal\user\PermissionHandlerInterface;
  use Drupal\user\PrivateTempStoreFactory;
  use Drupal\user\RoleStorageInterface;
  use Drupal\user\SharedTempStoreFactory;
  use Drupal\user\UserAuthInterface;
  use Drupal\user\UserDataInterface;
  use Drupal\user\UserStorageInterface;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Symfony\Component\EventDispatcher\EventSubscriberInterface;

  override(
    EntityTypeManagerInterface::getStorage(0),
    map([
      'user' => UserStorageInterface::class,
      'user_role' => RoleStorageInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'user' => EntityAccessControlHandlerInterface::class,
      'user_role' => EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getListBuilder(0),
    map([
      'user' => EntityListBuilderInterface::class,
      'user_role' => EntityListBuilderInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getViewBuilder(0),
    map([
      'user' => EntityViewBuilderInterface::class,
    ])
  );

  override(
    ContainerInterface::get(0),
    map([
      'access_check.permission' => AccessInterface::class,
      'access_check.user.login_status' => AccessInterface::class,
      'access_check.user.register' => AccessInterface::class,
      'access_check.user.role' => AccessInterface::class,
      'theme.negotiator.admin_theme' => ThemeNegotiatorInterface::class,
      'user.auth' => UserAuthInterface::class,
      'user.authentication.cookie' => AuthenticationProviderInterface::class,
      'user.current_user_context' => ContextProviderInterface::class,
      'user.data' => UserDataInterface::class,
      'user.permissions' => PermissionHandlerInterface::class,
      'user.private_tempstore' => PrivateTempStoreFactory::class,
      'user.shared_tempstore' => SharedTempStoreFactory::class,
      'user_access_denied_subscriber' => EventSubscriberInterface::class,
      'user_last_access_subscriber' => EventSubscriberInterface::class,
      'user_maintenance_mode_subscriber' => EventSubscriberInterface::class,
    ])
  );

}