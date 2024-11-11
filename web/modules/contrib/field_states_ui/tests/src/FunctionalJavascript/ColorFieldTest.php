<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Color Field Fields.
 *
 * @group field_states_ui
 */
class ColorFieldTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'color_field',
  ];

  /**
   * Tests field widgets for Inline Entity Form.
   */
  public function testColorField(): void {
    $field_name = 'field_color_field';
    $label = 'Color Field';
    $widgets = [
      'color_field_widget_box' => $this->fieldStateSettings,
      'color_field_widget_html5' => $this->fieldStateSettings,
      'color_field_widget_default' => $this->fieldStateSettings,
    ];
    // @todo consider how to test/if to test the 3rd party JS widgets
    // 'color_field_widget_spectrum' => $this->fieldStateSettings,
    // 'color_field_widget_grid' => $this->fieldStateSettings,
    $config = $this->createField('color_field_type', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
      // Uncheck opacity and test again.
      if ($widget === 'color_field_widget_html5') {
        $field_settings = $config->getSettings();
        $field_settings['opacity'] = 0;
        $config->set('settings', $field_settings);
        $this->checkField($field_name, $label, $widget, $settings, $config);
      }
    }
  }

}
