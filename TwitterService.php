<?php
include_once("TwitterAPI.php");
include_once('DataBaseManager.php');

$mysql = new MySQL('host', 'user', 'password', 'database');

class TwitterService {
  public $value;
  private $service;
  public function __construct(TwitterAPI $service) {
    if (!empty ($_GET['value'])) {
      $this->value = $_GET['value'];
    }
    $this->service = $service;
  }


  /**
       * Retrieves the tweets for the search elelment.
       *
       * @param value search value
       * @return the search result in the required format
      */
  public function getTwitterData() {
      $dataList =  $this->service->twitterData($this->value);
      $result = [];
      if ($dataList && $dataList->data && count($dataList->data) > 0 ) {
        foreach ($dataList->data as $value) {
          /**
          *to find the author details from the includes
          *author id is availabe in the data list
          */
          foreach ($dataList->includes->users as $eachUser) {
            if ($eachUser->id == $value->author_id) {
              $autherDetails = $eachUser;
            }
          }
          /**
          * create the object results
          */
          $result[] = (object) [
            'date'=> $value->created_at,
            'tweet' => $value->text,
            'tweet_id' => $value->id,
            'author' => $autherDetails
          ];
        }
      }
      return ($result);
  }

}

$data = new TwitterService(new TwitterAPI());

$result = $data->getTwitterData();

print_r($result); die;
 ?>
