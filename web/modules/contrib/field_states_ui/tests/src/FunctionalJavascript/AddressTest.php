<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Address Fields.
 *
 * @group field_states_ui
 */
class AddressTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'address',
  ];

  /**
   * Tests the field widgets for Address Fields.
   */
  public function testAddressField(): void {
    $field_name = 'field_address';
    $label = 'Address';
    $widgets = [
      'address_default' => $this->fieldStateSettings,
    ];
    $config = $this->createField('address', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the field widgets for Country Fields.
   */
  public function testCountryField(): void {
    $field_name = 'field_address_country';
    $label = 'Country';
    $widgets = [
      'address_country_default' => $this->fieldStateSettings,
    ];
    $config = $this->createField('address_country', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the field widgets for Zone Fields.
   */
  public function testZoneField(): void {
    $field_name = 'field_address_zone';
    $label = 'Zone';
    $widgets = [
      'address_zone_default' => $this->fieldStateSettings,
    ];
    $config = $this->createField('address_zone', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
