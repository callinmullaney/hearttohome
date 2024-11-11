<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on EntityReference Fields.
 *
 * @group field_states_ui
 */
class EntityReferenceTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'media',
  ];

  /**
   * Tests field widgets for ERR Fields.
   */
  public function testEntityReferenceField(): void {
    $field_name = 'field_entity_reference';
    $label = 'Default Entity Reference';
    $widgets = [
      'entity_reference_autocomplete' => $this->fieldStateSettings,
      'entity_reference_autocomplete_tags' => $this->fieldStateSettings,
      'options_buttons' => $this->fieldStateSettings,
      'options_select' => $this->fieldStateSettings,
    ];
    $config = $this->createField('entity_reference', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
      if ($widget === 'entity_reference_autocomplete') {
        $session = $this->assertSession();
        // Test field with unlimited values for ability to add another item.
        $config->getFieldStorageDefinition()->set('cardinality', -1)->save();
        $label .= " - unlimited";
        $config->set('label', $label)->save();
        $this->drupalGet("node/add/article");
        $session->pageTextNotContains($label);
        $page = $this->getSession()->getPage();
        $page->fillField('title[0][value]', 'Some value');
        $session->pageTextContains($label);
        $session->elementsCount('css', 'a.tabledrag-handle', 1);
        $page->pressButton('Add another item');
        $session->assertWaitOnAjaxRequest();
        $session->elementsCount('css', 'a.tabledrag-handle', 2);
      }
    }
  }

  /**
   * Tests field widgets for Paragraphs Fields.
   */
  public function testMediaField(): void {
    $field_name = 'field_entity_reference';
    $label = 'Media Entity Reference';
    $widgets = [
      'entity_reference_autocomplete' => $this->fieldStateSettings,
      'entity_reference_autocomplete_tags' => $this->fieldStateSettings,
      'media_library_widget' => $this->fieldStateSettings,
      'options_buttons' => $this->fieldStateSettings,
      'options_select' => $this->fieldStateSettings,
    ];
    $config = $this->createField('entity_reference', $field_name, $label);
    $config->getFieldStorageDefinition()->setSetting('target_type', 'media')->save();
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
