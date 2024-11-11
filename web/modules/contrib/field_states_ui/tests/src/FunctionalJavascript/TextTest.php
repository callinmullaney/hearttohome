<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Text Fields.
 *
 * @group field_states_ui
 */
class TextTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'text',
  ];

  /**
   * Tests the core field widgets for Text (plain, long) Fields.
   */
  public function testTextLongField(): void {
    $field_name = 'field_text_long';
    $label = 'Text - Long';
    $widgets = [
      'string_textarea' => $this->fieldStateSettings,
    ];
    $config = $this->createField('text_long', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for Text (plain) Fields.
   */
  public function testTextField(): void {
    $field_name = 'field_text';
    $label = 'Text - Short';
    $widgets = [
      'text_textfield' => $this->fieldStateSettings,
    ];
    $config = $this->createField('text', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for Text with summary Fields.
   */
  public function testTextSummaryField(): void {
    $field_name = 'field_text_summary';
    $label = 'Text - With Summary';
    $widgets = [
      'text_textarea_with_summary' => $this->fieldStateSettings,
    ];
    $config = $this->createField('text_with_summary', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
