<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Lsw\SecureControllerBundle\Annotation\Secure;

/**
 * Класс для выполнения ассинхронных операций из админки Сонаты
 *
 * @Secure(roles="ROLE_ADMIN")
 */
class SonataController extends Controller
{
	/**
	 * Действие смены булева поля
	 * @Route("/swap/{field}/{entity}/{id}", name = "swap_field")
	 */
	public function swapAction($field, $entity, $id)
	{
		$em     = $this->getDoctrine()->getManager('drug');
		$entity = 'VidalDrugBundle:' . $entity;
		$field  = 'e.' . $field;

		$isActive = $em->createQueryBuilder()
			->select($field)
			->from($entity, 'e')
			->where('e.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getSingleScalarResult();

		$swapActive = $isActive ? 0 : 1;

		$qb = $em->createQueryBuilder()
			->update($entity, 'e')
			->set($field, $swapActive)
			->where('e.id = :id')
			->setParameter('id', $id);

		$qb->getQuery()->execute();

		return new JsonResponse($swapActive);
	}

	/**
	 * Действие смены булева поля
	 * @Route("/swap-main/{field}/{entity}/{id}", name = "swap_main")
	 */
	public function swapMainAction($field, $entity, $id)
	{
		$em     = $this->getDoctrine()->getManager();
		$entity = 'VidalMainBundle:' . $entity;
		$field  = 'e.' . $field;

		$isActive = $em->createQueryBuilder()
			->select($field)
			->from($entity, 'e')
			->where('e.id = :id')
			->setParameter('id', $id)
			->getQuery()
			->getSingleScalarResult();

		$swapActive = $isActive ? 0 : 1;

		$qb = $em->createQueryBuilder()
			->update($entity, 'e')
			->set($field, $swapActive)
			->where('e.id = :id')
			->setParameter('id', $id);

		$qb->getQuery()->execute();

		return new JsonResponse($swapActive);
	}

	/**
	 * [AJAX] Подгрузка категорий
	 * @Route("/admin/types-of-rubrique/{rubriqueId}", name="types_of_rubrique", options={"expose":true})
	 */
	public function typesOfRubrique($rubriqueId)
	{
		$em      = $this->getDoctrine()->getManager('drug');
		$results = $em->createQuery('
			SELECT t.id, t.title
			FROM VidalDrugBundle:ArtType t
			WHERE t.rubrique = :rubriqueId
			ORDER BY t.title ASC
		')->setParameter('rubriqueId', $rubriqueId)
			->getResult();

		return new JsonResponse($results);
	}

	/**
	 * [AJAX] Подгрузка категорий
	 * @Route("/admin/categories-of-type/{typeId}", name="categories_of_type", options={"expose":true})
	 */
	public function categoriesOfType($typeId)
	{
		$em      = $this->getDoctrine()->getManager('drug');
		$results = $em->createQuery('
			SELECT c.id, c.title
			FROM VidalDrugBundle:ArtCategory c
			WHERE c.type = :typeId
			ORDER BY c.title ASC
		')->setParameter('typeId', $typeId)
			->getResult();

		return new JsonResponse($results);
	}
}