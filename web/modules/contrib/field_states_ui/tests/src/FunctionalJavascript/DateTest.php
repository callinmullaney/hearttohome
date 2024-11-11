<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Date Fields.
 *
 * @group field_states_ui
 */
class DateTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'datetime',
    'datetime_range',
  ];

  /**
   * Tests the core field widgets for Created Fields.
   */
  public function testCreatedField(): void {
    $field_name = 'field_created';
    $label = 'Created Field';
    $widgets = [
      'datetime_timestamp' => $this->fieldStateSettings,
    ];
    $config = $this->createField('created', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for Date Time Fields.
   */
  public function testDateRangeField(): void {
    $field_name = 'field_date_range';
    $label = 'Date Range';
    $widgets = [
      'daterange_datelist' => $this->fieldStateSettings,
      'daterange_default' => $this->fieldStateSettings,
    ];
    $config = $this->createField('daterange', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for Date Time Fields.
   */
  public function testDateTimeField(): void {
    $field_name = 'field_date_time';
    $label = 'Date Time';
    $widgets = [
      'datetime_datelist' => $this->fieldStateSettings,
      'datetime_default' => $this->fieldStateSettings,
    ];
    $config = $this->createField('datetime', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for Created Fields.
   */
  public function testTimestampField(): void {
    $field_name = 'field_timestamp';
    $label = 'Timestamp Field';
    $widgets = [
      'datetime_timestamp' => $this->fieldStateSettings,
    ];
    $config = $this->createField('timestamp', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
