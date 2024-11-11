<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Select2 Fields.
 *
 * @group field_states_ui
 */
class Select2Test extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'options',
    'select2',
  ];

  /**
   * Tests the select2 field widget for list_float Fields.
   */
  public function testListFloatField(): void {
    $field_name = 'field_list_float';
    $label = 'List Float';
    $widgets = [
      'select2' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_float', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the select2 field widget for list_integer Fields.
   */
  public function testListIntegerField(): void {
    $field_name = 'field_list_integer';
    $label = 'List Integer';
    $widgets = [
      'select2' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_integer', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the select2 field widget for list_string Fields.
   */
  public function testListStringField(): void {
    $field_name = 'field_list_string';
    $label = 'List String';
    $widgets = [
      'select2' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_string', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the select2 field widget for entity_reference Fields.
   */
  public function testEntityReferenceField(): void {
    $field_name = 'field_entity_reference';
    $label = 'Entity Reference';
    $widgets = [
      'select2_entity_reference' => $this->fieldStateSettings,
    ];
    $config = $this->createField('entity_reference', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
