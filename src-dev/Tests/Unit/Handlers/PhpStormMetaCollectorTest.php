<?php

namespace Drupal\ide_helper\Tests\Unit\Handlers;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldTypePluginManager;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldStorageConfigStorage;
use Drupal\ide_helper\Handlers\PhpStormMetaCollector;
use Drupal\ide_helper\Tests\Unit\IdeHelperTestBase;
use org\bovigo\vfs\vfsStream;
use Webmozart\PathUtil\Path;

/**
 * @covers \Drupal\ide_helper\Handlers\PhpStormMetaCollector
 *
 * @group IdeHelperUnit
 */
class PhpStormMetaCollectorTest extends IdeHelperTestBase {

  /**
   * @var string
   */
  protected $ideHelperDir = '.';

  public function __construct($name = NULL, array $data = [], $dataName = '') {
    parent::__construct($name, $data, $dataName);

    $this->ideHelperDir = Path::join(__DIR__, '..', '..', '..', '..');
  }

  public function testCollect(): void {
    $yaml = new Yaml();

    $extensionsInfo = [
      'my_empty' => [
        'my_empty.info.yml' => [
          'name' => 'My Empty',
          'type' => 'module',
          'description' => 'My Empty',
          'package' => 'my',
          'version' => '8.x-1.0',
          'core' => '8.x',
        ],
      ],
      'my_full' => [
        'my_full.info.yml' => [
          'name' => 'My Full',
          'type' => 'module',
          'description' => 'My Full',
          'package' => 'my',
          'version' => '8.x-1.0',
          'core' => '8.x',
        ],
        'my_full.routing.yml' => [
          'my_full.route_01' => [
            'path' => '/my-full',
          ],
          'route_callbacks' => [],
        ],
        'my_full.services.yml' => [
          'services' => [
            'my_full.service_01' => [
              'class' => '\Drupal\Core\Config\ConfigManager',
            ],
          ],
        ],
      ],
    ];

    $vfsRoot = $this->vfsRootDirFromMethod(__METHOD__);
    $vfsDirStructure = [
      'core' => [
        'core.services.yml' => $yaml->encode([
          'services' => [
            'form_error_handler' => [
              'class' => 'Drupal\Core\Form\FormErrorHandler',
            ],
          ],
        ]),
      ],
    ];

    $vfs = vfsStream::setup($vfsRoot, NULL, $vfsDirStructure);
    $appRoot = $vfs->url();

    /** @var \Drupal\Core\Extension\Extension[] $moduleList */
    $moduleList = [];
    foreach ($extensionsInfo as $extensionName => $extensionFiles) {
      $moduleList[$extensionName] = new Extension(
        $appRoot,
        $extensionFiles["$extensionName.info.yml"]['type'],
        "modules/custom/$extensionName/$extensionName.info.yml",
        "$extensionName." . $extensionFiles["$extensionName.info.yml"]['type']
      );

      $extensionDir = Path::join($appRoot, $moduleList[$extensionName]->getPath());
      foreach ($extensionFiles as $extensionFileName => $extensionFileContent) {
        $fileNameFull = Path::join($extensionDir, $extensionFileName);
        $fileDir = Path::getDirectory($fileNameFull);
        if (!file_exists($fileDir)) {
          mkdir($fileDir, 0777, TRUE);
        }

        file_put_contents(
          Path::join($extensionDir, $extensionFileName),
          $yaml->encode($extensionFileContent)
        );
      }
    }

    $entityTypeDefinitions = [
      'node' => new ContentEntityType($this->getEntityTypeDefinitionNode()),
    ];

    $fieldStorageConfigs = [
      'node.field_tags' => new FieldStorageConfig([
        'id' => 'node.field_tags',
        'field_name' => 'field_tags',
        'entity_type' => 'node',
        'type' => 'entity_reference',
        'module' => 'core',
        'settings' => [
          'target_type' => 'taxonomy_term',
        ],
        'cardinality' => -1,
        'translatable' => TRUE,
        'locked' => FALSE,
        'persist_with_no_fields' => FALSE,
        'custom_storage' => FALSE,
        'indexes' => [],
        'deleted' => FALSE,
        'schema' => NULL,
        'propertyDefinitions' => NULL,
        'originalId' => 'node.field_tags',
        'status' => TRUE,
        'uuid' => '40dadee1-c4cd-4591-82af-ecda5d033fca',
        'isSyncing' => FALSE,
        'isUninstalling' => FALSE,
        'langcode' => 'en',
        'third_party_settings' => [],
        '_core' => [
          'default_config_hash' => 'WpOE_bs8Bs_HY2ns7n2r__de-xno0-Bxkqep5-MsHAs',
        ],
        'trustedData' => FALSE,
        'entityTypeId' => 'field_storage_config',
        'enforceIsNew' => NULL,
        'typedData' => NULL,
        'cacheContexts' => [],
        'cacheTags' => [],
        'cacheMaxAge' => -1,
        '_serviceIds' => [],
        'dependencies' => [
          'module' => [
            'node',
            'taxonomy',
          ],
        ],
      ]),
    ];

    $fieldTypeDefinitions = [
      'entity_reference' => [
        'category' => 'Reference',
        'no_ui' => FALSE,
        'definition_class' => '\Drupal\Core\TypedData\DataDefinition',
        'list_definition_class' => '\Drupal\Core\TypedData\ListDataDefinition',
        'unwrap_for_canonical_representation' => TRUE,
        'id' => 'entity_reference',
        'label' => 'Entity reference',
        'description' => 'An entity field containing an entity reference.',
        'default_widget' => 'entity_reference_autocomplete',
        'default_formatter' => 'entity_reference_label',
        'list_class' => '\Drupal\Core\Field\EntityReferenceFieldItemList',
        'class' => '\Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem',
        'provider' => 'core',
      ],
    ];

    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler
      ->expects($this->any())
      ->method('getImplementations')
      ->willReturn([]);
    $moduleHandler
      ->expects($this->any())
      ->method('getModuleList')
      ->willReturn($moduleList);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager
      ->expects($this->any())
      ->method('getDefinitions')
      ->willReturn($entityTypeDefinitions);

    $fieldStorageConfigStorage = $this->createMock(FieldStorageConfigStorage::class);
    $fieldStorageConfigStorage
      ->expects($this->any())
      ->method('loadMultiple')
      ->willReturn($fieldStorageConfigs);

    $fieldTypePluginManager = $this->createMock(FieldTypePluginManager::class);
    $fieldTypePluginManager
      ->expects($this->any())
      ->method('getDefinitions')
      ->willReturn($fieldTypeDefinitions);

    $collector = new PhpStormMetaCollector(
      $moduleHandler,
      $entityTypeManager,
      $fieldStorageConfigStorage,
      $fieldTypePluginManager,
      $yaml
    );

    $collector->setDrupalRoot($appRoot);

    $expected = [
      'drupal.Core' => [
        'overrides' => [
          'Symfony\Component\DependencyInjection\ContainerInterface::get(0)' => [
            'class' => 'Symfony\Component\DependencyInjection\ContainerInterface',
            'method' => 'get(0)',
            'map' => [
              'form_error_handler' => 'Drupal\Core\Form\FormErrorHandlerInterface',
            ],
          ],
          'Drupal::service(0)' => [
            'class' => 'Drupal',
            'method' => 'service(0)',
            'map' => [
              'form_error_handler' => 'Drupal\Core\Form\FormErrorHandlerInterface',
            ],
          ],
        ],
      ],
      'drupal.my_full' => [
        'overrides' => [
          'Symfony\Component\DependencyInjection\ContainerInterface::get(0)' => [
            'class' => 'Symfony\Component\DependencyInjection\ContainerInterface',
            'method' => 'get(0)',
            'map' => [
              'my_full.service_01' => 'Drupal\Core\Config\ConfigManagerInterface',
            ],
          ],
          'Drupal::service(0)' => [
            'class' => 'Drupal',
            'method' => 'service(0)',
            'map' => [
              'my_full.service_01' => 'Drupal\Core\Config\ConfigManagerInterface',
            ],
          ],
          'Drupal\Core\Url::fromRoute(0)' => [
            'class' => 'Drupal\Core\Url',
            'method' => 'fromRoute(0)',
            'map' => [
              'my_full.route_01' => 'Drupal\Core\Url',
            ],
          ],
          'Drupal\Core\Link::createFromRoute(1)' => [
            'class' => 'Drupal\Core\Link',
            'method' => 'createFromRoute(1)',
            'map' => [
              'my_full.route_01' => 'Drupal\Core\Link',
            ],
          ],
        ],
      ],
      'drupal.node' => [
        'overrides' => [
          'Drupal\Core\Entity\EntityTypeManagerInterface::getStorage(0)' => [
            'class' => 'Drupal\Core\Entity\EntityTypeManagerInterface',
            'method' => 'getStorage(0)',
            'map' => [
              'node' => 'Drupal\node\NodeStorageInterface',
            ],
          ],
          'Drupal\Core\Entity\EntityTypeManagerInterface::getAccessControlHandler(0)' => [
            'class' => 'Drupal\Core\Entity\EntityTypeManagerInterface',
            'method' => 'getAccessControlHandler(0)',
            'map' => [
              'node' => 'Drupal\node\NodeAccessControlHandlerInterface',
            ],
          ],
          'Drupal\Core\Entity\EntityTypeManagerInterface::getListBuilder(0)' => [
            'class' => 'Drupal\Core\Entity\EntityTypeManagerInterface',
            'method' => 'getListBuilder(0)',
            'map' => [
              'node' => 'Drupal\Core\Entity\EntityListBuilderInterface',
            ],
          ],
          'Drupal\Core\Entity\EntityTypeManagerInterface::getViewBuilder(0)' => [
            'class' => 'Drupal\Core\Entity\EntityTypeManagerInterface',
            'method' => 'getViewBuilder(0)',
            'map' => [
              'node' => 'Drupal\Core\Entity\EntityViewBuilderInterface',
            ],
          ],
        ],
      ],
      'drupal.field.fields' => [
        'overrides' => [
          'Drupal\node\NodeInterface::get(0)' => [
            'class' => 'Drupal\node\NodeInterface',
            'method' => 'get(0)',
            'map' => [
              'field_tags' => 'Drupal\Core\Field\EntityReferenceFieldItemListInterface',
            ],
          ],
        ],
      ],
    ];
    $this->assertEquals($expected, $collector->collect());
  }

  protected function getEntityTypeDefinitionNode(): array {
    return [
      'revision_metadata_keys' => [
        'revision_user' => 'revision_uid',
        'revision_created' => 'revision_timestamp',
        'revision_log_message' => 'revision_log',
        'revision_default' => 'revision_default',
      ],
      'static_cache' => TRUE,
      'render_cache' => TRUE,
      'persistent_cache' => TRUE,
      'entity_keys' => [
        'id' => 'nid',
        'revision' => 'vid',
        'bundle' => 'type',
        'label' => 'title',
        'langcode' => 'langcode',
        'uuid' => 'uuid',
        'status' => 'status',
        'published' => 'status',
        'uid' => 'uid',
        'default_langcode' => 'default_langcode',
        'revision_translation_affected' => 'revision_translation_affected',
      ],
      'id' => 'node',
      'originalClass' => 'Drupal\node\Entity\Node',
      'handlers' => [
        'storage' => 'Drupal\node\NodeStorage',
        'storage_schema' => 'Drupal\node\NodeStorageSchema',
        'view_builder' => 'Drupal\node\NodeViewBuilder',
        'access' => 'Drupal\node\NodeAccessControlHandler',
        'views_data' => 'Drupal\node\NodeViewsData',
        'form' =>
          [
            'default' => 'Drupal\node\NodeForm',
            'delete' => 'Drupal\node\Form\NodeDeleteForm',
            'edit' => 'Drupal\node\NodeForm',
            'delete-multiple-confirm' => 'Drupal\node\Form\DeleteMultiple',
            'content_translation_deletion' => '\Drupal\content_translation\Form\ContentTranslationDeleteForm',
          ],
        'route_provider' =>
          [
            'html' => 'Drupal\node\Entity\NodeRouteProvider',
          ],
        'list_builder' => 'Drupal\node\NodeListBuilder',
        'translation' => 'Drupal\node\NodeTranslationHandler',
      ],
      'admin_permission' => NULL,
      'permission_granularity' => 'bundle',
      'links' => [
        'canonical' => '/node/{node}',
        'delete-form' => '/node/{node}/delete',
        'delete-multiple-form' => '/admin/content/node/delete',
        'edit-form' => '/node/{node}/edit',
        'version-history' => '/node/{node}/revisions',
        'revision' => '/node/{node}/revisions/{node_revision}/view',
        'create' => '/node',
        'devel-render' => '/devel/node/{node}/render',
        'devel-load' => '/devel/node/{node}',
        'drupal:content-translation-overview' => '/node/{node}/translations',
        'drupal:content-translation-add' => '/node/{node}/translations/add/{source}/{target}',
        'drupal:content-translation-edit' => '/node/{node}/translations/edit/{language}',
        'drupal:content-translation-delete' => '/node/{node}/translations/delete/{language}',
      ],
      'label_callback' => NULL,
      'bundle_entity_type' => 'node_type',
      'bundle_of' => NULL,
      'bundle_label' => 'Content type',
      'base_table' => 'node',
      'revision_data_table' => 'node_field_revision',
      'revision_table' => 'node_revision',
      'data_table' => 'node_field_data',
      'translatable' => TRUE,
      'show_revision_ui' => TRUE,
      'label' => 'Content',
      'label_collection' => 'Content',
      'label_singular' => 'content item',
      'label_plural' => 'content items',
      'label_count' => [
        'singular' => '@count content item',
        'plural' => '@count content items',
        'context' => NULL,
      ],
      'uri_callback' => NULL,
      'group' => 'content',
      'group_label' => 'Content',
      'field_ui_base_route' => 'entity.node_type.edit_form',
      'common_reference_target' => TRUE,
      'list_cache_contexts' => [
        0 => 'user.node_grants:view',
      ],
      'list_cache_tags' => [
        0 => 'node_list',
      ],
      'constraints' => [
        'EntityChanged' => NULL,
        'EntityUntranslatableFields' => NULL,
        'MenuSettings' => [],
      ],
      'additional' => [
        'content_translation_metadata' => 'Drupal\content_translation\ContentTranslationMetadataWrapper',
        'translation' => [
          'content_translation' => [
            'access_callback' => 'content_translation_translate_access',
          ],
        ],
      ],
      'class' => 'Drupal\node\Entity\Node',
      'provider' => 'node',
      'stringTranslation' => NULL,
    ];
  }

}
