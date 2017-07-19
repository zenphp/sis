<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/9/16
 * Time: 2:43 PM
 */

namespace Drupal\simple_icontact\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\simple_icontact\Service\SimpleIcontactApi;


class SimpleIcontactConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "SimpleIcontactConfigForm";
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('simple_icontact.config');

    $form['endpoint'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Select server'),
      '#options' => [
        SimpleIcontactApi::SANDBOX => 'Sandbox Server',
        SimpleIcontactApi::PRODUCTION => 'Production Server'
      ],
      '#default_value' => $config->get('endpoint')
    );

    $form['api_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('API AppId'),
      '#default_value' => $config->get('api_key'),
      '#required' => TRUE,
    );

    $form['username'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('API Username'),
      '#default_value' => $config->get('username'),
      '#required' => TRUE
    );

    $form['password'] = array(
      '#type' => 'password',
      '#title' => $this->t('API Password'),
      '#default_value' => $config->get('password'),
      '#required' => TRUE
    );

    $form['redirectPath'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Redirection Path'),
      '#default_value' => $config->get('redirectPath')
    );

    $clientId = $config->get('clientId');

    if ($clientId && !empty($clientId)) {
      $form['client_id'] = array(
        '#type' => '#markup',
        '#markup' => '<p><strong>Client Id: </strong> ' . $clientId . '</p>'
      );
    }

    $folderId = $config->get('folderId');
    if ($folderId && !empty($folderId)) {
      $form['client_id'] = array(
        '#type' => '#markup',
        '#markup' => '<p><strong>Folder Id: </strong> ' . $folderId . '</p>'
      );
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $factory = \Drupal::service('config.factory');
    $config = $factory->getEditable('simple_icontact.config');

    $config->set('api_key', $form_state->getValue('api_key'));
    $config->set('username', $form_state->getValue('username'));
    $config->set('password', $form_state->getValue('password'));
    $config->set('redirectPath', $form_state->getValue('redirectPath'));

    $config->save();
    parent::submitForm($form, $form_state);
  }

  protected function getEditableConfigNames() {
    return [
      'simple_icontact.config'
    ];
  }
}