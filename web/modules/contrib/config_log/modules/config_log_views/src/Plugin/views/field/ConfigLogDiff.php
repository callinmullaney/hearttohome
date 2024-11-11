<?php

namespace Drupal\config_log_views\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\Component\Diff\Diff;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("config_log_diff")
 */
class ConfigLogDiff extends FieldPluginBase {

  /**
   * The current display.
   *
   * @var string
   *   The current display of the view.
   */
  protected $currentDisplay;

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->currentDisplay = $view->current_display;
  }

  public function clickSort($order) {

  }

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    // First check whether the field should be hidden if the value(hide_alter_empty = TRUE) /the rewrite is empty (hide_alter_empty = FALSE).
    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {

    $length = 0;
    $newValue = ($values->config_log_data) ? explode("\n", $values->config_log_data) : [];
    $originalValue = ($values->config_log_originaldata) ? explode("\n", $values->config_log_originaldata) : [];

    $diff = new Diff($originalValue, $newValue);
    $diffFormatter = \Drupal::service('diff.formatter');
    $diffFormatter->show_header = FALSE;
    $diffFormatter->leading_context_lines = 0;
    $diffFormatter->trailing_context_lines = 0;
    $output = $diffFormatter->format($diff);
    if ($output) {
      // Add the CSS for the inline diff.
      $form['#attached']['library'][] = 'system/diff';
      // Lets check the length of the difference.
      if (is_array($output) && is_countable($output)) {
        $length = count($output);
      }
      // Collapse the field(s) if length is more than x rows.
      if ($length > 7) {
        $form['diff'] = [
          '#type' => 'details',
          '#title' => t('Text too long to display, expand for a full view'),
          '#open' => FALSE,
        ];
        $form['diff']['details'] = [
          '#type' => 'table',
          '#attributes' => [
            'class' => ['diff'],
          ],
          '#header' => [
            ['data' => t('From'), 'colspan' => '2'],
            ['data' => t('To'), 'colspan' => '2'],
          ],
          '#rows' => $output,
        ];
      }
      else {
        $form['diff'] = [
          '#type' => 'table',
          '#attributes' => [
            'class' => ['diff'],
          ],
          '#header' => [
            ['data' => t('From'), 'colspan' => '2'],
            ['data' => t('To'), 'colspan' => '2'],
          ],
          '#rows' => $output,
        ];
      }

      return $form;
    }
    else {
      $output = t('No change');
    }

    return $output;
  }

}
