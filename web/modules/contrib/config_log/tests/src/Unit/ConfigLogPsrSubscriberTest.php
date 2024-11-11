<?php

namespace Drupal\Tests\config_log\Unit;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber;
use Drupal\Core\Cache\MemoryBackend;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\TypedConfigManager;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The group annotation is required for Drupal's UI to pick up the test.
 *
 * @group config_log
 */
class ConfigLogPsrSubscriberTest extends UnitTestCase {

  /**
   * Test that each subscribed event method exists.
   *
   * @covers \Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber::getSubscribedEvents
   */
  public function testGetSubscribedEvents() {
    $events = ConfigLogPsrSubscriber::getSubscribedEvents();
    $this->assertNotEmpty($events, 'Subscriber is attached to at least one event');
    foreach ($events as $event => $subscribers) {
      foreach ($subscribers as $subscriber) {
        $this->assertTrue(method_exists('Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber', $subscriber[0]));
      }
    }
  }

  /**
   * Test that a configuration save event is logged.
   *
   * @covers \Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber::__construct
   * @covers \Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber::onConfigSave
   * @covers \Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber::logConfigChanges
   * @covers \Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber::joinKey
   */
  public function testOnConfigSave() {
    $name = 'system.site';
    $data = ['name' => 'Drupal 8', '403' => '/403'];
    $config = $this->writableConfig($name, $data);
    $config->set('name', 'Drupal 9');
    $logger = $this->emitSaveEvent($config);

    $info = $logger->getLogs('info');

    $this->assertCount(1, $info);
    $this->assertEquals('Configuration changed: %key changed from %original to %value at %time', $info[0]['message']);

    // Assert that each changed value logs the correct original and changed
    // value.
    $this->assertEquals([
      '%key' => 'system.site.name',
      '%original' => 'Drupal 8',
      '%value' => 'Drupal 9',
      '%time' => 1686481721,
    ], $info[0]['context']);
  }

  /**
   * Test that nested configuration objects are logged.
   *
   * @covers \Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber::logConfigChanges
   */
  public function testNestedConfiguration() {
    $name = 'system.site';
    $data = ['page' => ['403' => '/403', '404' => '/404']];
    $config = $this->writableConfig($name, $data);
    $config->set('page.404', '/fourohfour');
    $logger = $this->emitSaveEvent($config);
    $info = $logger->getLogs('info');

    $this->assertEquals([
      '%key' => 'system.site.page.404',
      '%original' => '/404',
      '%value' => '/fourohfour',
      '%time' => 1686481721,
    ], $info[0]['context']);
  }

  /**
   * @covers \Drupal\config_log\EventSubscriber\ConfigLogPsrSubscriber::format
   */
  public function testFormat() {
    $name = 'system.site';
    $data = ['403' => NULL, '404' => '', 500 => FALSE, 418 => "I'm a teapot"];
    $config = $this->writableConfig($name, $data);
    $config->set('403', '/403');
    $config->set('404', '/404');
    $config->set('500', '/500');
    $config->set('418', "No coffee here");
    $logger = $this->emitSaveEvent($config);
    $info = $logger->getLogs('info');

    $this->assertTrue(
      empty(array_diff_key(['%original' => "NULL"], $info[0]['context'])) && empty(array_diff_assoc(['%original' => "NULL"], $info[0]['context']))
    );
    $this->assertTrue(
      empty(array_diff_key(['%original' => '<empty string>'], $info[1]['context'])) && empty(array_diff_assoc(['%original' => '<empty string>'], $info[1]['context']))
    );
    $this->assertTrue(
      empty(array_diff_key(['%original' => "FALSE"], $info[2]['context'])) && empty(array_diff_assoc(['%original' => "FALSE"], $info[2]['context']))
    );
    $this->assertTrue(
      empty(array_diff_key(['%original' => "I'm a teapot"], $info[3]['context'])) && empty(array_diff_assoc(['%original' => "I'm a teapot"], $info[3]['context']))
    );
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
   * @return \Drupal\Tests\config_log\Unit\ContextLogger
   *   A logger that stores both messages and context variables.
   */
  private function emitSaveEvent(Config $config) {
    $event = new ConfigCrudEvent($config);
    $logger = new ContextLogger();

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $this->getConfigFactoryStub([
      'config_log.settings' => ['log_destination' => 0],
    ]);
    $time_interface = $this->createMock('Drupal\Component\Datetime\TimeInterface');
    $time_interface->method('getCurrentTime')
      ->willReturn(1686481721);

    $configLogger = new ConfigLogPsrSubscriber($logger, $config_factory, $time_interface);
    $configLogger->onConfigSave($event);

    return $logger;
  }

}
