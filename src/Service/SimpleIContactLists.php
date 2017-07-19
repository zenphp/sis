<?php
/**
 * Created by PhpStorm.
 * User: jmurray
 * Date: 12/12/16
 * Time: 9:56 AM
 */

namespace Drupal\simple_icontact\Service;


class SimpleIContactLists extends SimpleIcontactApi {


  Const CACHED = 0;
  Const REFRESH = 1;


  public function fetchLists($refresh) {

    $data = NULL;

    if (!$refresh && ($cache = \Drupal::cache()
        ->get('simple_icontact_lists'))
    ) {
      $data = $cache->data;
    }
    else {
      $client = $this->authenticatedClient();
      $request_path = '/icp/a/' . $this->getAccountId() . '/c/' . $this->getClientFolderId() . '/lists';
      $response = $client->request('GET', $request_path);

      $data = \GuzzleHttp\json_decode($response->getBody());

      \Drupal::cache()->set('simple_icontact_lists', $data);
    }
    return $data;

  }

  public function getLists($refresh = self::CACHED) {
    return $this->fetchLists($refresh);
  }

  public function getListFormOptions($filter = []) {
    $lists = $this->getLists(self::CACHED);

    $list_array = [];
    foreach ($lists->lists as $key => $value) {
      if (empty($filter) || (in_array($value->listId, $filter))) {
        $list_array[$value->listId] = $value->name;
      }
    }

    return $list_array;
  }
}