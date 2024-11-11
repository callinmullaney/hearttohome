<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Boolean Fields.
 *
 * @group field_states_ui
 */
class TelephoneTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'telephone',
  ];

  /**
   * Tests the core field widgets for Boolean Fields.
   */
  public function testTelephoneField(): void {
    $field_name = 'field_telephone';
    $label = 'Telephone';
    $widgets = [
      'telephone' => $this->fieldStateSettings,
    ];
    $config = $this->createField('telephone', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
