<?php

namespace Drupal\config_log\EventSubscriber;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigRenameEvent;
use Drupal\Core\Config\ConfigImporterEvent;
use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Datetime\TimeInterface;

/**
 * ConfigLog subscriber for configuration CRUD events.
 */
class ConfigLogDatabaseSubscriber extends ConfigLogSubscriberBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Time Object.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

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
  public static $type = 'custom';

  /**
   * Constructs the ConfigLogDatabaseSubscriber object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection object.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory services.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time object.
   */
  public function __construct(Connection $database, ConfigFactoryInterface $config_factory, AccountProxyInterface $current_user, TimeInterface $time) {
    parent::__construct($config_factory);
    $this->database = $database;
    $this->currentUser = $current_user;
    $this->time = $time;
    $this->flag_ignore_config_import = FALSE;
  }

  /**
   * Insert record to the database if the table exists.
   *
   * @param array $values
   *   The database values.
   *
   * @throws \Exception
   */
  protected function insertRecord(array $values) {
    // When uninstalling the module the table is removed before this code runs.
    // For now this check is there for all inserts, this can be improved later.
    if ($this->database->schema()->tableExists('config_log')) {
      $this->database
        ->insert('config_log')
        ->fields(array_keys($values))
        ->values($values)
        ->execute();
    }
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
   * React to configuration ConfigEvent::SAVE events.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The event to process.
   *
   * @throws \Exception
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

    $values = [
      'uid' => $this->currentUser->id(),
      'operation' => $config->isNew() ? 'create' : 'update',
      'name' => $config->getName(),
      'data' => $this->encode($config->get()),
      'originalData' => $this->encode($config->getOriginal()),
      'created' => $this->time->getCurrentTime(),
    ];
    $this->insertRecord($values);
  }

  /**
   * React to configuration ConfigEvent::RENAME events.
   *
   * @param \Drupal\Core\Config\ConfigRenameEvent $event
   *   The event to process.
   *
   * @throws \Exception
   */
  public function onConfigRename(ConfigRenameEvent $event) {
    if (!$this->isEnabled()) {
      return;
    }
    $config = $event->getConfig();
    if ($this->isIgnored($config->getName())) {
      return;
    }

    $values = [
      'uid' => $this->currentUser->id(),
      'operation' => 'rename',
      'name' => $config->getName(),
      'old_name' => $event->getOldName(),
      'data' => $this->encode($config->get()),
      'originalData' => $this->encode($config->getOriginal()),
      'created' => $this->time->getCurrentTime(),
    ];
    $this->insertRecord($values);
  }

  /**
   * React to configuration ConfigEvent::DELETE events.
   *
   * @param \Drupal\Core\Config\ConfigCrudEvent $event
   *   The event to process.
   *
   * @throws \Exception
   */
  public function onConfigDelete(ConfigCrudEvent $event) {
    if (!$this->isEnabled()) {
      return;
    }
    $config = $event->getConfig();
    if ($this->isIgnored($config->getName())) {
      return;
    }

    $values = [
      'uid' => $this->currentUser->id(),
      'operation' => 'delete',
      'name' => $config->getName(),
      'created' => $this->time->getCurrentTime(),
    ];
    $this->insertRecord($values);
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
            $this->insertRecord([
              'uid' => 1,
              'operation' => $operation,
              'name' => $config_name,
              'created' => $this->time->getCurrentTime(),
            ]);
          }
        },
        $config_names
      );
    }
  }

  /**
   * Registers the methods in this class that should be listeners.
   *
   * @return array
   *   An array of event listener definitions.
   */
  public static function getSubscribedEvents(): array {
    $events[ConfigEvents::SAVE][] = ['onConfigSave', 10];
    $events[ConfigEvents::DELETE][] = ['onConfigDelete', 10];
    $events[ConfigEvents::RENAME][] = ['onConfigRename', 10];
    $events[ConfigEvents::IMPORT][] = ['onConfigImport', 10];
    $events[ConfigEvents::IMPORT_VALIDATE][] = ['onConfigValidate', 10];

    return $events;
  }

}
