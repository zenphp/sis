<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/12/16
 * Time: 2:10 PM
 */

namespace Drupal\simple_icontact\Service;


use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\Client;

class SimpleIcontactApi {

  const SANDBOX = 1;
  const PRODUCTION = 2;

  protected $config;

  public function __construct(ConfigFactoryInterface $config) {
    $this->config = $config;
  }

  protected function getAccountId() {

    $config = $this->config->get('simple_icontact.config');
    $accountId = $config->get('accountId');

    if ((!$accountId) || empty($accountId)) {
      $client = $this->authenticatedClient();

      $result = \GuzzleHttp\json_decode($client->request('GET', '/icp/a')->getBody());

      $accountId = $result->accounts[0]->accountId;

      $editConfig = $this->config->getEditable('simple_icontact.config');
      $editConfig->set('clientId', $accountId);
      $editConfig->save();
    }

    return $accountId;
  }

  protected function getClientFolderId() {
    $config = $this->config->get('simple_icontact.config');
    $folderId = $config->get('folderId');

    if ((!$folderId) || empty($folderId)) {
      $client = $this->authenticatedClient();
      $request_path = "/icp/a/" . $this->getAccountId() . "/c";
      $result = \GuzzleHttp\json_decode($client->request('GET', $request_path)->getBody());

      $folderId = $result->clientfolders[0]->clientFolderId;

      $editConfig = $this->config->getEditable('simple_icontact.config');
      $editConfig->set('folderId', $folderId);
      $editConfig->save();
    }
    return $folderId;
  }

  public function authenticatedClient() {
    $config = $this->config->get('simple_icontact.config');

    if ($config->get('endpoint') == self::SANDBOX) {
      $base_uri = 'https://app.sandbox.icontact.com';
    }
    else {
      $base_uri = '';
    }
    $client = new Client([
      'base_uri' => $base_uri,
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'API-Version' => '2.1',
        'API-AppId' => $config->get('api_key'),
        'API-Username' => $config->get('username'),
        'API-Password' => $config->get('password')
      ]
    ]);

    return $client;
  }
}