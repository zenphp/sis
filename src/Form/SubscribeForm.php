<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/12/16
 * Time: 1:44 PM
 */

namespace Drupal\simple_icontact\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\simple_icontact\Service\SimpleIContactLists;
use Drupal\simple_icontact\Service\SimpleIcontactSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubscribeForm extends FormBase {
  /**
   * @var \Drupal\simple_icontact\Service\SimpleIContactLists
   */
  private $lists;
  /**
   * @var \Drupal\simple_icontact\Service\SimpleIcontactSubscriber
   */
  private $subscriber;

  public function getFormId() {
    return 'simple_icontact_subscribe_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('simple_icontact.config');

    $form['email'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE
    );

    $form['firstName'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#required' => TRUE
    );

    $form['lastName'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#required' => TRUE,
    );

    $form['business'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Organization')
    );

    $form['street'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Address 1')
    );

    $form['street2'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Address 2')
    );

    $form['city'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('City')
    );

    $form['state'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('State/Province')
    );

    $form['postalCode'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Zip/Postal Code')
    );

    $form['country'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Country'),
      '#required' => TRUE
    );

    $form['list_wrapper'] = array(
      '#type' => 'fieldset',
      '#collapsable' => FALSE
    );

    $available_lists = $this->lists->getListFormOptions($config->get('default_lists'));
    $form['list_wrapper']['lists'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('I would like to recieve updates from:'),
      '#options' => $available_lists,
      '#required' => TRUE
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Sign up'
    );

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $subscriber_data = $form_state->getValues();

    $redirect = $this->subscriber->saveContact($subscriber_data, $form_state->getValue('lists'));

    $config = $this->config('simple_icontact.config');
    if ($redirect && $config->get('redirectPath')) {
      $redirectUrl = Url::fromUserInput($config->get('redirectPath'));
      $form_state->setRedirectUrl($redirectUrl);
    }
  }

  public static function create(ContainerInterface $container) {
    $lists = $container->get('simple_icontact.lists');
    $subscriber = $container->get('simple_icontact.subscriber');
    return new static($lists, $subscriber);
  }

  public function __construct(SimpleIContactLists $lists, SimpleIcontactSubscriber $subscriber) {

    $this->lists = $lists;
    $this->subscriber = $subscriber;
  }


}