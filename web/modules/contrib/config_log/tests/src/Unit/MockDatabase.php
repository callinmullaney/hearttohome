<?php

namespace Drupal\Tests\config_log\Unit;

use Drupal\Core\Database\Connection;

/**
 * Mimics a database connection for unit testing.
 */
class MockDatabase extends Connection {

  /**
   * Array of database entries.
   *
   * @var array
   */
  protected $entries = [];

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function schema() {
    return new class {

      /**
       * {@inheritdoc}
       */
      public function tableExists($table) {
        // Return true for config_log table and false for all others.
        return $table == 'config_log';
      }

    };
  }

  /**
   * {@inheritdoc}
   */
  public function insert($table, array $options = []) {
    return new class($this, $table) {

      /**
       * Mock database connection instance.
       *
       * @var MockDatabase
       */
      protected $connection;

      /**
       * Table where insertion is happening.
       *
       * @var string
       */
      protected $table;

      /**
       * Fields for the insertion.
       *
       * @var array
       */
      protected $fields = [];

      /**
       * Values for the fields.
       *
       * @var array
       */
      protected $values = [];

      /**
       * {@inheritdoc}
       */
      public function __construct($connection, $table) {
        $this->connection = $connection;
        $this->table = $table;
      }

      /**
       * {@inheritdoc}
       */
      public function fields(array $fields, array $values = []) {
        if (empty($values)) {
          $this->fields = $fields;
        }
        else {
          $this->fields = array_combine($fields, $values);
        }
        return $this;
      }

      /**
       * {@inheritdoc}
       */
      public function values(array $values) {
        $this->values = $values;
        return $this;
      }

      /**
       * {@inheritdoc}
       */
      public function execute() {
        $this->connection->addEntry($this->table, $this->fields, $this->values);
      }

    };
  }

  /**
   * Add an entry to the entries array.
   *
   * @param string $table
   *   The database table.
   * @param array $fields
   *   The fields to be inserted.
   * @param array $values
   *   The values to be inserted.
   */
  public function addEntry($table, array $fields, array $values) {
    $this->entries[] = [
      'table' => $table,
      'fields' => $fields,
      'values' => $values,
    ];
  }

  /**
   * Return all saved database entries.
   *
   * @return array
   *   An array of database entries.
   */
  public function getEntries() {
    return $this->entries;
  }

  /**
   * {@inheritdoc}
   */
  public function query($query, array $args = [], $options = []) {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function queryRange($query, $from, $count, array $args = [], array $options = []) {
    // Implement this as a stub.
  }

  public function upsert($table, array $options = []) {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function queryTemporary($query, array $args = [], array $options = []) {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function createDatabase($database) {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function driver() {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function databaseType() {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public static function open(array &$connection_options = []) {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function mapConditionOperator($operator) {
    // Implement this as a stub.
  }

  /**
   * {@inheritdoc}
   */
  public function nextId($existing_id = 0) {
    // Implement this as a stub.
  }

}
