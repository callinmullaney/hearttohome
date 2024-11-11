<?php

declare(strict_types = 1);

namespace Drupal\field_states_ui;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for field staes.
 *
 * @see \Drupal\field_states_ui\Annotation\FieldState
 * @see \Drupal\field_states_ui\FieldStateInterface
 * @see \Drupal\field_states_ui\FieldStateManager
 * @see plugin_api
 */
abstract class FieldStateBase extends PluginBase implements FieldStateInterface, ContainerFactoryPluginInterface {

  /**
   * The field state ID.
   *
   * @var string
   */
  protected string $uuid;

  /**
   * The Uuid Service.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected UuidInterface $uuidService;

  /**
   * The entityFieldManager Service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, UuidInterface $uuid_service, EntityFieldManagerInterface $entity_field_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->uuidService = $uuid_service;
    $this->setConfiguration($configuration);
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function applyState(array &$states, FormStateInterface $form_state, array $context, array $element, array $parents = NULL): bool {
    if (empty($this->configuration['target']) || empty($this->configuration['comparison'])) {
      return FALSE;
    }
    $target_field = $form_state->getFormObject()
      ->getFormDisplay($form_state)
      ->getComponent($this->configuration['target']);
    // If dealing with a field on an Inline Entity Form or a Field Collection
    // have to include the field parents in the selector.
    if (!empty($parents)) {
      $target = array_shift($parents) . '[' . implode('][', $parents) . '][' . $this->configuration['target'] . ']';
    }
    else {
      $target = $this->configuration['target'];
    }
    switch ($target_field['type']) {
      case 'options_select':
        $selector = "select[name^='{$target}']";
        break;

      default:
        $selector = ":input[name^='{$target}']";
        break;
    }

    if ($this->configuration['comparison'] === 'value') {
      $value = $this->configuration['value'];
    }
    else {
      $value = TRUE;
    }
    $states[$this->pluginDefinition['id']][] = [
      $selector => [
        $this->configuration['comparison'] => $value,
      ],
    ];

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary(): array {
    return [
      '#theme' => 'field_states_ui_summary',
      '#data' => $this->configuration,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function label(): string|MarkupInterface {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getUuid(): string|MarkupInterface {
    return $this->uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return [
      'uuid' => $this->getUuid(),
      'id' => $this->getPluginId(),
      'data' => $this->configuration,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $configuration += [
      'data' => [],
      'uuid' => '',
    ];
    $this->configuration = $configuration['data'] + $this->defaultConfiguration();
    if (!isset($this->configuration['value'])) {
      $this->configuration['value'] = TRUE;
    }
    $this->uuid = $configuration['uuid'] ?? $this->uuidService->generate();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigurationForForm() {
    $form = [];
    foreach ($this->configuration as $key => $value) {
      $form[$key] = [
        '#type' => 'hidden',
        '#value' => $value,
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'value' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration([
      'data' => $form_state->getValues(),
      'uuid' => $this->uuid,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $display = $form_state->getFormObject()->getEntity();
    $fields = [];
    $definitions = $this->entityFieldManager->getFieldDefinitions($display->getTargetEntityTypeId(), $display->getTargetBundle());
    $current_field = $form_state->get('field_states_ui_edit');
    foreach (array_keys($display->getComponents()) as $name) {
      if (!isset($definitions[$name]) || $name === $current_field) {
        continue;
      }
      $fields[$name] = $definitions[$name]->getLabel();
    }
    asort($fields, SORT_NATURAL | SORT_FLAG_CASE);

    $form['target'] = [
      '#type' => 'select',
      '#title' => t('Target'),
      '#description' => t('The field to run a comparison on'),
      '#required' => TRUE,
      '#other' => t('Other element on the page'),
      '#other_description' => t('Should be a valid jQuery style element selector.'),
      '#options' => $fields,
      '#default_value' => $this->configuration['target'] ?? '',
    ];
    $form['comparison'] = [
      '#type' => 'select',
      '#title' => t('Comparison Type'),
      '#options' => [
        'empty' => 'empty',
        'filled' => 'filled',
        'checked' => 'checked',
        'unchecked' => 'unchecked',
        'expanded' => 'expanded',
        'collapsed' => 'collapsed',
        'value' => 'value',
      ],
      '#default_value' => $this->configuration['comparison'] ?? '',
    ];
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => t('Value'),
      '#default_value' => $this->configuration['value'] ?? '',
      '#states' => [
        'visible' => [
          'select[name$="[comparison]"]' => ['value' => 'value'],
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('uuid'),
      $container->get('entity_field.manager')
    );
  }

}
