<?php
class TwitterAPI {

    private $TWITTER_ENDPOINT_URL = "https://api.twitter.com/2/tweets/search/recent?";

    //This needs to be included in the Authorization header in the request you send to the twitter endpoint.
    private $API_TOKEN = "AAAAAAAAAAAAAAAAAAAAACmRSgEAAAAAI2auqKXEw%2B%2FRWN33HqC%2BDnl4R4U%3DcovJeHIV5NMawm0EkFGHbpRIgLGerv1GhYpVGg1j5pnjhyzs0z";

    /**
     * Retrieves the data for twitter hash tags by calling the https://api.twitter.com/2/tweets/search/recent endpoint.
    */
    public function twitterData($value) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->TWITTER_ENDPOINT_URL.'query=' . $value . '&expansions=author_id&tweet.fields=author_id,created_at,id,text&user.fields=name,id,username',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array("Authorization: Bearer ". $this->API_TOKEN),
        ));

          $response = curl_exec($curl);

          curl_close($curl);

          return (json_decode($response));
}

}
