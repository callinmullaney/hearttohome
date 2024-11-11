<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Views Reference Fields.
 *
 * @group field_states_ui
 */
class ViewsReferenceTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   *
   * Views Reference modules doesn't have schema so need to disable checking.
   * See https://www.drupal.org/project/viewsreference/issues/2957529.
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'views',
    'viewsreference',
  ];

  /**
   * Tests the core field widgets for Views Reference Fields.
   */
  public function testViewsReferenceField(): void {
    $field_name = 'field_viewsreference';
    $label = 'Views Reference';
    $widgets = [
      'viewsreference_autocomplete' => $this->fieldStateSettings,
      'viewsreference_select' => $this->fieldStateSettings,
    ];
    $config = $this->createField('viewsreference', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
