<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/9/16
 * Time: 3:23 PM
 */

namespace Drupal\simple_icontact\Service;


use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;

class SimpleIContactConfig {

  /**
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   */
  private $keyValueFactory;
  private $useCache;

  public function __construct(KeyValueFactoryInterface $keyValueFactory, $useCache) {
    $this->keyValueFactory = $keyValueFactory;
    $this->useCache = $useCache;
  }

  public function getLists() {

  }

}