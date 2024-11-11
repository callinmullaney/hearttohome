<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Inline Entity Form Widgets.
 *
 * @group field_states_ui
 */
class InlineEntityFormTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'inline_entity_form',
  ];

  /**
   * Tests field widgets for Inline Entity Form.
   */
  public function testInlineEntityFormWidgets(): void {
    $field_name = 'field_entity_reference';
    $label = 'Default Entity Reference';
    $widgets = [
      'ief_table_view_complex' => $this->fieldStateSettings,
      'inline_entity_form_complex' => $this->fieldStateSettings,
      'inline_entity_form_simple' => $this->fieldStateSettings,
    ];
    $config = $this->createField('entity_reference', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
