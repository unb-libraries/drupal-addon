<?php

namespace Drupal\lib_unb_custom_entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JsonController extends ControllerBase {

  protected const FORMAT = 'json';

  protected function getStorage($entity_type) {
    return \Drupal::entityTypeManager()->getStorage($entity_type);
  }

  public function view(EntityInterface $entity, Request $request) {
    $json = $entity->toArray() + [
      'url' => $entity->toUrl('canonical', [
        'format' => self::FORMAT
      ])->toString(),
    ];
    return new JsonResponse($json, 200);
  }

  public function add($entity_type, Request $request) {
    return new JsonResponse([
      'message' => 'Nothing to see or do here.'
    ], 200);
  }

  public function edit(EntityInterface $entity, Request $request) {
    return new JsonResponse([
      'message' => 'Nothing to see or do here.'
    ], 200);
  }

  public function delete(EntityInterface $entity, Request $request) {
    return new JsonResponse([
      'message' => 'Nothing to see or do here.'
    ], 200);
  }

  public function list($entity_type, Request $request) {
    foreach ($this->getStorage($entity_type)->loadMultiple() as $entity) {
      $json[] = [
        'id' => $entity->id(),
        'label' => $entity->label(),
        'url' => $entity->toUrl('canonical',[
          'format' => self::FORMAT
        ])->toString(),
      ];
    }

    return new JsonResponse($json, 200);
  }

}