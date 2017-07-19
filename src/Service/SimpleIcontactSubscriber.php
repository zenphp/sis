<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/12/16
 * Time: 1:43 PM
 */

namespace Drupal\simple_icontact\Service;


class SimpleIcontactSubscriber extends SimpleIcontactApi {
  protected $config;

  public function getContactIdByEmail($email) {
    $client = $this->authenticatedClient();

    $request_url = '/icp/a/' . $this->getAccountId() . '/c/' . $this->getClientFolderId() . '/contacts';
    $response = $client->request('GET', $request_url, [
      'query' => [
        'email' => $email
      ]
    ]);
    $contacts = \GuzzleHttp\json_decode($response->getBody());

    if ($contacts->total > 0) {
      $contactId = $contacts->contacts[0]->contactId;
    }
    else {
      $contactId = FALSE;
    }

    return $contactId;
  }

  public function saveContact($formValues, $subscriptions) {
    $redirect = FALSE;

    $email = $formValues['email'];

    $data = [];
    $form_allowed_fields = [
      'email',
      'firstName',
      'lastName',
      'business',
      'street',
      'street2',
      'city',
      'state',
      'postalCode',
      'country'
    ];

    foreach ($form_allowed_fields as $key) {
      if ($key != 'country') {
        if ($formValues[$key] && !empty($formValues[$key])) {
          $data[$key] = $formValues[$key];
        }
      }
    }

    $method = 'POST';
    $request_url = "/icp/a/" . $this->getAccountId() . "/c/" . $this->getClientFolderId() . "/contacts";

    $contactId = $this->getContactIdByEmail($email);

    if ($contactId) {
      $request_url .= "/" . $contactId;
      $data['contactId'] = $contactId;
    }
    $data['status'] = 'normal';


    $client = $this->authenticatedClient();

    $postData = [
      'json' => array($data)
    ];
    $response = $client->request($method, $request_url, $postData);

    if ($response->getStatusCode() == 200) {
      $data = \GuzzleHttp\json_decode($response->getBody());
      if (!empty($data->contacts)) {
        $newContactId = $data->contacts[0]->contactId;
      }
      elseif (!empty($data->contact)) {
        $newContactId = $data->contact->contactId;
      }
      else {
        $newContactId = FALSE;
      }
      if ($newContactId) {
        foreach ($subscriptions as $key => $val) {
          $request_url = '/icp/a/' . $this->getAccountId() . '/c/' . $this->getClientFolderId() . '/subscriptions';

          $subData = [];
          $subData['listId'] = $val;
          $subData['contactId'] = $newContactId;
          $subData['status'] = 'normal';

          $client = $this->authenticatedClient();
          $response = $client->request('POST', $request_url, [
            'json' => array($subData)
          ]);
          if ($response->getStatusCode() != 200) {
            drupal_set_message('Sorry, there was an error subscribing you to the selected mailing lists.  Please try again later.',
              'error');
          }
          else {
            drupal_set_message("Your subscription request is being processed.","status");
            $redirect = TRUE;
          }
        }
      }
    }

    return $redirect;

  }
}