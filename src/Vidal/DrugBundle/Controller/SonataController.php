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

	/** @Route("/admin/check-product/{ProductID}", name="check_product", options={"expose":true}) */
	public function checkProduct($ProductID)
	{
		$em          = $this->getDoctrine()->getManager('drug');
		$productInDb = $em->getRepository('VidalDrugBundle:Product')->findOneByProductID($ProductID);
		$isFree      = $productInDb ? 0 : 1;

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

		# надо склонировать связи старого документа на новый
		$tables = explode(' ', 'document_indicnozology document_clphpointers documentoc_atc document_infopage art_document article_document molecule_document pharm_article_document publication_document');
		$fields = explode(' ', 'NozologyCode ClPhPointerID ATCCode InfoPageID art_id article_id MoleculeID pharm_article_id publication_id');

		for ($i = 0; $i < count($tables); $i++) {
			$table = $tables[$i];
			$field = $fields[$i];
			$stmt  = $pdo->prepare("
				INSERT INTO $table ($field, DocumentID)
				SELECT $field, $newDocumentID
				FROM $table
				WHERE DocumentID = $DocumentID
			");
			$stmt->execute();
		}

		return $this->redirect($this->generateUrl('admin_vidal_drug_document_edit', array('id' => $newDocumentID)));
	}

	/** @Route("/admin/clone-product/{ProductID}/{newProductID}", name="clone_product", options={"expose":true}) */
	public function cloneProduct($ProductID, $newProductID)
	{
		$em = $this->getDoctrine()->getManager('drug');

		$columns = 'RusName, EngName, Name, NonPrescriptionDrug, CountryEditionCode, RegistrationDate,
			DateOfCloseRegistration, RegistrationNumber, PPR, ZipInfo, Composition, DateOfIncludingText, ProductTypeCode,
			ItsMultiProduct, BelongMultiProductID, CheckingRegDate, Personal, m, GNVLS, DLO, List_AB, List_PKKN,
			StrongMeans, Poison, MinAs,	ValidPeriod, StrCond, photo, inactive, MarketStatusID';

		$pdo   = $em->getConnection();
		$query = "
			INSERT INTO product (ProductID, document_id, $columns)
			SELECT $newProductID, NULL, $columns
			FROM product
			WHERE ProductID = $ProductID
		";

		# отключаем проверку внешних ключей
		$stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
		$stmt->execute();

		# вставляем документ с новым идентификатором
		$stmt = $pdo->prepare($query);
		$stmt->execute();

		# надо склонировать связи старого документа на новый
		$tables = explode(' ', 'product_atc product_clphgroups product_company product_phthgrp product_moleculename');
		$fields = explode(' ', 'ATCCode ClPhGroupsID CompanyID PhThGroupsID MoleculeNameID');

		for ($i = 0; $i < count($tables); $i++) {
			$table = $tables[$i];
			$field = $fields[$i];
			$stmt  = $pdo->prepare("
				INSERT INTO $table ($field, ProductID)
				SELECT $field, $newProductID
				FROM $table
				WHERE ProductID = $ProductID
			");
			$stmt->execute();
		}

		# надо склонировать картинки
		$stmt = $pdo->prepare("
			INSERT INTO productpicture (ProductID, PictureID, YearEdition, CountryEditionCode, EditionCode)
			SELECT $newProductID, PictureID, YearEdition, CountryEditionCode, EditionCode
			FROM productpicture
			WHERE ProductID = $ProductID
		");
		$stmt->execute();

		$this->get('session')->getFlashbag()->add('notice', '');

		return $this->redirect($this->generateUrl('admin_vidal_drug_product_edit', array('id' => $newProductID)));
	}

	/** @Route("/tag-clean/{tagId}/{ajax}", name="tag_clean", options={"expose":true}) */
	public function tagCleanAction($tagId, $ajax = false)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$tag = $em->getRepository('VidalDrugBundle:Tag')->findOneById($tagId);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$pdo = $em->getConnection();

		$tagId  = $tag->getId();
		$tables = explode(' ', 'art_tag article_tag publication_tag pharmarticle_tag');
		foreach ($tables as $table) {
			$stmt = $pdo->prepare("DELETE FROM $table WHERE tag_id = $tagId");
			$stmt->execute();
		}

		if ($ajax) {
			return new JsonResponse('OK');
		}

		# добавляем для админки сонаты оповещение
		$this->get('session')->getFlashbag()->add('tag_clean', '');

		return $this->redirect($this->generateUrl('admin_vidal_drug_tag_edit', array('id' => $tagId)));
	}

	/** @Route("/tag-clean-old/{tagId}", name="tag_clean_old", options={"expose":true}) */
	public function tagCleanOldAction($tagId)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$tag = $em->getRepository('VidalDrugBundle:Tag')->findOneById($tagId);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$pdo     = $em->getConnection();
		$tagId   = $tag->getId();
		$oldDate = new \DateTime('2014-05-15');

		foreach ($tag->getArticles() as $a) {
			if ($a->getDate() < $oldDate) {
				$id   = $a->getId();
				$stmt = $pdo->prepare("DELETE FROM article_tag WHERE tag_id = $tagId AND article_id = $id");
				$stmt->execute();
			}
		}

		foreach ($tag->getArts() as $a) {
			if ($a->getDate() < $oldDate) {
				$id   = $a->getId();
				$stmt = $pdo->prepare("DELETE FROM art_tag WHERE tag_id = $tagId AND art_id = $id");
				$stmt->execute();
			}
		}

		foreach ($tag->getPublications() as $a) {
			if ($a->getDate() < $oldDate) {
				$id   = $a->getId();
				$stmt = $pdo->prepare("DELETE FROM publication_tag WHERE tag_id = $tagId AND publication_id = $id");
				$stmt->execute();
			}
		}

		foreach ($tag->getPharmArticles() as $a) {
			if ($a->getCreated() > $oldDate) {
				$id   = $a->getId();
				$stmt = $pdo->prepare("DELETE FROM pharmarticle_tag WHERE tag_id = $tagId AND pharmarticle_id = $id");
				$stmt->execute();
			}
		}

		# добавляем для админки сонаты оповещение
		$this->get('session')->getFlashbag()->add('tag_clean_old', '');

		return $this->redirect($this->generateUrl('admin_vidal_drug_tag_edit', array('id' => $tagId)));
	}

	/** @Route("/tag-set/{tagId}/{ajax}", name="tag_set", options={"expose":true}) */
	public function tagSetAction($tagId, $ajax = false)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$tag = $em->getRepository('VidalDrugBundle:Tag')->findOneById($tagId);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$tagSearch = $tag->getSearch();
		$text      = empty($tagSearch) ? $tag->getText() : $tagSearch;
		$pdo       = $em->getConnection();

		# проставляем тег у статей энкициклопедии
		$stmt = $pdo->prepare("SELECT id FROM article WHERE title REGEXP '[[:<:]]{$text}[[:>:]]' OR body REGEXP '[[:<:]]{$text}[[:>:]]' OR announce REGEXP '[[:<:]]{$text}[[:>:]]'");
		$stmt->execute();
		$articles = $stmt->fetchAll();
		foreach ($articles as $a) {
			$id   = $a['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO article_tag (tag_id, article_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		# проставляем тег у статей специалистам
		$stmt = $pdo->prepare("SELECT id FROM art WHERE title REGEXP '[[:<:]]{$text}[[:>:]]' OR body REGEXP '[[:<:]]{$text}[[:>:]]' OR announce REGEXP '[[:<:]]{$text}[[:>:]]'");
		$stmt->execute();
		$articles = $stmt->fetchAll();
		foreach ($articles as $a) {
			$id   = $a['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO art_tag (tag_id, art_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		# проставляем тег у новостей
		$stmt = $pdo->prepare("SELECT id FROM publication WHERE title REGEXP '[[:<:]]{$text}[[:>:]]' OR body REGEXP '[[:<:]]{$text}[[:>:]]' OR announce REGEXP '[[:<:]]{$text}[[:>:]]'");
		$stmt->execute();
		$articles = $stmt->fetchAll();
		foreach ($articles as $a) {
			$id   = $a['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO publication_tag (tag_id, publication_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		# проставляем тег у новостей фарм-компаний
		$stmt = $pdo->prepare("SELECT id FROM pharm_article WHERE text REGEXP '[[:<:]]{$text}[[:>:]]'");
		$stmt->execute();
		$articles = $stmt->fetchAll();
		foreach ($articles as $a) {
			$id   = $a['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO pharmarticle_tag (tag_id, pharmarticle_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		if ($ajax) {
			return new JsonResponse('OK');
		}

		# добавляем для админки сонаты оповещение
		$this->get('session')->getFlashbag()->add('tag_set', '');

		return $this->redirect($this->generateUrl('admin_vidal_drug_tag_edit', array('id' => $tagId)));
	}

	/** @Route("/admin-tag-editable", name="admin_tag_editable", options={"expose":true}) */
	public function tagEditableAction(Request $request)
	{
		$id = $request->request->get('id', null);
		$em = $this->getDoctrine()->getManager('drug');

		$tag = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$text = trim($request->request->get('value'));

		if (!empty($text)) {
			$tag->setText($text);
			$em->flush($tag);
		}

		return new Response($text);
	}

	/** @Route("/admin-tag-search", name="admin_tag_search", options={"expose":true}) */
	public function tagSearchAction(Request $request)
	{
		$id = $request->request->get('id', null);
		$em = $this->getDoctrine()->getManager('drug');

		$tag = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$search = trim($request->request->get('value'));

		if (!empty($search)) {
			$tag->setSearch($search);
			$em->flush($tag);
		}

		return new Response($search);
	}
}