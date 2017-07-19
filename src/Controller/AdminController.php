<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/9/16
 * Time: 2:24 PM
 */

namespace Drupal\simple_icontact\Controller;

use Drupal\simple_icontact\Form\SimpleIcontactConfigForm;
use Drupal\simple_icontact\Service\SimpleIContactConfig;
use Drupal\Core\Controller\ControllerBase;
use Drupal\simple_icontact\Service\SimpleIContactLists;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends ControllerBase{

  private $lists;

  public function __construct(SimpleIContactLists $lists) {
    $this->lists = $lists;
  }


  public static function create(ContainerInterface $container) {
    $lists = $container->get('simple_icontact.lists');

    return new static($lists);
  }


}