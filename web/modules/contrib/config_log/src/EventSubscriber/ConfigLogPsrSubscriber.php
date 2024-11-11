<?php

namespace Drupal\config_log\EventSubscriber;

use Drupal\Component\Utility\DiffArray;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigImporterEvent;
use Psr\Log\LoggerInterface;
use Drupal\Component\Datetime\TimeInterface;

/**
 * Config subscriber.
 */
class ConfigLogPsrSubscriber extends ConfigLogSubscriberBase {

  /**
   * The log logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Time Object.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Flag to signal import operation.
   *
   * @var bool
   */
  protected $flag_ignore_config_import;

  /**
   * The type of the subscriber.
   *
   * @var string
   */
  public static $type = 'default';

  /**
   * {@inheritdoc}
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The log logger.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory services.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time object.
   */
  public function __construct(LoggerInterface $logger, ConfigFactoryInterface $config_factory, TimeInterface $time) {
    $this->logger = $logger;
    $this->time = $time;
    parent::__construct($config_factory);
    $this->flag_ignore_config_import = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events[ConfigEvents::SAVE][] = ['onConfigSave', 20];
    $events[ConfigEvents::DELETE][] = ['onConfigSave', 20];
    $events[ConfigEvents::IMPORT][] = ['onConfigImport', 20];
    $events[ConfigEvents::IMPORT_VALIDATE][] = ['onConfigValidate', 20];

    return $events;
  }

  /**
   * Using Config validate method to set flag_ignore_config_import to ignore log.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The config crud event.
   */
  public function onConfigValidate($event) {
    $this->flag_ignore_config_import = $this->isConfigImportIgnored();
  }

  /**
   * Save config.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The config crud event.
   */
  public function onConfigSave(ConfigCrudEvent $event) {
    if (!$this->isEnabled() || $this->flag_ignore_config_import === TRUE) {
      return;
    }

    $config = $event->getConfig();
    if ($this->isIgnored($config->getName())) {
      return;
    }

    // Check if ignoring config with no changes is enabled and ensure we do not
    // record the log if it is so.
    if ($this->isIgnoredNoChanges() && !$this->isChanged($config)) {
      return;
    }

    $diff = DiffArray::diffAssocRecursive($config->get(), $config->getOriginal());
    $this->logConfigChanges($config, $diff);
  }

  /**
   * The config change.
   *
   * @param \Drupal\Core\Config\Config $config
   *   The configuration.
   * @param array $diff
   *   The diff array.
   * @param string $subkey
   *   The config subkey.
   */
  protected function logConfigChanges(Config $config, array $diff, string $subkey = '') {
    foreach ($diff as $key => $value) {
      $full_key = $key;
      if ($subkey) {
        $full_key = $this->joinKey($subkey, $key);
      }

      if (is_array($value)) {
        $this->logConfigChanges($config, $diff[$key], $full_key);
      }
      else {
        $this->logger->info("Configuration changed: %key changed from %original to %value at %time", [
          '%key' => $this->joinKey($config->getName(), $full_key),
          '%original' => $this->format($config->getOriginal($full_key)),
          '%value' => $this->format($value),
          '%time' => $this->time->getCurrentTime(),
        ]);
      }
    }
  }

  /**
   * React to configuration ConfigEvent::IMPORT events.
   *
   * @param \Drupal\Core\Config\ConfigImporterEvent $event
   *   The event to process.
   */
  public function onConfigImport(ConfigImporterEvent $event) {
    if (!$this->isEnabled() || $this->isConfigImportIgnored()) {
      return;
    }

    // Get the changelist and insert records for each change if not ignored.
    foreach ($event->getChangelist() as $operation => $config_names) {
      array_map(
        function ($config_name) use ($operation) {
          if (!$this->isIgnored($config_name)) {
            $this->logger->info("Configuration %operation: %key", [
              '%key' => $config_name,
              '%operation' => $operation,
            ]);
          }
        },
        $config_names
      );
    }
  }

  /**
   * The format value.
   *
   * @param mixed $value
   *   The value.
   *
   * @return mixed
   *   Return format.
   */
  private function format($value) {
    if ($value === NULL) {
      return "NULL";
    }

    if ($value === "") {
      return '<empty string>';
    }

    if (is_bool($value)) {
      return ($value ? 'TRUE' : 'FALSE');
    }

    return $value;
  }

  /**
   * The join config keys.
   *
   * @param string $subkey
   *   The sub key.
   * @param string $key
   *   The key.
   *
   * @return string
   *   Merged key.
   */
  private function joinKey($subkey, $key) {
    return $subkey . '.' . $key;
  }

}
