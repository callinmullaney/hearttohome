<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Email Fields.
 *
 * @group field_states_ui
 */
class EmailTest extends FieldStateTestBase {

  /**
   * Tests the core field widgets for Email Fields.
   */
  public function testEmailField(): void {
    $field_name = 'field_email';
    $label = 'Email Test Field';
    $widgets = [
      'email_default' => $this->fieldStateSettings,
    ];
    $config = $this->createField('email', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
