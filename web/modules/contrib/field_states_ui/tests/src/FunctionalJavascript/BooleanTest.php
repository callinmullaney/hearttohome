<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Boolean Fields.
 *
 * @group field_states_ui
 */
class BooleanTest extends FieldStateTestBase {

  /**
   * Tests the core field widgets for Boolean Fields.
   */
  public function testBooleanField(): void {
    $field_name = 'field_boolean';
    $label = 'Boolean';
    $widgets = [
      'options_buttons' => $this->fieldStateSettings,
      'boolean_checkbox' => $this->fieldStateSettings,
    ];
    $config = $this->createField('boolean', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
