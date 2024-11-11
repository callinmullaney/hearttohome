<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on core String Fields.
 *
 * @group field_states_ui
 */
class StringTest extends FieldStateTestBase {

  /**
   * Tests the core field widgets for String Fields.
   */
  public function testStringField(): void {
    $field_name = 'field_string';
    $label = 'String - Short';
    $widgets = [
      'string_textfield' => $this->fieldStateSettings,
    ];
    $config = $this->createField('string', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for Long String Fields.
   */
  public function testStringLongField(): void {
    $field_name = 'field_string_long';
    $label = 'String - Long';
    $widgets = [
      'string_textarea' => $this->fieldStateSettings,
    ];
    $config = $this->createField('string_long', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
