<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on core Numeric fields.
 *
 * @group field_states_ui
 */
class NumericTest extends FieldStateTestBase {

  /**
   * Tests the core field widget for float Fields.
   */
  public function testFloatField(): void {
    $field_name = 'field_float';
    $label = 'Float';
    $widgets = [
      'number' => $this->fieldStateSettings,
    ];
    $config = $this->createField('float', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widget for Decimal fields.
   */
  public function testDecimalField(): void {
    $field_name = 'field_decimal';
    $label = 'Decimal';
    $widgets = [
      'number' => $this->fieldStateSettings,
    ];
    $config = $this->createField('decimal', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widget for Integer fields.
   */
  public function testIntegerField(): void {
    $field_name = 'field_integer';
    $label = 'Integer';
    $widgets = [
      'number' => $this->fieldStateSettings,
    ];
    $config = $this->createField('integer', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
