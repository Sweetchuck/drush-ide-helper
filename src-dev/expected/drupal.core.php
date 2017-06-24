<?php

/**
 * @file
 * PhpStorm meta.
 */

namespace PHPSTORM_META {

  use ArrayAccess;
  use Drupal\Component\Datetime\TimeInterface;
  use Drupal\Component\Serialization\SerializationInterface;
  use Drupal\Component\Transliteration\TransliterationInterface;
  use Drupal\Component\Uuid\UuidInterface;
  use Drupal\Core\Access\AccessArgumentsResolverFactoryInterface;
  use Drupal\Core\Access\AccessCheckInterface;
  use Drupal\Core\Access\AccessManagerInterface;
  use Drupal\Core\Access\CheckProviderInterface;
  use Drupal\Core\Access\CsrfTokenGenerator;
  use Drupal\Core\AppRootFactory;
  use Drupal\Core\Asset\AssetCollectionGrouperInterface;
  use Drupal\Core\Asset\AssetCollectionOptimizerInterface;
  use Drupal\Core\Asset\AssetCollectionRendererInterface;
  use Drupal\Core\Asset\AssetDumperInterface;
  use Drupal\Core\Asset\AssetOptimizerInterface;
  use Drupal\Core\Asset\AssetResolverInterface;
  use Drupal\Core\Asset\LibraryDependencyResolverInterface;
  use Drupal\Core\Asset\LibraryDiscoveryInterface;
  use Drupal\Core\Asset\LibraryDiscoveryParser;
  use Drupal\Core\Authentication\AuthenticationCollectorInterface;
  use Drupal\Core\Authentication\AuthenticationProviderInterface;
  use Drupal\Core\Batch\BatchStorageInterface;
  use Drupal\Core\Block\BlockManagerInterface;
  use Drupal\Core\Breadcrumb\ChainBreadcrumbBuilderInterface;
  use Drupal\Core\Cache\CacheBackendInterface;
  use Drupal\Core\Cache\CacheCollectorInterface;
  use Drupal\Core\Cache\CacheFactoryInterface;
  use Drupal\Core\Cache\CacheTagsChecksumInterface;
  use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
  use Drupal\Core\Cache\CacheableDependencyInterface;
  use Drupal\Core\Cache\Context\CacheContextInterface;
  use Drupal\Core\Cache\Context\CacheContextsManager;
  use Drupal\Core\Cache\Context\CalculatedCacheContextInterface;
  use Drupal\Core\Cache\Context\RequestFormatCacheContext;
  use Drupal\Core\Cache\Context\SessionCacheContext;
  use Drupal\Core\Config\ConfigFactoryInterface;
  use Drupal\Core\Config\ConfigInstallerInterface;
  use Drupal\Core\Config\ConfigManagerInterface;
  use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
  use Drupal\Core\Config\StorageInterface;
  use Drupal\Core\Config\TypedConfigManagerInterface;
  use Drupal\Core\Controller\ControllerResolverInterface;
  use Drupal\Core\Controller\HtmlFormController;
  use Drupal\Core\Controller\TitleResolverInterface;
  use Drupal\Core\CronInterface;
  use Drupal\Core\Database\Connection;
  use Drupal\Core\Datetime\DateFormatterInterface;
  use Drupal\Core\DependencyInjection\ClassResolverInterface;
  use Drupal\Core\DestructableInterface;
  use Drupal\Core\Diff\DiffFormatter;
  use Drupal\Core\Entity\EntityAccessControlHandlerInterface;
  use Drupal\Core\Entity\EntityAutocompleteMatcher;
  use Drupal\Core\Entity\EntityBundleListenerInterface;
  use Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface;
  use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
  use Drupal\Core\Entity\EntityFieldManagerInterface;
  use Drupal\Core\Entity\EntityFormBuilderInterface;
  use Drupal\Core\Entity\EntityLastInstalledSchemaRepositoryInterface;
  use Drupal\Core\Entity\EntityListBuilderInterface;
  use Drupal\Core\Entity\EntityManagerInterface;
  use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
  use Drupal\Core\Entity\EntityRepositoryInterface;
  use Drupal\Core\Entity\EntityResolverManager;
  use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
  use Drupal\Core\Entity\EntityTypeListenerInterface;
  use Drupal\Core\Entity\EntityTypeManagerInterface;
  use Drupal\Core\Entity\EntityTypeRepositoryInterface;
  use Drupal\Core\Entity\HtmlEntityFormController;
  use Drupal\Core\Entity\Query\QueryFactory;
  use Drupal\Core\Entity\Query\QueryFactoryInterface;
  use Drupal\Core\Executable\ExecutableManagerInterface;
  use Drupal\Core\Extension\InfoParserInterface;
  use Drupal\Core\Extension\ModuleHandlerInterface;
  use Drupal\Core\Extension\ModuleInstallerInterface;
  use Drupal\Core\Extension\ModuleUninstallValidatorInterface;
  use Drupal\Core\Extension\ThemeHandlerInterface;
  use Drupal\Core\Extension\ThemeInstallerInterface;
  use Drupal\Core\Field\FieldDefinitionListenerInterface;
  use Drupal\Core\Field\FieldStorageDefinitionListenerInterface;
  use Drupal\Core\Field\FieldTypePluginManagerInterface;
  use Drupal\Core\File\FileSystemInterface;
  use Drupal\Core\Flood\FloodInterface;
  use Drupal\Core\Form\FormAjaxResponseBuilderInterface;
  use Drupal\Core\Form\FormBuilderInterface;
  use Drupal\Core\Form\FormCacheInterface;
  use Drupal\Core\Form\FormErrorHandlerInterface;
  use Drupal\Core\Form\FormSubmitterInterface;
  use Drupal\Core\Form\FormValidatorInterface;
  use Drupal\Core\Http\ClientFactory;
  use Drupal\Core\Http\HandlerStackConfigurator;
  use Drupal\Core\ImageToolkit\ImageToolkitOperationManagerInterface;
  use Drupal\Core\Image\ImageFactory;
  use Drupal\Core\KeyValueStore\KeyValueExpirableFactoryInterface;
  use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
  use Drupal\Core\Language\LanguageDefault;
  use Drupal\Core\Language\LanguageManagerInterface;
  use Drupal\Core\Locale\CountryManagerInterface;
  use Drupal\Core\Lock\LockBackendInterface;
  use Drupal\Core\Logger\LogMessageParserInterface;
  use Drupal\Core\Logger\LoggerChannelFactoryInterface;
  use Drupal\Core\Logger\LoggerChannelInterface;
  use Drupal\Core\Mail\MailManagerInterface;
  use Drupal\Core\Menu\ContextualLinkManagerInterface;
  use Drupal\Core\Menu\DefaultMenuLinkTreeManipulators;
  use Drupal\Core\Menu\LocalActionManagerInterface;
  use Drupal\Core\Menu\LocalTaskManagerInterface;
  use Drupal\Core\Menu\MenuActiveTrailInterface;
  use Drupal\Core\Menu\MenuLinkManagerInterface;
  use Drupal\Core\Menu\MenuLinkTreeInterface;
  use Drupal\Core\Menu\MenuParentFormSelectorInterface;
  use Drupal\Core\Menu\MenuTreeStorageInterface;
  use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
  use Drupal\Core\PageCache\ChainResponsePolicyInterface;
  use Drupal\Core\PageCache\RequestPolicyInterface;
  use Drupal\Core\PageCache\ResponsePolicyInterface;
  use Drupal\Core\ParamConverter\ParamConverterInterface;
  use Drupal\Core\ParamConverter\ParamConverterManagerInterface;
  use Drupal\Core\Password\PasswordInterface;
  use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
  use Drupal\Core\Path\AliasManagerInterface;
  use Drupal\Core\Path\AliasStorageInterface;
  use Drupal\Core\Path\AliasWhitelistInterface;
  use Drupal\Core\Path\CurrentPathStack;
  use Drupal\Core\Path\PathMatcherInterface;
  use Drupal\Core\Path\PathValidatorInterface;
  use Drupal\Core\Plugin\CachedDiscoveryClearerInterface;
  use Drupal\Core\Plugin\Context\ContextHandlerInterface;
  use Drupal\Core\Plugin\Context\ContextProviderInterface;
  use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
  use Drupal\Core\Plugin\PluginFormFactoryInterface;
  use Drupal\Core\PrivateKey;
  use Drupal\Core\Queue\QueueDatabaseFactory;
  use Drupal\Core\Queue\QueueFactory;
  use Drupal\Core\Queue\QueueWorkerManagerInterface;
  use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
  use Drupal\Core\Render\BareHtmlPageRendererInterface;
  use Drupal\Core\Render\ElementInfoManagerInterface;
  use Drupal\Core\Render\MainContent\MainContentRendererInterface;
  use Drupal\Core\Render\PlaceholderGeneratorInterface;
  use Drupal\Core\Render\Placeholder\PlaceholderStrategyInterface;
  use Drupal\Core\Render\RenderCacheInterface;
  use Drupal\Core\Render\RendererInterface as RendererInterface1;
  use Drupal\Core\RouteProcessor\OutboundRouteProcessorInterface;
  use Drupal\Core\Routing\AccessAwareRouterInterface;
  use Drupal\Core\Routing\Access\AccessInterface;
  use Drupal\Core\Routing\AdminContext;
  use Drupal\Core\Routing\Enhancer\RouteEnhancerInterface as RouteEnhancerInterface1;
  use Drupal\Core\Routing\MatcherDumperInterface;
  use Drupal\Core\Routing\PreloadableRouteProviderInterface;
  use Drupal\Core\Routing\RedirectDestinationInterface;
  use Drupal\Core\Routing\RequestContext;
  use Drupal\Core\Routing\ResettableStackedRouteMatchInterface;
  use Drupal\Core\Routing\RouteBuilderInterface;
  use Drupal\Core\Routing\RouteFilterInterface as RouteFilterInterface1;
  use Drupal\Core\Routing\RouteProviderInterface;
  use Drupal\Core\Routing\UrlGeneratorInterface;
  use Drupal\Core\Session\AccountProxyInterface;
  use Drupal\Core\Session\AccountSwitcherInterface;
  use Drupal\Core\Session\PermissionsHashGeneratorInterface;
  use Drupal\Core\Session\SessionConfigurationInterface;
  use Drupal\Core\Session\SessionManagerInterface;
  use Drupal\Core\Session\WriteSafeSessionHandlerInterface;
  use Drupal\Core\SitePathFactory;
  use Drupal\Core\Site\MaintenanceModeInterface;
  use Drupal\Core\Site\Settings;
  use Drupal\Core\State\StateInterface;
  use Drupal\Core\StreamWrapper\PhpStreamWrapperInterface;
  use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
  use Drupal\Core\StringTranslation\TranslationInterface;
  use Drupal\Core\StringTranslation\Translator\TranslatorInterface;
  use Drupal\Core\Template\TwigEnvironment;
  use Drupal\Core\Theme\ThemeInitializationInterface;
  use Drupal\Core\Theme\ThemeManagerInterface;
  use Drupal\Core\Theme\ThemeNegotiatorInterface;
  use Drupal\Core\TypedData\TypedDataManagerInterface;
  use Drupal\Core\Update\UpdateRegistry;
  use Drupal\Core\Update\UpdateRegistryFactory;
  use Drupal\Core\Utility\LinkGeneratorInterface;
  use Drupal\Core\Utility\Token;
  use Drupal\Core\Utility\UnroutedUrlAssemblerInterface;
  use Egulias\EmailValidator\EmailValidatorInterface;
  use GuzzleHttp\ClientInterface;
  use GuzzleHttp\HandlerStack;
  use SessionHandlerInterface;
  use SplString;
  use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
  use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
  use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
  use Symfony\Cmf\Component\Routing\Enhancer\RouteEnhancerInterface;
  use Symfony\Cmf\Component\Routing\NestedMatcher\RouteFilterInterface;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Symfony\Component\EventDispatcher\EventDispatcherInterface;
  use Symfony\Component\EventDispatcher\EventSubscriberInterface;
  use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;
  use Symfony\Component\HttpFoundation\RequestStack;
  use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
  use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
  use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
  use Symfony\Component\HttpFoundation\Session\SessionInterface;
  use Symfony\Component\HttpKernel\HttpKernelInterface;
  use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
  use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
  use Symfony\Component\Routing\RouterInterface;
  use Twig_ExistsLoaderInterface;
  use Twig_ExtensionInterface;
  use Twig_LoaderInterface;
  use Zend\Feed\Reader\ExtensionManagerInterface;
  use Zend\Feed\Reader\Extension\Atom\Entry as Entry2;
  use Zend\Feed\Reader\Extension\Atom\Feed as Feed1;
  use Zend\Feed\Reader\Extension\Content\Entry as Entry1;
  use Zend\Feed\Reader\Extension\DublinCore\Entry;
  use Zend\Feed\Reader\Extension\DublinCore\Feed;
  use Zend\Feed\Reader\Extension\Podcast\Entry as Entry6;
  use Zend\Feed\Reader\Extension\Podcast\Feed as Feed2;
  use Zend\Feed\Reader\Extension\Slash\Entry as Entry3;
  use Zend\Feed\Reader\Extension\Thread\Entry as Entry5;
  use Zend\Feed\Reader\Extension\WellFormedWeb\Entry as Entry4;
  use Zend\Feed\Writer\Extension\ITunes\Entry as Entry7;
  use Zend\Feed\Writer\Extension\ITunes\Feed as Feed3;
  use Zend\Feed\Writer\Extension\RendererInterface;

  override(
    EntityTypeManagerInterface::getStorage(0),
    map([
      'base_field_override' => ConfigEntityStorageInterface::class,
      'date_format' => ConfigEntityStorageInterface::class,
      'entity_form_display' => ConfigEntityStorageInterface::class,
      'entity_form_mode' => ConfigEntityStorageInterface::class,
      'entity_view_display' => ConfigEntityStorageInterface::class,
      'entity_view_mode' => ConfigEntityStorageInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getAccessControlHandler(0),
    map([
      'base_field_override' => EntityAccessControlHandlerInterface::class,
      'date_format' => EntityAccessControlHandlerInterface::class,
      'entity_form_display' => EntityAccessControlHandlerInterface::class,
      'entity_form_mode' => EntityAccessControlHandlerInterface::class,
      'entity_view_display' => EntityAccessControlHandlerInterface::class,
      'entity_view_mode' => EntityAccessControlHandlerInterface::class,
    ])
  );

  override(
    EntityTypeManagerInterface::getListBuilder(0),
    map([
      'date_format' => EntityListBuilderInterface::class,
    ])
  );

  override(
    ContainerInterface::get(0),
    map([
      'accept_negotiation_406' => EventSubscriberInterface::class,
      'access_arguments_resolver_factory' => AccessArgumentsResolverFactoryInterface::class,
      'access_check.csrf' => AccessInterface::class,
      'access_check.custom' => AccessInterface::class,
      'access_check.default' => AccessInterface::class,
      'access_check.entity' => AccessInterface::class,
      'access_check.entity_create' => AccessInterface::class,
      'access_check.entity_create_any' => AccessInterface::class,
      'access_check.header.csrf' => AccessCheckInterface::class,
      'access_check.theme' => AccessInterface::class,
      'access_manager' => AccessManagerInterface::class,
      'access_manager.check_provider' => CheckProviderInterface::class,
      'account_switcher' => AccountSwitcherInterface::class,
      'ajax_response.attachments_processor' => AttachmentsResponseProcessorInterface::class,
      'ajax_response.subscriber' => EventSubscriberInterface::class,
      'anonymous_user_response_subscriber' => EventSubscriberInterface::class,
      'app.root' => SplString::class,
      'app.root.factory' => AppRootFactory::class,
      'asset.css.collection_grouper' => AssetCollectionGrouperInterface::class,
      'asset.css.collection_optimizer' => AssetCollectionOptimizerInterface::class,
      'asset.css.collection_renderer' => AssetCollectionRendererInterface::class,
      'asset.css.dumper' => AssetDumperInterface::class,
      'asset.css.optimizer' => AssetOptimizerInterface::class,
      'asset.js.collection_grouper' => AssetCollectionGrouperInterface::class,
      'asset.js.collection_optimizer' => AssetCollectionOptimizerInterface::class,
      'asset.js.collection_renderer' => AssetCollectionRendererInterface::class,
      'asset.js.dumper' => AssetDumperInterface::class,
      'asset.js.optimizer' => AssetOptimizerInterface::class,
      'asset.resolver' => AssetResolverInterface::class,
      'authentication' => AuthenticationProviderInterface::class,
      'authentication_collector' => AuthenticationCollectorInterface::class,
      'authentication_subscriber' => EventSubscriberInterface::class,
      'bare_html_page_renderer' => BareHtmlPageRendererInterface::class,
      'batch.storage' => BatchStorageInterface::class,
      'breadcrumb' => ChainBreadcrumbBuilderInterface::class,
      'cache.backend.apcu' => CacheFactoryInterface::class,
      'cache.backend.chainedfast' => CacheFactoryInterface::class,
      'cache.backend.database' => CacheFactoryInterface::class,
      'cache.backend.memory' => CacheFactoryInterface::class,
      'cache.backend.php' => CacheFactoryInterface::class,
      'cache.bootstrap' => CacheBackendInterface::class,
      'cache.config' => CacheBackendInterface::class,
      'cache.data' => CacheBackendInterface::class,
      'cache.default' => CacheBackendInterface::class,
      'cache.discovery' => CacheBackendInterface::class,
      'cache.entity' => CacheBackendInterface::class,
      'cache.menu' => CacheBackendInterface::class,
      'cache.render' => CacheBackendInterface::class,
      'cache.static' => CacheBackendInterface::class,
      'cache_context.cookies' => CalculatedCacheContextInterface::class,
      'cache_context.headers' => CalculatedCacheContextInterface::class,
      'cache_context.ip' => CacheContextInterface::class,
      'cache_context.languages' => CalculatedCacheContextInterface::class,
      'cache_context.request_format' => RequestFormatCacheContext::class,
      'cache_context.route' => CacheContextInterface::class,
      'cache_context.route.menu_active_trails' => CalculatedCacheContextInterface::class,
      'cache_context.route.name' => CacheContextInterface::class,
      'cache_context.session' => SessionCacheContext::class,
      'cache_context.session.exists' => CacheContextInterface::class,
      'cache_context.theme' => CacheContextInterface::class,
      'cache_context.timezone' => CacheContextInterface::class,
      'cache_context.url' => CacheContextInterface::class,
      'cache_context.url.path' => CacheContextInterface::class,
      'cache_context.url.path.is_front' => CacheContextInterface::class,
      'cache_context.url.path.parent' => CacheContextInterface::class,
      'cache_context.url.query_args' => CalculatedCacheContextInterface::class,
      'cache_context.url.query_args.pagers' => CalculatedCacheContextInterface::class,
      'cache_context.url.site' => CacheContextInterface::class,
      'cache_context.user' => CacheContextInterface::class,
      'cache_context.user.is_super_user' => CacheContextInterface::class,
      'cache_context.user.permissions' => CacheContextInterface::class,
      'cache_context.user.roles' => CalculatedCacheContextInterface::class,
      'cache_contexts_manager' => CacheContextsManager::class,
      'cache_factory' => CacheFactoryInterface::class,
      'cache_router_rebuild_subscriber' => EventSubscriberInterface::class,
      'cache_tags.invalidator' => CacheTagsInvalidatorInterface::class,
      'cache_tags.invalidator.checksum' => CacheTagsChecksumInterface::class,
      'class_resolver' => ClassResolverInterface::class,
      'client_error_response_subscriber' => EventSubscriberInterface::class,
      'config.factory' => ConfigFactoryInterface::class,
      'config.importer_subscriber' => EventSubscriberInterface::class,
      'config.installer' => ConfigInstallerInterface::class,
      'config.manager' => ConfigManagerInterface::class,
      'config.storage' => StorageInterface::class,
      'config.storage.active' => StorageInterface::class,
      'config.storage.schema' => StorageInterface::class,
      'config.storage.snapshot' => StorageInterface::class,
      'config.storage.staging' => StorageInterface::class,
      'config.typed' => TypedConfigManagerInterface::class,
      'config_import_subscriber' => EventSubscriberInterface::class,
      'config_snapshot_subscriber' => EventSubscriberInterface::class,
      'container.namespaces' => ArrayAccess::class,
      'content_type_header_matcher' => RouteFilterInterface1::class,
      'content_uninstall_validator' => ModuleUninstallValidatorInterface::class,
      'context.handler' => ContextHandlerInterface::class,
      'context.repository' => ContextRepositoryInterface::class,
      'controller.entity_form' => HtmlEntityFormController::class,
      'controller.form' => HtmlFormController::class,
      'controller_resolver' => ControllerResolverInterface::class,
      'country_manager' => CountryManagerInterface::class,
      'cron' => CronInterface::class,
      'csrf_token' => CsrfTokenGenerator::class,
      'current_route_match' => ResettableStackedRouteMatchInterface::class,
      'current_user' => AccountProxyInterface::class,
      'database' => Connection::class,
      'database.replica' => Connection::class,
      'date.formatter' => DateFormatterInterface::class,
      'datetime.time' => TimeInterface::class,
      'diff.formatter' => DiffFormatter::class,
      'early_rendering_controller_wrapper_subscriber' => EventSubscriberInterface::class,
      'email.validator' => EmailValidatorInterface::class,
      'entity.autocomplete_matcher' => EntityAutocompleteMatcher::class,
      'entity.bundle_config_import_validator' => EventSubscriberInterface::class,
      'entity.definition_update_manager' => EntityDefinitionUpdateManagerInterface::class,
      'entity.form_builder' => EntityFormBuilderInterface::class,
      'entity.last_installed_schema.repository' => EntityLastInstalledSchemaRepositoryInterface::class,
      'entity.manager' => EntityManagerInterface::class,
      'entity.query' => QueryFactory::class,
      'entity.query.config' => QueryFactoryInterface::class,
      'entity.query.keyvalue' => QueryFactoryInterface::class,
      'entity.query.null' => QueryFactoryInterface::class,
      'entity.query.sql' => QueryFactoryInterface::class,
      'entity.repository' => EntityRepositoryInterface::class,
      'entity_bundle.listener' => EntityBundleListenerInterface::class,
      'entity_display.repository' => EntityDisplayRepositoryInterface::class,
      'entity_field.manager' => EntityFieldManagerInterface::class,
      'entity_route_subscriber' => EventSubscriberInterface::class,
      'entity_type.bundle.info' => EntityTypeBundleInfoInterface::class,
      'entity_type.listener' => EntityTypeListenerInterface::class,
      'entity_type.manager' => EntityTypeManagerInterface::class,
      'entity_type.repository' => EntityTypeRepositoryInterface::class,
      'event_dispatcher' => EventDispatcherInterface::class,
      'exception.custom_page_html' => EventSubscriberInterface::class,
      'exception.default' => EventSubscriberInterface::class,
      'exception.default_html' => EventSubscriberInterface::class,
      'exception.default_json' => EventSubscriberInterface::class,
      'exception.enforced_form_response' => EventSubscriberInterface::class,
      'exception.fast_404_html' => EventSubscriberInterface::class,
      'exception.logger' => EventSubscriberInterface::class,
      'exception.needs_installer' => EventSubscriberInterface::class,
      'exception.test_site' => EventSubscriberInterface::class,
      'feed.bridge.reader' => ExtensionManagerInterface::class,
      'feed.bridge.writer' => ExtensionManagerInterface::class,
      'feed.reader.atomentry' => Entry2::class,
      'feed.reader.atomfeed' => Feed1::class,
      'feed.reader.contententry' => Entry1::class,
      'feed.reader.dublincoreentry' => Entry::class,
      'feed.reader.dublincorefeed' => Feed::class,
      'feed.reader.podcastentry' => Entry6::class,
      'feed.reader.podcastfeed' => Feed2::class,
      'feed.reader.slashentry' => Entry3::class,
      'feed.reader.threadentry' => Entry5::class,
      'feed.reader.wellformedwebentry' => Entry4::class,
      'feed.writer.atomrendererfeed' => RendererInterface::class,
      'feed.writer.contentrendererentry' => RendererInterface::class,
      'feed.writer.dublincorerendererentry' => RendererInterface::class,
      'feed.writer.dublincorerendererfeed' => RendererInterface::class,
      'feed.writer.itunesentry' => Entry7::class,
      'feed.writer.itunesfeed' => Feed3::class,
      'feed.writer.itunesrendererentry' => RendererInterface::class,
      'feed.writer.itunesrendererfeed' => RendererInterface::class,
      'feed.writer.slashrendererentry' => RendererInterface::class,
      'feed.writer.threadingrendererentry' => RendererInterface::class,
      'feed.writer.wellformedwebrendererentry' => RendererInterface::class,
      'field_definition.listener' => FieldDefinitionListenerInterface::class,
      'field_storage_definition.listener' => FieldStorageDefinitionListenerInterface::class,
      'field_uninstall_validator' => ModuleUninstallValidatorInterface::class,
      'file.mime_type.guesser' => MimeTypeGuesserInterface::class,
      'file.mime_type.guesser.extension' => MimeTypeGuesserInterface::class,
      'file_system' => FileSystemInterface::class,
      'finish_response_subscriber' => EventSubscriberInterface::class,
      'flood' => FloodInterface::class,
      'form_ajax_response_builder' => FormAjaxResponseBuilderInterface::class,
      'form_ajax_subscriber' => EventSubscriberInterface::class,
      'form_builder' => FormBuilderInterface::class,
      'form_cache' => FormCacheInterface::class,
      'form_error_handler' => FormErrorHandlerInterface::class,
      'form_submitter' => FormSubmitterInterface::class,
      'form_validator' => FormValidatorInterface::class,
      'html_response.attachments_processor' => AttachmentsResponseProcessorInterface::class,
      'html_response.placeholder_strategy_subscriber' => EventSubscriberInterface::class,
      'html_response.subscriber' => EventSubscriberInterface::class,
      'http_client' => ClientInterface::class,
      'http_client_factory' => ClientFactory::class,
      'http_handler_stack' => HandlerStack::class,
      'http_handler_stack_configurator' => HandlerStackConfigurator::class,
      'http_kernel' => HttpKernelInterface::class,
      'http_kernel.basic' => HttpKernelInterface::class,
      'http_middleware.cors' => HttpKernelInterface::class,
      'http_middleware.kernel_pre_handle' => HttpKernelInterface::class,
      'http_middleware.negotiation' => HttpKernelInterface::class,
      'http_middleware.reverse_proxy' => HttpKernelInterface::class,
      'http_middleware.session' => HttpKernelInterface::class,
      'image.factory' => ImageFactory::class,
      'image.toolkit.manager' => CacheableDependencyInterface::class,
      'image.toolkit.operation.manager' => ImageToolkitOperationManagerInterface::class,
      'info_parser' => InfoParserInterface::class,
      'kernel_destruct_subscriber' => EventSubscriberInterface::class,
      'keyvalue' => KeyValueFactoryInterface::class,
      'keyvalue.database' => KeyValueFactoryInterface::class,
      'keyvalue.expirable' => KeyValueExpirableFactoryInterface::class,
      'keyvalue.expirable.database' => KeyValueExpirableFactoryInterface::class,
      'language.current_language_context' => ContextProviderInterface::class,
      'language.default' => LanguageDefault::class,
      'language_manager' => LanguageManagerInterface::class,
      'library.dependency_resolver' => LibraryDependencyResolverInterface::class,
      'library.discovery' => LibraryDiscoveryInterface::class,
      'library.discovery.collector' => CacheCollectorInterface::class,
      'library.discovery.parser' => LibraryDiscoveryParser::class,
      'link_generator' => LinkGeneratorInterface::class,
      'lock' => LockBackendInterface::class,
      'lock.persistent' => LockBackendInterface::class,
      'logger.channel.file' => LoggerChannelInterface::class,
      'logger.channel_base' => LoggerChannelInterface::class,
      'logger.factory' => LoggerChannelFactoryInterface::class,
      'logger.log_message_parser' => LogMessageParserInterface::class,
      'main_content_renderer.ajax' => MainContentRendererInterface::class,
      'main_content_renderer.dialog' => MainContentRendererInterface::class,
      'main_content_renderer.html' => MainContentRendererInterface::class,
      'main_content_renderer.modal' => MainContentRendererInterface::class,
      'main_content_view_subscriber' => EventSubscriberInterface::class,
      'maintenance_mode' => MaintenanceModeInterface::class,
      'maintenance_mode_subscriber' => EventSubscriberInterface::class,
      'menu.active_trail' => MenuActiveTrailInterface::class,
      'menu.default_tree_manipulators' => DefaultMenuLinkTreeManipulators::class,
      'menu.link_tree' => MenuLinkTreeInterface::class,
      'menu.parent_form_selector' => MenuParentFormSelectorInterface::class,
      'menu.rebuild_subscriber' => EventSubscriberInterface::class,
      'menu.tree_storage' => MenuTreeStorageInterface::class,
      'menu_link.static.overrides' => StaticMenuLinkOverridesInterface::class,
      'method_filter' => RouteFilterInterface1::class,
      'module_handler' => ModuleHandlerInterface::class,
      'module_installer' => ModuleInstallerInterface::class,
      'options_request_listener' => EventSubscriberInterface::class,
      'page_cache_kill_switch' => ResponsePolicyInterface::class,
      'page_cache_no_cache_routes' => ResponsePolicyInterface::class,
      'page_cache_no_server_error' => ResponsePolicyInterface::class,
      'page_cache_request_policy' => RequestPolicyInterface::class,
      'page_cache_response_policy' => ChainResponsePolicyInterface::class,
      'paramconverter.configentity_admin' => ParamConverterInterface::class,
      'paramconverter.entity' => ParamConverterInterface::class,
      'paramconverter.entity_revision' => ParamConverterInterface::class,
      'paramconverter.menu_link' => ParamConverterInterface::class,
      'paramconverter_manager' => ParamConverterManagerInterface::class,
      'paramconverter_subscriber' => EventSubscriberInterface::class,
      'password' => PasswordInterface::class,
      'path.alias_manager' => AliasManagerInterface::class,
      'path.alias_storage' => AliasStorageInterface::class,
      'path.alias_whitelist' => AliasWhitelistInterface::class,
      'path.current' => CurrentPathStack::class,
      'path.matcher' => PathMatcherInterface::class,
      'path.validator' => PathValidatorInterface::class,
      'path_processor_alias' => InboundPathProcessorInterface::class,
      'path_processor_decode' => InboundPathProcessorInterface::class,
      'path_processor_front' => InboundPathProcessorInterface::class,
      'path_processor_manager' => InboundPathProcessorInterface::class,
      'path_subscriber' => EventSubscriberInterface::class,
      'pgsql.entity.query.sql' => QueryFactoryInterface::class,
      'placeholder_strategy' => PlaceholderStrategyInterface::class,
      'placeholder_strategy.single_flush' => PlaceholderStrategyInterface::class,
      'plugin.cache_clearer' => CachedDiscoveryClearerInterface::class,
      'plugin.manager.action' => CacheableDependencyInterface::class,
      'plugin.manager.archiver' => CacheableDependencyInterface::class,
      'plugin.manager.block' => BlockManagerInterface::class,
      'plugin.manager.condition' => ExecutableManagerInterface::class,
      'plugin.manager.display_variant' => CacheableDependencyInterface::class,
      'plugin.manager.element_info' => ElementInfoManagerInterface::class,
      'plugin.manager.entity_reference_selection' => SelectionPluginManagerInterface::class,
      'plugin.manager.field.field_type' => FieldTypePluginManagerInterface::class,
      'plugin.manager.field.formatter' => CacheableDependencyInterface::class,
      'plugin.manager.field.widget' => CacheableDependencyInterface::class,
      'plugin.manager.link_relation_type' => CacheableDependencyInterface::class,
      'plugin.manager.mail' => MailManagerInterface::class,
      'plugin.manager.menu.contextual_link' => ContextualLinkManagerInterface::class,
      'plugin.manager.menu.link' => MenuLinkManagerInterface::class,
      'plugin.manager.menu.local_action' => LocalActionManagerInterface::class,
      'plugin.manager.menu.local_task' => LocalTaskManagerInterface::class,
      'plugin.manager.queue_worker' => QueueWorkerManagerInterface::class,
      'plugin_form.factory' => PluginFormFactoryInterface::class,
      'private_key' => PrivateKey::class,
      'psr7.http_foundation_factory' => HttpFoundationFactoryInterface::class,
      'psr7.http_message_factory' => HttpMessageFactoryInterface::class,
      'psr_response_view_subscriber' => EventSubscriberInterface::class,
      'queue' => QueueFactory::class,
      'queue.database' => QueueDatabaseFactory::class,
      'redirect.destination' => RedirectDestinationInterface::class,
      'redirect_leading_slashes_subscriber' => EventSubscriberInterface::class,
      'redirect_response_subscriber' => EventSubscriberInterface::class,
      'render_cache' => RenderCacheInterface::class,
      'render_placeholder_generator' => PlaceholderGeneratorInterface::class,
      'renderer' => RendererInterface1::class,
      'replica_database_ignore__subscriber' => EventSubscriberInterface::class,
      'request_close_subscriber' => EventSubscriberInterface::class,
      'request_format_route_filter' => RouteFilterInterface1::class,
      'request_stack' => RequestStack::class,
      'required_module_uninstall_validator' => ModuleUninstallValidatorInterface::class,
      'resolver_manager.entity' => EntityResolverManager::class,
      'response_filter.active_link' => EventSubscriberInterface::class,
      'response_filter.rss.relative_url' => EventSubscriberInterface::class,
      'response_generator_subscriber' => EventSubscriberInterface::class,
      'route_access_response_subscriber' => EventSubscriberInterface::class,
      'route_enhancer.entity' => RouteEnhancerInterface1::class,
      'route_enhancer.entity_revision' => RouteEnhancerInterface1::class,
      'route_enhancer.form' => RouteEnhancerInterface1::class,
      'route_enhancer.lazy_collector' => RouteEnhancerInterface::class,
      'route_enhancer.param_conversion' => RouteEnhancerInterface1::class,
      'route_enhancer_subscriber' => EventSubscriberInterface::class,
      'route_filter.lazy_collector' => RouteFilterInterface::class,
      'route_filter_subscriber' => EventSubscriberInterface::class,
      'route_http_method_subscriber' => EventSubscriberInterface::class,
      'route_processor_csrf' => OutboundRouteProcessorInterface::class,
      'route_processor_current' => OutboundRouteProcessorInterface::class,
      'route_processor_manager' => OutboundRouteProcessorInterface::class,
      'route_special_attributes_subscriber' => EventSubscriberInterface::class,
      'route_subscriber.entity' => EventSubscriberInterface::class,
      'route_subscriber.module' => EventSubscriberInterface::class,
      'router' => AccessAwareRouterInterface::class,
      'router.admin_context' => AdminContext::class,
      'router.builder' => RouteBuilderInterface::class,
      'router.dumper' => MatcherDumperInterface::class,
      'router.dynamic' => ChainedRouterInterface::class,
      'router.matcher' => RequestMatcherInterface::class,
      'router.matcher.final_matcher' => UrlMatcherInterface::class,
      'router.no_access_checks' => RouterInterface::class,
      'router.path_roots_subscriber' => EventSubscriberInterface::class,
      'router.request_context' => RequestContext::class,
      'router.route_preloader' => EventSubscriberInterface::class,
      'router.route_provider' => RouteProviderInterface::class,
      'router.route_provider.lazy_builder' => PreloadableRouteProviderInterface::class,
      'router_listener' => EventSubscriberInterface::class,
      'serialization.json' => SerializationInterface::class,
      'serialization.phpserialize' => SerializationInterface::class,
      'serialization.yaml' => SerializationInterface::class,
      'session' => SessionInterface::class,
      'session.attribute_bag' => AttributeBagInterface::class,
      'session.flash_bag' => FlashBagInterface::class,
      'session_configuration' => SessionConfigurationInterface::class,
      'session_handler.storage' => SessionHandlerInterface::class,
      'session_handler.write_check' => SessionHandlerInterface::class,
      'session_handler.write_safe' => WriteSafeSessionHandlerInterface::class,
      'session_manager' => SessionManagerInterface::class,
      'session_manager.metadata_bag' => SessionBagInterface::class,
      'settings' => Settings::class,
      'site.path' => SplString::class,
      'site.path.factory' => SitePathFactory::class,
      'state' => StateInterface::class,
      'stream_wrapper.public' => PhpStreamWrapperInterface::class,
      'stream_wrapper.temporary' => PhpStreamWrapperInterface::class,
      'stream_wrapper_manager' => StreamWrapperManagerInterface::class,
      'string_translation' => TranslationInterface::class,
      'string_translator.custom_strings' => TranslatorInterface::class,
      'theme.initialization' => ThemeInitializationInterface::class,
      'theme.manager' => ThemeManagerInterface::class,
      'theme.negotiator' => ThemeNegotiatorInterface::class,
      'theme.negotiator.ajax_base_page' => ThemeNegotiatorInterface::class,
      'theme.negotiator.default' => ThemeNegotiatorInterface::class,
      'theme.registry' => DestructableInterface::class,
      'theme_handler' => ThemeHandlerInterface::class,
      'theme_installer' => ThemeInstallerInterface::class,
      'title_resolver' => TitleResolverInterface::class,
      'token' => Token::class,
      'transliteration' => TransliterationInterface::class,
      'twig' => TwigEnvironment::class,
      'twig.extension' => Twig_ExtensionInterface::class,
      'twig.extension.debug' => Twig_ExtensionInterface::class,
      'twig.loader' => Twig_LoaderInterface::class,
      'twig.loader.filesystem' => Twig_ExistsLoaderInterface::class,
      'twig.loader.string' => Twig_ExistsLoaderInterface::class,
      'twig.loader.theme_registry' => Twig_ExistsLoaderInterface::class,
      'typed_data_manager' => TypedDataManagerInterface::class,
      'unrouted_url_assembler' => UnroutedUrlAssemblerInterface::class,
      'update.post_update_registry' => UpdateRegistry::class,
      'update.post_update_registry_factory' => UpdateRegistryFactory::class,
      'url_generator' => UrlGeneratorInterface::class,
      'url_generator.non_bubbling' => UrlGeneratorInterface::class,
      'user_permissions_hash_generator' => PermissionsHashGeneratorInterface::class,
      'uuid' => UuidInterface::class,
      'validation.constraint' => CacheableDependencyInterface::class,
    ])
  );

}