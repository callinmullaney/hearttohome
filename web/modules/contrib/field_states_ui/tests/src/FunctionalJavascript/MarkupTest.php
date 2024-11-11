<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Markup Fields.
 *
 * @group field_states_ui
 */
class MarkupTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'markup',
  ];

  /**
   * Tests the field widgets for Markup Fields.
   */
  public function testMarkupField(): void {
    $field_name = 'field_markup';
    $label = 'Markup Field';
    $widgets = [
      'markup' => $this->fieldStateSettings,
    ];
    $config = $this->createField('markup', $field_name, $label);
    $config->set('settings', [
      'markup' => [
        'value' => 'Markup Field - markup - multiple',
        'format' => filter_fallback_format(),
      ],
    ])->save();
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
