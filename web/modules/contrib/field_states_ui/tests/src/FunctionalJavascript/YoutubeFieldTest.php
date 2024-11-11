<?php

declare(strict_types = 1);

namespace Drupal\Tests\field_states_ui\FunctionalJavascript;

/**
 * Test states on Color Field Fields.
 *
 * @group field_states_ui
 */
class YoutubeFieldTest extends FieldStateTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'field_states_ui',
    'youtube',
  ];

  /**
   * Tests field widgets for Inline Entity Form.
   */
  public function testYoutubeField(): void {
    $field_name = 'field_youtube';
    $label = 'Youtube';
    $widgets = [
      'youtube' => $this->fieldStateSettings,
    ];
    $config = $this->createField('youtube', $field_name, $label);
    foreach ($widgets as $widget => $settings) {
      $this->checkField($field_name, $label, $widget, $settings, $config);
      $this->preSubmitTest($field_name, 'youtube', $label, [], $config);
      $page = $this->getSession()->getPage();
      $page->fillField($label, 'https://www.youtube.com/watch?v=FuzTkGyxkYI');
      $page->pressButton('Save');
      $this->drupalGet('/node/1');
      $page = $this->getSession()->getPage();
      $page->has('css', 'iframe');
    }
  }

}
