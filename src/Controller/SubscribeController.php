<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/12/16
 * Time: 9:46 AM
 */

namespace Drupal\simple_icontact\Controller;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubscribeController extends ControllerBase  {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $config;

  public function __construct(ConfigFactoryInterface $config) {
    $this->config = $config;
  }

  public static function create(ContainerInterface $container) {
    $lists = $container->get('simple_incontact.lists');
    
    return new static($lists);
  }

  public function subscribe() {
    $config = $this->config->get('simple_icontact.config');
    $api = $config->get('api_key');
    $pass = $config->get('password');

  }

}