<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on ERR Fields.
 *
 * @group field_states_ui
 */
class ErrTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'paragraphs',
    'layout_paragraphs',
  ];

  /**
   * Tests field widgets for ERR Fields.
   */
  public function testEntityReferenceRevisionField(): void {
    $field_name = 'field_err';
    $label = 'Default ERR';
    $widgets = [
      'entity_reference_revisions_autocomplete' => $this->fieldStateSettings,
      'options_buttons' => $this->fieldStateSettings,
      'options_select' => $this->fieldStateSettings,
    ];
    $config = $this->createField('entity_reference_revisions', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests field widgets for Paragraphs Fields.
   */
  public function testParagraphsField(): void {
    $field_name = 'field_err';
    $label = 'Paragraphs ERR';
    $widgets = [
      'entity_reference_paragraphs' => $this->fieldStateSettings,
      'layout_paragraphs' => $this->fieldStateSettings,
      'paragraphs' => $this->fieldStateSettings,
    ];
    $config = $this->createField('entity_reference_revisions', $field_name, $label);
    $config->getFieldStorageDefinition()->setSetting('target_type', 'paragraph')->save();
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
