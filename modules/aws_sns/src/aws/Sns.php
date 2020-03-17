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

}
