<?php

namespace Drupal\aws_sns\aws;

use \Aws\Sns\SnsClient;

/**
 * Class to interact with AWS/SNS.
 *
 * @package Drupal\aws_sns\aws
 */
class Sns {

  /**
   * AWS/SNS client.
   *
   * @var \Aws\Sns\SnsClient
   */
  protected $client;

  /**
   * Retrieve the AWS/SNS client.
   *
   * @return \Aws\Sns\SnsClient
   *   A client object.
   */
  protected function getClient() {
    if (!isset($this->client)) {
      $this->client = new SnsClient([
        'version' => 'latest',
        'credentials' => [
          'key' => '',
          'secret' => '',
        ],
        'region' => 'us-east-1',
      ]);
    }
    return $this->client;
  }

  /**
   * Retrieve all available topics.
   *
   * @return array
   *   An array of topic ARNs keyed by an ID,
   *   e.g. 'topic1' => 'arn:aws:sns:1234567890:topic1'.
   */
  public function getTopics() {
    $topics = [];
    foreach ($this->getClient()->listTopics([])['Topics'] as $topic) {
      $arn = $topic['TopicArn'];
      $exploded_arn = explode(':', $arn);
      $id = $exploded_arn[count($exploded_arn) - 1];
      $topics[$id] = $arn;
    }
    return $topics;
  }

}
