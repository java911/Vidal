<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

	/**
	 * @Route("/admin/document-remove/{type}/{id}/{DocumentID}", name="document_remove")
	 */
	public function documentRemoveAction($type, $id, $DocumentID)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$entity   = $em->getRepository("VidalDrugBundle:$type")->findOneById($id);
		$document = $em->getRepository('VidalDrugBundle:Document')->findById($DocumentID);

		if (!$entity || !$document) {
			return new JsonResponse('FAIL');
		}

		$entity->removeDocument($document);
		$em->flush();

		return new JsonResponse('OK');
	}

	/**
	 * @Route("/admin/move-art", name="move_art")
	 *
	 * @Template("VidalDrugBundle:Sonata:move_art.html.twig")
	 */
	public function moveArtAction(Request $request)
	{
		$em        = $this->getDoctrine()->getManager('drug');
		$articles  = $em->getRepository('VidalDrugBundle:Art')->findAll();
		$rubriques = $em->getRepository('VidalDrugBundle:ArtRubrique')->findAll();

		$params = array(
			'title'     => 'Перемещение статей',
			'articles'  => $articles,
			'rubriques' => $rubriques,
		);

		if ($request->getMethod() == 'POST') {
			$articleIds = $request->request->get('articles');
			$rubriqueId = $request->request->get('rubrique', null);
			$typeId     = $request->request->get('type', null);
			$categoryId = $request->request->get('category', null);

			$em->createQuery('
				UPDATE VidalDrugBundle:Art a
				SET a.rubrique = :rubriqueId,
					a.type = :typeId,
					a.category = :categoryId
				WHERE a.id IN (:articleIds)
			')->setParameters(array(
					'articleIds' => $articleIds,
					'rubriqueId' => $rubriqueId,
					'typeId'     => $typeId,
					'categoryId' => $categoryId,
				))->execute();

			$this->get('session')->getFlashBag()->add('notice', '');

			return $this->redirect($this->generateUrl('move_art'));
		}

		return $params;
	}

	/**
	 * @Route("/admin/qa-email/{id}", name="qa_email")
	 */
	public function qaEmailAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$qa = $em->getRepository('VidalMainBundle:QuestionAnswer')->findOneById($id);

		if (!$qa || $qa->getEmailSent() == true) {
			return new JsonResponse(false);
		}

		$email = $qa->getAuthorEmail();

		$this->get('email.service')->send(
			$email,
			array('VidalMainBundle:Email:qa_answer.html.twig', array('faq' => $qa)),
			'Ответ на сайте vidal.ru'
		);

		$qa->setEmailSent(true);
		$em->flush();

		return new JsonResponse(true);
	}

	/**
	 * @Route("/admin/qa-email-test/{id}", name="qa_email_test")
	 */
	public function qaEmailTestAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$qa = $em->getRepository('VidalMainBundle:QuestionAnswer')->findOneById($id);

		$email = $qa->getAuthorEmail();

		$this->get('email.service')->send(
			$email,
			array('VidalMainBundle:Email:qa_answer.html.twig', array('faq' => $qa)),
			'Ответ на сайте vidal.ru'
		);

		$qa->setEmailSent(true);
		$em->flush();

		exit;
	}

	/**
	 * @Route("/email1", name="email1")
	 */
	public function email1Action()
	{
		$templating = $this->container->get('templating');
		$em         = $this->getDoctrine()->getManager('drug');

		# получаем список адресов по рассылке
		//		$doctors = $em->createQuery('
		//			SELECT u.username
		//			FROM VidalMainBundle:User u
		//		')->getResult();
		//
		//		$emails = array();
		//		foreach ($doctors as $doctor) {
		//			$emails[] = $doctor['username'];
		//		}

		# разметка дайджеста
		$subject = 'Тестовая рассылка';
		$html    = $templating->render('VidalMainBundle:Email:email.html.twig');

		#testing
		$emails = array(
			'si-bu@yandex.ru',
			'feijfrdug@mail.ru',
			'ovshum@rambler.ru',
			'm.yudintseva@vidal.ru',
			'alfa__omega@mail.ru',
			'meola243@gmail.com',
			'tan-zh@yandex.ru',
			'7binary@bk.ru',
			'7binary@gmail.com',
		);

		# рассылка по 100 пользователям за цикл
		for ($i = 0, $c = count($emails); $i < $c; $i++) {
			$result = $this->send($emails[$i], $html, $subject);

			if ($i && ($i % 50) == 0) {
				sleep(30);
			}
		}

		echo 'OK';
		exit;
	}

	public function send($email, $html, $subject)
	{
		$mail = new \PHPMailer();

		$mail->isSMTP();
		$mail->isHTML(true);
		$mail->CharSet  = 'UTF-8';
		$mail->FromName = 'Портал Vidal.ru';
		$mail->Subject  = $subject;
		$mail->Body     = $html;
		$mail->addAddress($email);

		if ($this->container->getParameter('kernel.environment') == 'prod') {
			$mail->Host = '127.0.0.1';
			$mail->From = 'maillist@vidal.ru';
		}
		else {
			$mail->Host       = 'smtp.mail.ru';
			$mail->From       = '7binary@list.ru';
			$mail->SMTPSecure = 'ssl';
			$mail->Port       = 465;
			$mail->SMTPAuth   = true;
			$mail->Username   = '7binary@list.ru';
			$mail->Password   = 'ooo000)O';
		}

		return $mail->send();
	}

	/** @Route("/admin/check-document/{DocumentID}", name="check_document", options={"expose":true}) */
	public function checkDocument($DocumentID)
	{
		$em           = $this->getDoctrine()->getManager('drug');
		$documentInDb = $em->getRepository('VidalDrugBundle:Document')->findOneByDocumentID($DocumentID);
		$isFree       = $documentInDb ? 0 : 1;

		return new JsonResponse($isFree);
	}

	/** @Route("/admin/clone-document/{DocumentID}/{newDocumentID}", name="clone_document", options={"expose":true}) */
	public function cloneDocument($DocumentID, $newDocumentID)
	{
		$em = $this->getDoctrine()->getManager('drug');

		$columns = 'RusName, EngName, Name, CompiledComposition, ArticleID, YearEdition, DateOfIncludingText,
		 DateTextModified, Elaboration, CompaniesDescription, ClPhGrDescription, ClPhGrName, PhInfluence, PhKinetics,
		 Dosage, OverDosage, Interaction, Lactation, SideEffects, StorageCondition, Indication, ContraIndication,
		 SpecialInstruction, ShowGenericsOnlyInGNList, NewForCurrentEdition, CountryEditionCode, IsApproved,
		 CountOfColorPhoto,	PregnancyUsing, NursingUsing, RenalInsuf, RenalInsufUsing, HepatoInsuf, HepatoInsufUsing,
		 PharmDelivery, WithoutRenalInsuf, WithoutHepatoInsuf, ElderlyInsuf, ElderlyInsufUsing, ChildInsuf,
		 ChildInsufUsing, ed';

		$pdo   = $em->getConnection();
		$query = "
			INSERT INTO document (DocumentID, $columns)
			SELECT $newDocumentID, $columns
			FROM document
			WHERE DocumentID = $DocumentID
		";

		# отключаем проверку внешних ключей
		$stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
		$stmt->execute();

		# вставляем документ с новым идентификатором
		$stmt = $pdo->prepare($query);
		$stmt->execute();

		return $this->redirect($this->generateUrl('admin_vidal_drug_document_edit', array('id' => $newDocumentID)));
	}
}