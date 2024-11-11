<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Chosen Fields.
 *
 * @group field_states_ui
 */
class ChosenTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'options',
    'chosen_field',
  ];

  /**
   * Tests the chosen field widget for list_float Fields.
   */
  public function testListFloatField(): void {
    $field_name = 'field_list_float';
    $label = 'List Float';
    $widgets = [
      'chosen' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_float', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the chosen field widget for list_integer Fields.
   */
  public function testListIntegerField(): void {
    $field_name = 'field_list_integer';
    $label = 'List Integer';
    $widgets = [
      'chosen' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_integer', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the chosen field widget for list_string Fields.
   */
  public function testListStringField(): void {
    $field_name = 'field_list_string';
    $label = 'List String';
    $widgets = [
      'chosen' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_string', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the chosen field widget for entity_reference Fields.
   */
  public function testEntityReferenceField(): void {
    $field_name = 'field_entity_reference';
    $label = 'Entity Reference';
    $widgets = [
      'chosen' => $this->fieldStateSettings,
    ];
    $config = $this->createField('entity_reference', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
