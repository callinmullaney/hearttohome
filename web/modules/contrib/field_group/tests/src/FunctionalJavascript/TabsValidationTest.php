<?php

namespace Drupal\Tests\field_group\FunctionalJavascript;

use Drupal\Core\Url;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\field_group\Functional\FieldGroupTestTrait;

/**
 * Tests HTML5 validation on fields in tabs.
 *
 * @group field_group
 */
class TabsValidationTest extends WebDriverTestBase {

  use FieldGroupTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field_group',
    'node',
    'user',
  ];

  /**
   * The node type used for testing.
   *
   * @var \Drupal\node\NodeTypeInterface
   */
  protected $testNodeType;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->testNodeType = $this->drupalCreateContentType([
      'type' => 'test_node_bundle',
      'name' => 'Test Node Type',
    ]);

    // Add an extra field to the test content type.
    $entity_type_manager = $this->container->get('entity_type.manager');
    $field_storage = $entity_type_manager
      ->getStorage('field_storage_config')
      ->create([
        'type' => 'string',
        'field_name' => 'test_label',
        'entity_type' => 'node',
      ]);
    assert($field_storage instanceof FieldStorageConfigInterface);
    $field_storage->save();

    $entity_type_manager->getStorage('field_config')
      ->create([
        'label' => 'Test label',
        'field_storage' => $field_storage,
        'bundle' => $this->testNodeType->id(),
        'required' => TRUE,
      ])
      ->save();

    $tab1 = [
      'label' => 'Tab1',
      'group_name' => 'group_tab1',
      'weight' => '1',
      'children' => [
        0 => 'body',
      ],
      'format_type' => 'tab',
      'format_settings' => [
        'label' => 'Tab1',
        'formatter' => 'open',
      ],
    ];
    $this->createGroup('node', $this->testNodeType->id(), 'form', 'default', $tab1);

    $tab2 = [
      'label' => 'Tab2',
      'group_name' => 'group_tab2',
      'weight' => '2',
      'children' => [
        0 => 'test_label',
      ],
      'format_type' => 'tab',
      'format_settings' => [
        'label' => 'Tab2',
        'formatter' => 'closed',
      ],
    ];
    $this->createGroup('node', $this->testNodeType->id(), 'form', 'default', $tab2);

    $horizontal_tabs = [
      'label' => 'Horizontal tabs',
      'group_name' => 'group_horizontal_tabs',
      'weight' => '-5',
      'children' => [
        'group_tab1',
        'group_tab2',
      ],
      'format_type' => 'tabs',
      'format_settings' => [
        'direction' => 'horizontal',
        'label' => 'Horizontal tabs',
      ],
    ];
    $this->createGroup('node', $this->testNodeType->id(), 'form', 'default', $horizontal_tabs);

    $entity_type_manager->getStorage('entity_form_display')
      ->load(implode('.', [
        'node',
        $this->testNodeType->id(),
        'default',
      ]))
      ->setComponent('test_label', ['weight' => '1'])
      ->save();
  }

  /**
   * Tests tabs validation.
   */
  public function testTabsValidation() {
    $page = $this->getSession()->getPage();
    $this->drupalLogin($this->rootUser);

    $this->drupalGet(Url::fromRoute('node.add', [
      'node_type' => $this->testNodeType->id(),
    ]));

    $titleField = $page->findField('title[0][value]');
    $labelField = $page->findField('test_label[0][value]');
    $submitButton = $page->findButton('edit-submit');
    $title = $this->randomMachineName();

    // Submit the form, the browser should try to validate the Title field.
    $submitButton->click();
    $this->assertTrue($titleField->isVisible(), 'Title is visible');
    $this->assertFalse($labelField->isVisible(), 'Label is not visible');

    // Populate the title and submit again. The label field should be displayed.
    $titleField->setValue($title);
    $submitButton->click();
    $this->assertTrue($labelField->isVisible(), 'Label is visible');

    // Populate the label field. We should be able to submit now.
    $labelField->setValue($this->randomMachineName());
    $submitButton->click();
    $this->assertTrue($this->assertSession()->waitForText(sprintf('Test Node Type %s has been created', $title)));
  }

}
