<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on File Fields.
 *
 * @group field_states_ui
 */
class FileTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'file',
    'image',
  ];

  /**
   * Tests the core field widget for File Fields.
   */
  public function testFileField(): void {
    $field_name = 'field_file';
    $label = 'File Field';
    $widgets = [
      'file_generic' => $this->fieldStateSettings,
    ];
    $config = $this->createField('file', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

  /**
   * Tests the core field widgets for Image Fields.
   */
  public function testImageField(): void {
    $field_name = 'field_image';
    $label = 'Image';
    $widgets = [
      'image_image' => $this->fieldStateSettings,
    ];
    $config = $this->createField('image', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
    }
  }

}
