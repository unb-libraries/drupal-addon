<?php

namespace Drupal\lib_unb_custom_entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class JsonController extends ControllerBase {

  protected const FORMAT = 'json';

  public function view(EntityInterface $entity, Request $request) {
    $json = $entity->toArray() + [
      'url' => $entity->toUrl('canonical', [
        'format' => self::FORMAT
      ])->toString()
    ];
    return new JsonResponse($json, 200);
  }

  public function add($entity_type, Request $request) {
    return RedirectResponse::create()
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
  }

}