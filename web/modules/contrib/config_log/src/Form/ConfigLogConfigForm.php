<?php

namespace Drupal\config_log\Form;

use Drupal\config_log\EventSubscriber\ConfigLogDatabaseSubscriber;
use Drupal\config_log\EventSubscriber\ConfigLogMailSubscriber;
use Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements a ConfigLogConfig form.
 */
class ConfigLogConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'config_log_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['config_log.settings'];
  }

  /**
   * Config Log configuration form.
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config_log_conf = $this->configFactory->get('config_log.settings');

    $form['options']['log_destination'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Log destination'),
      '#options' => [
        ConfigLogDatabaseSubscriber::$type => $this->t('Custom table ("config_log")'),
        ConfigLogPsrSubscriber::$type => $this->t('Default logging system'),
        ConfigLogMailSubscriber::$type => $this->t('Mail notification'),
      ],
      '#default_value' => $config_log_conf->get('log_destination') ?? [],
      '#description' => $this->t('Select log destination. If none are selected, no destinations will be allowed.'),
    ];

    $form['options']['log_email_address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Notification e-mail address'),
      '#default_value' => $config_log_conf->get('log_email_address'),
      '#states' => [
        'visible' => [
          ':input[name="log_destination[mail]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['options']['ignore_config_import'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ignore logging on config import'),
      '#default_value' => $config_log_conf->get('ignore_config_import'),
      '#description' => $this->t('Select this if you do not want to log any changes when importing configuration.'),
    ];

    $form['options']['ignore_no_changes'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ignore logging if there are no changes to the configuration'),
      '#description' => $this->t('Select this if you do not want to log if there are no changes to the configuration.'),
      '#default_value' => $config_log_conf->get('ignore_no_changes'),
    ];

    $description = $this->t('One configuration name per line.<br />
Examples: <ul>
<li>user.settings</li>
<li>views.settings</li>
<li>contact.settings</li>
</ul>');

    $log_ignored_config = $config_log_conf->get('log_ignored_config');

    $form['options']['log_ignored_config'] = [
      '#type' => 'textarea',
      '#rows' => 25,
      '#title' => $this->t('Configuration entity names to ignore'),
      '#description' => $description,
      '#default_value' => (is_array($log_ignored_config) ? implode(PHP_EOL, $log_ignored_config) : ''),
      '#size' => 60,
    ];

    $form['options']['log_ignored_config_negate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Negate the condition'),
      '#description' => $this->t('Check this if you want log only configurations from the list above.'),
      '#default_value' => $config_log_conf->get('log_ignored_config_negate'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Config Log configuration form submit handler.
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('config_log.settings');

    $config->set('log_destination', $form_state->getValue('log_destination'));
    $config->set('log_email_address', $form_state->getValue('log_email_address'));
    $config->set('ignore_config_import', $form_state->getValue('ignore_config_import'));
    $config->set('ignore_no_changes', $form_state->getValue('ignore_no_changes'));

    $ignore_settings_array = preg_split("[\n|\r]", $form_state->getValue('log_ignored_config'));
    $ignore_settings_array = array_filter($ignore_settings_array);
    $config->set('log_ignored_config', $ignore_settings_array);
    $config->set('log_ignored_config_negate', $form_state->getValue('log_ignored_config_negate'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
