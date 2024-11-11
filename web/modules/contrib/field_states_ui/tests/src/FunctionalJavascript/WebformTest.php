<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Webform Reference Fields.
 *
 * @group field_states_ui
 */
class WebformTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'webform',
  ];

  /**
   * Tests the core field widgets for Boolean Fields.
   */
  public function testWebformField(): void {
    $field_name = 'field_webform';
    $label = 'Webform';
    $widgets = [
      'webform_entity_reference_select' => $this->fieldStateSettings,
      'webform_entity_reference_autocomplete' => $this->fieldStateSettings,
    ];
    $config = $this->createField('webform', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
