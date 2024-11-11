<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Link Fields.
 *
 * @group field_states_ui
 */
class LinkTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'link',
  ];

  /**
   * Tests the core field widgets for Link Fields.
   */
  public function testLinkField(): void {
    $field_name = 'field_link';
    $label = 'Link Tester';
    $widgets = [
      'link_default' => $this->fieldStateSettings,
    ];
    $config = $this->createField('link', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
