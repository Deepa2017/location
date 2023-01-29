<?php

namespace Drupal\location\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\location\TimeService;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Location Block.
 *
 * @Block(
 * id = "location",
 * admin_label = @Translation("Location"),
 * category = @Translation("Custom")
 * )
 */
class LocationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The location service.
   *
   * @var location\Drupal\location\TimeService
   */
  protected $location;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Create Block object.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Container Interface.
   * @param array $configuration
   *   The factory COnfirguration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The Plugin definition.
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('location.time'),
      $container->get('config.factory'),
    );
  }

  /**
   * Construct function for Block class.
   *
   * @param array $configuration
   *   The factory COnfirguration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The Plugin definition.
   * @param \Drupal\location\TimeService $location
   *   The factory for configuration objects.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TimeService $location, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->location = $location;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $location_time = $this->location->getTime();
    $city = $this->configFactory->getEditable('location.settings')->get('city');
    $country = $this->configFactory->getEditable('location.settings')->get('country');

    $date = new DrupalDateTime($location_time);
    $day = $date->format('l, d F Y');

    $time = new DrupalDateTime($location_time);
    $display_time = $time->format('g:i a');
    $build = [
      '#theme' => 'location',
      '#data' => [
        'time' => $display_time,
        'date' => $day,
        'city' => $city,
        'country' => $country,
      ],
    ];
    return $build;
  }

}
