<?php

namespace Drupal\Tests\config_log\Unit;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\MemoryBackend;
use Drupal\Core\Config\StorageInterface;

/**
 * An in-memory storage backend for unit testing.
 *
 * @class MemoryStorage
 */
class MemoryStorage implements StorageInterface {

  /**
   * The memory backend.
   *
   * @var \Drupal\Core\Cache\MemoryBackend
   */
  protected $data;

  /**
   * Construct a new MemoryStorage backend.
   */
  public function __construct(TimeInterface $time) {
    $this->data = new MemoryBackend($time);
  }

  /**
   * {@inheritdoc}
   */
  public function exists($name) {
    return (bool) $this->data->get($name);
  }

  /**
   * {@inheritdoc}
   */
  public function read($name) {
    return $this->data->get($name);
  }

  /**
   * {@inheritdoc}
   */
  public function readMultiple(array $names) {
    return $this->data->getMultiple($names);
  }

  /**
   * {@inheritdoc}
   */
  public function write($name, array $data) {
    $this->data->set($name, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($name) {
    $this->data->delete($name);
  }

  /**
   * {@inheritdoc}
   */
  public function rename($name, $new_name) {
    $this->data->set($new_name, $this->data->get($name));
    $this->data->delete($name);
  }

  /**
   * {@inheritdoc}
   */
  public function encode($data) {
    return serialize($data);
  }

  /**
   * {@inheritdoc}
   */
  public function decode($raw) {
    return unserialize($raw);
  }

  /**
   * The listAll method is not implemented.
   *
   * @throws \Exception
   */
  public function listAll($prefix = '') {
    throw new \Exception('Method not implemented');
  }

  /**
   * The deleteAll method is not implemented.
   *
   * @throws \Exception
   */
  public function deleteAll($prefix = '') {
    throw new \Exception('Method not implemented');
  }

  /**
   * The createCollection method is not implemented.
   *
   * @throws \Exception
   */
  public function createCollection($collection) {
    throw new \Exception('Method not implemented');
  }

  /**
   * The getAllCollectionNames method is not implemented.
   *
   * @throws \Exception
   */
  public function getAllCollectionNames() {
    throw new \Exception('Method not implemented');
  }

  /**
   * The getCollectionName method is not implemented.
   *
   * @throws \Exception
   */
  public function getCollectionName() {
    throw new \Exception('Method not implemented');
  }

}
