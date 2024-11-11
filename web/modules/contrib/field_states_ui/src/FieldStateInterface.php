<?php

declare(strict_types = 1);

namespace Drupal\field_states_ui;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the interface for field states.
 *
 * @see \Drupal\field_states_ui\Annotation\FieldState
 * @see \Drupal\field_states_ui\FieldStateBase
 * @see \Drupal\field_states_ui\FieldStateManager
 * @see plugin_api
 */
interface FieldStateInterface extends PluginInspectionInterface, ConfigurableInterface {

  /**
   * Applies a field state to the field widget's form element.
   *
   * @param mixed[] $states
   *   An array to hold states.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param mixed[] $context
   *   An associative array containing the following key-value pairs:
   *   - form: The form structure to which widgets are being attached. This may
   *     be a full form structure, or a sub-element of a larger form.
   *   - widget: The widget plugin instance.
   *   - items: The field values, as a
   *     \Drupal\Core\Field\FieldItemListInterface object.
   *   - delta: The order of this item in the array of sub-elements (0, 1, n).
   *   - default: A boolean indicating whether the form is being shown as a
   *     dummy form to set default values.
   * @param mixed[] $element
   *   The field widget form element as constructed by hook_field_widget_form().
   * @param string[] $parents
   *   The parents array from the element.
   *
   * @see \Drupal\Core\Field\WidgetBase::formSingleElement()
   * @see hook_field_widget_form_alter()
   *
   * @return bool
   *   TRUE on success. FALSE if unable to calculate the field state.
   */
  public function applyState(array &$states, FormStateInterface $form_state, array $context, array $element, array $parents = NULL): bool;

  /**
   * Returns a render array summarizing the configuration of the image effect.
   *
   * @return mixed[]
   *   A render array.
   */
  public function getSummary(): array;

  /**
   * Returns the field state label.
   *
   * @return string
   *   The field state label.
   */
  public function label(): string|MarkupInterface;

  /**
   * Returns the unique ID representing the field state.
   *
   * @return string
   *   The field state ID.
   */
  public function getUuid(): string|MarkupInterface;

}
