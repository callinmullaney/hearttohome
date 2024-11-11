<?php

namespace Drupal\Tests\config_log\Unit;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\config_log\EventSubscriber\ConfigLogMailSubscriber;
use Drupal\Core\Cache\MemoryBackend;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\TypedConfigManager;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * The group annotation is required for Drupal's UI to pick up the test.
 *
 * @group config_log
 */
class ConfigLogMailSubscriberTest extends UnitTestCase {

  /**
   * Test that each subscribed event method exists.
   */
  public function testGetSubscribedEvents() {
    $events = ConfigLogMailSubscriber::getSubscribedEvents();
    $this->assertNotEmpty($events, 'Subscriber is attached to at least one event');
    foreach ($events as $event => $subscribers) {
      foreach ($subscribers as $subscriber) {
        $this->assertTrue(method_exists('Drupal\config_log\EventSubscriber\ConfigLogMailSubscriber', $subscriber[0]));
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
      "module" => "system",
      "key" => "message",
      "to" => "test@test.com",
      "langcode" => "en",
      "params" => [
        "context" => [
          "subject" => "[Site name] Configuration change",
          "message" => "User ID: 1<br />Configuration changed: <em class=\"placeholder\">system.site.name</em> changed from <em class=\"placeholder\">Drupal 8</em> to <em class=\"placeholder\">Drupal 9</em> at <em class=\"placeholder\">formatteddate</em><br />",
        ],
      ],
      "reply" => NULL,
      "send" => TRUE,
    ], $info[0]);
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
      "module" => "system",
      "key" => "message",
      "to" => "test@test.com",
      "langcode" => "en",
      "params" => [
        "context" => [
          "subject" => "[Site name] Configuration change",
          "message" => "User ID: 1<br />Configuration changed: <em class=\"placeholder\">system.site.page.404</em> changed from <em class=\"placeholder\">/404</em> to <em class=\"placeholder\">/fourohfour</em> at <em class=\"placeholder\">formatteddate</em><br />",
        ],
      ],
      "reply" => NULL,
      "send" => TRUE,
    ], $info[0]);
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
    $logger = new MockMailManager();

    // Create a mock of the AccountProxyInterface.
    $accountProxy = $this->createMock(AccountProxyInterface::class);

    // Mock the id() method.
    $accountProxy->expects($this->any())
      ->method('id')
      ->willReturn('1');

    // Mock the LanguageInterface for the default language.
    $language = $this->createMock(LanguageInterface::class);
    $language->method('getId')->willReturn('en');

    // Mock the LanguageManagerInterface.
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getDefaultLanguage')->willReturn($language);

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $this->getConfigFactoryStub([
      'config_log.settings' => [
        'log_destination' => 0,
        'log_email_address' => 'test@test.com',
      ],
      'system.site' => [
        'name' => "Site name",
      ],
    ]);

    $time_interface = $this->createMock('Drupal\Component\Datetime\TimeInterface');
    $time_interface->method('getCurrentTime')
      ->willReturn(1686481721);

    $date_formatter = $this->createMock('Drupal\Core\Datetime\DateFormatterInterface');

    $date_formatter->method('format')
      ->willReturn('formatteddate');

    $configLogger = new ConfigLogMailSubscriber($logger, $config_factory, $languageManager, $accountProxy, $time_interface, $date_formatter);
    // Inject the mock service into the string translation trait.
    $configLogger->setStringTranslation($this->getStringTranslationStub());
    $configLogger->onConfigSave($event);

    return $logger;
  }

}
