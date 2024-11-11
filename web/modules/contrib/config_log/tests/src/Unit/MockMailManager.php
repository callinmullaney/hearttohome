<?php

namespace Drupal\Tests\config_log\Unit;

use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Mocks the MailManager for unit testing.
 */
class MockMailManager implements MailManagerInterface {
  /**
   * Array of mail entries.
   *
   * Each entry is an array.
   *
   * @var array
   */
  protected $entries = [];

  /**
   * {@inheritdoc}
   */
  public function mail($module, $key, $to, $langcode, $params = [], $reply = NULL, $send = TRUE) {
    // Converting any TranslatableMarkup instances into strings.
    foreach ($params["context"] as $key => $param) {
      if ($param instanceof TranslatableMarkup) {
        $params["context"][$key] = $param->render();
      }
    }

    // Store the parameters in the entries array.
    $this->entries[] = [
      'module' => $module,
      'key' => $key,
      'to' => $to,
      'langcode' => $langcode,
      'params' => $params,
      'reply' => $reply,
      'send' => $send,
    ];

    return TRUE;
  }

  /**
   * Returns the saved mail entries.
   *
   * @return array
   *   An array of mail entries.
   */
  public function getEntries() {
    return $this->entries;
  }

  /**
   * {@inheritdoc}
   */
  public function getInstance(array $options) {
    // As this is a mock, we do not need to implement this method.
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE) {
    // As this is a mock, we do not need to implement this method.
  }

  /**
   * {@inheritdoc}
   */
  public function hasDefinition($plugin_id) {
    // As this is a mock, we do not need to implement this method.
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    // As this is a mock, we do not need to implement this method.
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = []) {
    // As this is a mock, we do not need to implement this method.
  }

}
