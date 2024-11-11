<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldConfigInterface;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\node\NodeInterface;

/**
 * Test the Field States UI FieldStateManager.
 *
 * @group field_states_ui
 */
abstract class FieldStateTestBase extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'claro';

  /**
   * The entity display interface.
   *
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  protected EntityViewDisplayInterface $display;

  /**
   * The Entity Form Display for the article node type.
   *
   * @var \Drupal\Core\Entity\Entity\EntityFormDisplay
   */
  protected EntityFormDisplayInterface $formDisplay;

  /**
   * The primary node to be testing.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected NodeInterface $node;

  /**
   * Generalized Settings for field state arrays.
   *
   * @var array
   */
  protected array $fieldStateSettings;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
  ];

  /**
   * Default set of field tests.
   *
   * @param string $field_name
   *   The field name/id.
   * @param string $label
   *   The field label.
   * @param string $widget
   *   The field widget id.
   * @param mixed[] $settings
   *   Field States settings.
   * @param \Drupal\field\FieldConfigInterface $config
   *   The field config to adjust as necessary.
   */
  protected function checkField(string $field_name, string $label, string $widget, array $settings, FieldConfigInterface $config) {
    $this->formDisplay->setComponent($field_name, [
      'type' => $widget,
      'settings' => [],
      'third_party_settings' => [
        'field_states_ui' => [
          'field_states' => $settings,
        ],
      ],
    ])->save();
    $label .= " - {$widget}";
    $config->set('label', $label)->save();

    // Test single value field with simple visibility state.
    $config->getFieldStorageDefinition()->set('cardinality', 1)->save();
    $this->drupalGet("node/add/article");
    $session = $this->assertSession();
    $session->pageTextNotContains($label);
    $page = $this->getSession()->getPage();
    $page->fillField('title[0][value]', 'Some value');
    $session->pageTextContains($label);

    // Test field with specified number of values (2)
    $config->getFieldStorageDefinition()->set('cardinality', 2)->save();
    $label .= " - multiple";
    $config->set('label', $label)->save();
    $this->drupalGet("node/add/article");
    $session->pageTextNotContains($label);
    $page = $this->getSession()->getPage();
    $page->fillField('title[0][value]', 'Some value');
    $session->pageTextContains($label);
  }

  /**
   * Create field of the required type with default settings and return config.
   *
   * @param string $type
   *   The field type to create.
   * @param string $field_name
   *   Field name/id.
   * @param string $label
   *   Label for the field.
   *
   * @return \Drupal\field\FieldConfigInterface
   *   The field config.
   */
  protected function createField(string $type, string $field_name, string $label): FieldConfigInterface {
    FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'type' => $type,
      'cardinality' => '1',
    ])->save();
    $config = FieldConfig::create([
      'field_name' => $field_name,
      'label' => $label,
      'entity_type' => 'node',
      'bundle' => 'article',
    ]);
    $config->save();
    return $config;
  }

  /**
   * Prepare to submit a field.
   *
   * Updates the entity view display, and loads the node add page with a filled
   * title so the field is visible.
   *
   * @param string $field_name
   *   The field name/id.
   * @param string $display_widget
   *   The display widget to test.
   * @param string $label
   *   The field label.
   * @param mixed[] $display_setting
   *   The display settings to use - empty array for default settings.
   * @param \Drupal\field\FieldConfigInterface $config
   *   The Field config.
   */
  protected function preSubmitTest(string $field_name, string $display_widget, string $label, array $display_setting, FieldConfigInterface $config): void {
    $this->display->setComponent($field_name, [
      'type' => $display_widget,
      'settings' => $display_setting,
      'region' => 'content',
      'weight' => 5,
    ])->save();

    // Test saving value.
    $config->getFieldStorageDefinition()->set('cardinality', 1)->save();
    $this->drupalGet("node/add/article");
    $page = $this->getSession()->getPage();
    $page->fillField('title[0][value]', 'Some value');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->fieldStateSettings = [
      '248b729c-09de-40b4-8957-35a38eb5ecd5' => [
        'uuid' => '248b729c-09de-40b4-8957-35a38eb5ecd5',
        'id' => 'visible',
        'data' => [
          'target' => 'title',
          'comparison' => 'filled',
          'value' => 'true',
        ],
      ],
    ];
    $this->drupalCreateContentType(['type' => 'article']);
    $user = $this->drupalCreateUser([
      'create article content',
      'edit own article content',
      'administer node form display',
    ]);
    $this->drupalLogin($user);
    $entityTypeManager = $this->container->get('entity_type.manager');
    $this->formDisplay = $entityTypeManager
      ->getStorage('entity_form_display')
      ->load('node.article.default');
    $this->display = $entityTypeManager
      ->getStorage('entity_view_display')
      ->load('node.article.default');
  }

}
