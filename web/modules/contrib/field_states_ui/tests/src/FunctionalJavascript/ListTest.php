<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Boolean Fields.
 *
 * @group field_states_ui
 */
class ListTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'options',
  ];

  /**
   * Tests the core field widgets for List - Float Fields.
   */
  public function testListFloatField(): void {
    $field_name = 'field_list_float';
    $label = 'List - Float';
    $widgets = [
      'options_buttons' => $this->fieldStateSettings,
      'options_select' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_float', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for List - Integer Fields.
   */
  public function testListIntegerField(): void {
    $field_name = 'field_list_integer';
    $label = 'List - Integer';
    $widgets = [
      'options_buttons' => $this->fieldStateSettings,
      'options_select' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_integer', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for List - String Fields.
   */
  public function testListStringField(): void {
    $field_name = 'field_list_string';
    $label = 'List - String';
    $widgets = [
      'options_buttons' => $this->fieldStateSettings,
      'options_select' => $this->fieldStateSettings,
    ];
    $config = $this->createField('list_string', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
