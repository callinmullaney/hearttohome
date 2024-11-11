<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Geofield Fields.
 *
 * @group field_states_ui
 */
class GeofieldTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'geofield',
  ];

  /**
   * Tests the core field widgets for Geofield Fields.
   */
  public function testGeofieldField(): void {
    $field_name = 'field_geofield';
    $label = 'Geofield';
    $widgets = [
      'geofield_bounds' => $this->fieldStateSettings,
      'geofield_default' => $this->fieldStateSettings,
      'geofield_dms' => $this->fieldStateSettings,
      'geofield_latlon' => $this->fieldStateSettings,
    ];
    $config = $this->createField('geofield', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
