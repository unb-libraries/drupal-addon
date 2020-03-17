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
   * Retrieve the topic ARN for the given topic ID.
   *
   * @param $topic_id
   *   A string of the form "arn:aws:sns:<REGION>:<SOME_NUMBER>:<TOPIC_ID>".
   *
   * @return string|false
   *   A string. FALSE if the given topic ID does not exist.
   */
  public function getTopic($topic_id) {
    $topics = $this->getTopics();
    if (array_key_exists($topic_id, $topics)) {
      return $topics[$topic_id];
    }
    return FALSE;
  }

  /**
   * Retrieve all available topics.
   *
   * @return array
   *   An array of topic ARNs keyed by an ID,
   *   e.g. 'topic1' => 'arn:aws:sns:us-east-1:1234567890:topic1'.
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

  /**
   * Send a message to the given topic.
   *
   * @param string $topic_id
   *   A string.
   * @param string $message
   *   The message string to send.
   *
   * @return bool
   *   TRUE if the message was successfully sent. FALSE otherwise.
   */
  public function send($topic_id, $message = '') {
    if ($arn = $this->getTopic($topic_id)) {
      return $this->doSend($arn, $message);
    }
    return FALSE;
  }

  /**
   * Send the message to the topic with the given ARN.
   *
   * @param string $arn
   *   The topic ARN.
   * @param string $message
   *   The message to send.
   *
   * @return bool
   *   TRUE if the message was successfully sent.
   *   FALSE otherwise.
   */
  protected function doSend($arn, $message = '') {
      /** @var \Aws\Result $result */
      $result = $this->getClient()->publish([
        'Message' => $message,
        'TopicArn' => $arn,
      ]);

      if ($result->get('MessageId')) {
        return TRUE;
      }
      return FALSE;
  }

}
