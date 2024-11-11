<?php

namespace Drupal\config_log\EventSubscriber;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Yaml\Dumper;

/**
 * Config subscriber.
 */
abstract class ConfigLogSubscriberBase implements EventSubscriberInterface {
  /**
   * The Config.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * A shared YAML dumper instance.
   *
   * @var \Symfony\Component\Yaml\Dumper
   */
  protected $dumper;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory services.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Returns whether the subscriber is enabled.
   *
   * @return bool
   *   The config is enabled.
   */
  public function isEnabled() {
    $log_destination = $this->configFactory->get('config_log.settings')->get('log_destination');
    if (!empty($log_destination) && empty($log_destination[static::$type])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Returns whether the config import should be ignored.
   *
   * @return bool
   *   Config import logging status.
   */
  public function isConfigImportIgnored() {
    if ($this->configFactory->get('config_log.settings')->get('ignore_config_import')) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Returns whether the config_name is ignored.
   *
   * @param string $config_name
   *   The config name variable.
   *
   * @return bool
   *   Config is ignored.
   */
  public function isIgnored(string $config_name) {
    $ignored_entities = $this->configFactory->get('config_log.settings')->get('log_ignored_config');
    $log_ignored_config_negate = $this->configFactory->get('config_log.settings')->get('log_ignored_config_negate') ?? FALSE;

    if (!empty($ignored_entities)) {
      foreach ($ignored_entities as $ignore) {
        if (fnmatch($ignore, $config_name)) {
          return !$log_ignored_config_negate;
        }
      }
    }

    return $log_ignored_config_negate;
  }

  /**
   * Encode data as YAML.
   *
   * @param array $data
   *   The data.
   *
   * @return string
   *   Encoded string.
   *
   * @see: \Drupal\Core\Config\FileStorage:encode()
   */
  protected function encode(array $data): string {
    if (!isset($this->dumper)) {
      // Set Yaml\Dumper's default indentation for nested nodes/collections to
      // 2 spaces for consistency with Drupal coding standards.
      $this->dumper = new Dumper(2);
    }
    // The level where you switch to inline YAML is set to PHP_INT_MAX to ensure
    // this does not occur. Also set the exceptionOnInvalidType parameter to
    // TRUE, so exceptions are thrown for an invalid data type.
    return $this->dumper->dump($data, PHP_INT_MAX, 0, TRUE);
  }

  /**
   * Returns whether the config is changed.
   *
   * @param \Drupal\Core\Config\Config $config
   *   The config name variable.
   *
   * @return bool
   *   Config is changed.
   */
  public function isChanged(Config $config): bool {
    $originalData = $this->encode($config->getOriginal());
    $newData = $this->encode($config->get());

    // Compare old and new values.
    return $originalData != $newData;
  }

  /**
   * Returns whether the config with no changes should be ignored.
   *
   * @return bool
   *   Config with no changes logging status.
   */
  public function isIgnoredNoChanges(): bool {
    if ($this->configFactory->get('config_log.settings')->get('ignore_no_changes')) {
      return TRUE;
    }
    return FALSE;
  }

}
