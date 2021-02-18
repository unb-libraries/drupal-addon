<?php

namespace Drupal\lib_unb_custom_entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller which responds to requests that require "_format=json".
 *
 * @package Drupal\lib_unb_custom_entity\Controller
 */
class JsonController extends ControllerBase {

  protected const FORMAT = 'json';

  /**
   * Retrieve the storage handler for the given entity type.
   *
   * @param string $entity_type
   *   The entity type for which to retrieve a storage handler.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   An instance of EntityStorageInterface.
   */
  protected function getStorage(string $entity_type) {
    return $this->entityTypeManager()
      ->getStorage($entity_type);
  }

  /**
   * Retrieve the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to retrieve.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An instance of JsonResponse.
   */
  public function view(EntityInterface $entity, Request $request) {
    $json = $entity->toArray() + [
      'url' => $entity->toUrl('canonical', [
        'format' => self::FORMAT,
      ])->toString(),
    ];

    return $this->respond($json);
  }

  /**
   * Create a new entity of the given type.
   *
   * @param string $entity_type
   *   The entity type of which to create an instance.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An instance of JsonResponse.
   */
  public function add($entity_type, Request $request) {
    return $this->respond([
      'message' => 'Nothing to see or do here.',
    ]);
  }

  /**
   * Update the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to update.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An instance of JsonResponse.
   */
  public function edit(EntityInterface $entity, Request $request) {
    return $this->respond([
      'message' => 'Nothing to see or do here.',
    ]);
  }

  /**
   * Delete the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to delete.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An instance of JsonResponse.
   */
  public function delete(EntityInterface $entity, Request $request) {
    return $this->respond([
      'message' => 'Nothing to see or do here.',
    ]);
  }

  /**
   * List all entities of the given type in JSON format.
   *
   * @param string $entity_type
   *   The entity type to list.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An instance of JsonResponse.
   */
  public function list($entity_type, Request $request) {
    foreach ($this->getStorage($entity_type)->loadMultiple() as $entity) {
      $json[] = [
        'id' => $entity->id(),
        'label' => $entity->label(),
        'url' => $entity->toUrl('canonical', [
          'format' => self::FORMAT,
        ])->toString(),
      ];
    }
    return $this->respond($json);
  }

  /**
   * Build a respond object.
   *
   * @param mixed $data
   *   The data to contain in the response.
   * @param int $status
   *   The HTTP respond code.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   An instance of JsonResponse.
   */
  protected function respond($data, $status = 200) {
    return new JsonResponse($data, $status);
  }

}
