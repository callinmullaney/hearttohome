<?php

namespace Drupal\Tests\config_log\Unit;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\config_log\EventSubscriber\ConfigLogDatabaseSubscriber;
use Drupal\Core\Cache\MemoryBackend;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\TypedConfigManager;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * The group annotation is required for Drupal's UI to pick up the test.
 *
 * @group config_log
 */
class ConfigLogDatabaseSubscriberTest extends UnitTestCase {

  /**
   * Test that each subscribed event method exists.
   */
  public function testGetSubscribedEvents() {
    $events = ConfigLogDatabaseSubscriber::getSubscribedEvents();
    $this->assertNotEmpty($events, 'Subscriber is attached to at least one event');
    foreach ($events as $event => $subscribers) {
      foreach ($subscribers as $subscriber) {
        $this->assertTrue(method_exists('Drupal\config_log\EventSubscriber\ConfigLogDatabaseSubscriber', $subscriber[0]));
      }
    }
  }

  /**
   * Test that a configuration save event is logged.
   */
  public function testOnConfigSave() {
    $name = 'system.site';
    $data = ['name' => 'Drupal 8', '403' => '/403'];
    $config = $this->writableConfig($name, $data);
    $config->set('name', 'Drupal 9');
    $logger = $this->emitSaveEvent($config);

    $info = $logger->getEntries();

    $this->assertCount(1, $info);

    $this->assertEquals([
      'uid' => '1',
      'operation' => 'update',
      'name' => 'system.site',
      'data' => "name: 'Drupal 9'\n403: /403\n",
      'created' => 1686481721,
      'originalData' => "name: 'Drupal 8'\n403: /403\n",
    ], $info[0]['values']);
  }

  /**
   * Test that nested configuration objects are logged.
   */
  public function testNestedConfiguration() {
    $name = 'system.site';
    $data = ['page' => ['403' => '/403', '404' => '/404']];
    $config = $this->writableConfig($name, $data);
    $config->set('page.404', '/fourohfour');
    $logger = $this->emitSaveEvent($config);
    $info = $logger->getEntries();

    $this->assertEquals([
      'uid' => '1',
      'operation' => 'update',
      'name' => 'system.site',
      'data' => "page:\n  403: /403\n  404: /fourohfour\n",
      'created' => 1686481721,
      'originalData' => "page:\n  403: /403\n  404: /404\n",
    ], $info[0]['values']);
  }

  /**
   * Return a writable configuration object.
   *
   * @param string $name
   *   The name of the configuration, such as 'system.site'.
   * @param array $data
   *   An array of configuration data.
   *
   * @return \Drupal\Core\Config\Config
   *   A writable configuration object that responds to set() calls.
   */
  private function writableConfig(string $name, array $data) {
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
    $module_handler = $this->createMock('Drupal\Core\Extension\ModuleHandlerInterface');

    /** @var \Drupal\Core\DependencyInjection\ClassResolverInterface $class_resolver */
    $class_resolver = $this->createMock('Drupal\Core\DependencyInjection\ClassResolverInterface');

    $time = $this->prophesize(TimeInterface::class)->reveal();
    $typed_config = new TypedConfigManager(new MemoryStorage($time), new MemoryStorage($time), new MemoryBackend($time), $module_handler, $class_resolver);
    $config = new Config($name, new MemoryStorage($time), new EventDispatcher(), $typed_config);
    $config->initWithData($data);
    return $config;
  }

  /**
   * Emit a save event on a configuration object.
   *
   * @param \Drupal\Core\Config\Config $config
   *   The configuration to emit the event on.
   *
   * @return \Drupal\Tests\config_log\Unit\MockDatabase
   *   A test connection class.
   */
  private function emitSaveEvent(Config $config) {
    $event = new ConfigCrudEvent($config);
    $logger = new MockDatabase();

    // Create a mock of the AccountProxyInterface.
    $accountProxy = $this->createMock(AccountProxyInterface::class);

    // Mock the id() method.
    $accountProxy->expects($this->any())
      ->method('id')
      ->willReturn('1');

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $this->getConfigFactoryStub([
      'config_log.settings' => ['log_destination' => 0],
    ]);

    $time_interface = $this->createMock('Drupal\Component\Datetime\TimeInterface');
    $time_interface->method('getCurrentTime')
      ->willReturn(1686481721);

    $configLogger = new ConfigLogDatabaseSubscriber($logger, $config_factory, $accountProxy, $time_interface);
    $configLogger->onConfigSave($event);

    return $logger;
  }

}
