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

  override(
    \Drupal::service(0),
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

  override(
    \Drupal\Core\Url::fromRoute(0),
    map([
      'entity.user.admin_form' => \Drupal\Core\Url::class,
      'entity.user.collection' => \Drupal\Core\Url::class,
      'entity.user_role.collection' => \Drupal\Core\Url::class,
      'entity.user_role.delete_form' => \Drupal\Core\Url::class,
      'entity.user_role.edit_form' => \Drupal\Core\Url::class,
      'entity.user_role.edit_permissions_form' => \Drupal\Core\Url::class,
      'user.admin_create' => \Drupal\Core\Url::class,
      'user.admin_index' => \Drupal\Core\Url::class,
      'user.admin_permissions' => \Drupal\Core\Url::class,
      'user.cancel_confirm' => \Drupal\Core\Url::class,
      'user.login' => \Drupal\Core\Url::class,
      'user.login.http' => \Drupal\Core\Url::class,
      'user.login_status.http' => \Drupal\Core\Url::class,
      'user.logout' => \Drupal\Core\Url::class,
      'user.logout.http' => \Drupal\Core\Url::class,
      'user.multiple_cancel_confirm' => \Drupal\Core\Url::class,
      'user.page' => \Drupal\Core\Url::class,
      'user.pass' => \Drupal\Core\Url::class,
      'user.pass.http' => \Drupal\Core\Url::class,
      'user.register' => \Drupal\Core\Url::class,
      'user.reset' => \Drupal\Core\Url::class,
      'user.reset.form' => \Drupal\Core\Url::class,
      'user.reset.login' => \Drupal\Core\Url::class,
      'user.role_add' => \Drupal\Core\Url::class,
    ])
  );

  override(
    \Drupal\Core\Link::createFromRoute(1),
    map([
      'entity.user.admin_form' => \Drupal\Core\Link::class,
      'entity.user.collection' => \Drupal\Core\Link::class,
      'entity.user_role.collection' => \Drupal\Core\Link::class,
      'entity.user_role.delete_form' => \Drupal\Core\Link::class,
      'entity.user_role.edit_form' => \Drupal\Core\Link::class,
      'entity.user_role.edit_permissions_form' => \Drupal\Core\Link::class,
      'user.admin_create' => \Drupal\Core\Link::class,
      'user.admin_index' => \Drupal\Core\Link::class,
      'user.admin_permissions' => \Drupal\Core\Link::class,
      'user.cancel_confirm' => \Drupal\Core\Link::class,
      'user.login' => \Drupal\Core\Link::class,
      'user.login.http' => \Drupal\Core\Link::class,
      'user.login_status.http' => \Drupal\Core\Link::class,
      'user.logout' => \Drupal\Core\Link::class,
      'user.logout.http' => \Drupal\Core\Link::class,
      'user.multiple_cancel_confirm' => \Drupal\Core\Link::class,
      'user.page' => \Drupal\Core\Link::class,
      'user.pass' => \Drupal\Core\Link::class,
      'user.pass.http' => \Drupal\Core\Link::class,
      'user.register' => \Drupal\Core\Link::class,
      'user.reset' => \Drupal\Core\Link::class,
      'user.reset.form' => \Drupal\Core\Link::class,
      'user.reset.login' => \Drupal\Core\Link::class,
      'user.role_add' => \Drupal\Core\Link::class,
    ])
  );

}
