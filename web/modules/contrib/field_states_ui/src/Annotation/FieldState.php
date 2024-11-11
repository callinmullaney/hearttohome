<?php

declare(strict_types = 1);

namespace Drupal\field_states_ui\Annotation;

use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

/**
 * Defines a field state annotation object.
 *
 * Plugin Namespace: Plugin\FieldState.
 *
 * For a working example, see
 * \Drupal\field_states_ui\Plugin\FieldState\Visible
 *
 * @see \Drupal\field_states_ui\FieldStateInterface
 * @see \Drupal\field_states_ui\FieldStateBase
 * @see \Drupal\field_states_ui\FieldStateManager
 * @see plugin_api
 *
 * @Annotation
 */
class FieldState extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public string $id;

  /**
   * The human-readable name of the field state.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public Translation $label;

  /**
   * Optional: a brief description of the field state.
   *
   * This will be shown when adding or configuring this field state.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public Translation $description;

}
