<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states with Smart Date widgets/fields.
 *
 * @group field_states_ui
 */
class SmartDateTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'datetime_range',
    'smart_date',
  ];

  /**
   * Tests the Smart Date field widgets for core Date Range Fields.
   */
  public function testDateRangeField(): void {
    $field_name = 'field_date_range';
    $label = 'Date Range';
    $widgets = [
      'smartdate_only' => $this->fieldStateSettings,
      'smartdate_default' => $this->fieldStateSettings,
      'smartdate_inline' => $this->fieldStateSettings,
    ];
    $config = $this->createField('daterange', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the field widgets for Smart Date's Date Range Fields.
   */
  public function testSmartDateRangeField(): void {
    $field_name = 'field_date_range';
    $label = 'Smart Date Range';
    $widgets = [
      'smartdate_only' => $this->fieldStateSettings,
      'smartdate_default' => $this->fieldStateSettings,
      'smartdate_inline' => $this->fieldStateSettings,
      // 'smartdate_datelist' => $this->fieldStateSettings,
      // @todo Investigate causes a JS error.
      'smartdate_timezone' => $this->fieldStateSettings,
    ];
    $config = $this->createField('smartdate', $field_name, $label);
    $config->set('default_value', [
      [
        'default_duration' => 60,
        'default_duration_increments' => "30\r\n60|1 hour\r\n90\r\n120|2 hours\r\ncustom",
        'default_date_type' => '',
        'min' => '',
        'max' => '',
      ],
    ])->save();
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
