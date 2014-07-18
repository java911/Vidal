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

	/** @Route("/tag-set/{tagId}", name="tag_set", options={"expose":true}) */
	public function tagSetAction($tagId)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$tag = $em->getRepository('VidalDrugBundle:Tag')->findOneById($tagId);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$text = $tag->getText();
		$pdo  = $em->getConnection();

		# проставляем тег у статей энкициклопедии
		$articles = $em->createQuery('
			SELECT a.id
			FROM VidalDrugBundle:Article a
			WHERE a.title LIKE :text
				OR a.announce LIKE :text
				OR a.body LIKE :text
		')->setParameter('text', '%' . $text . '%')->getResult();

		foreach ($articles as $a) {
			$id   = $a['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO article_tag (tag_id, article_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		# проставляем тег у статей специалистам
		$arts = $em->createQuery('
			SELECT a.id
			FROM VidalDrugBundle:Art a
			WHERE a.title LIKE :text
				OR a.announce LIKE :text
				OR a.body LIKE :text
		')->setParameter('text', '%' . $text . '%')->getResult();

		foreach ($arts as $a) {
			$id   = $a['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO art_tag (tag_id, art_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		# проставляем тег у новостей
		$publications = $em->createQuery('
			SELECT a.id
			FROM VidalDrugBundle:Publication a
			WHERE a.title LIKE :text
				OR a.announce LIKE :text
				OR a.body LIKE :text
		')->setParameter('text', '%' . $text . '%')->getResult();

		foreach ($publications as $p) {
			$id   = $p['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO publication_tag (tag_id, publication_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		# проставляем тег у новостей фарм-компаний
		$pharmArticles = $em->createQuery('
			SELECT a.id
			FROM VidalDrugBundle:PharmArticle a
			WHERE a.text LIKE :text
		')->setParameter('text', '%' . $text . '%')->getResult();

		foreach ($pharmArticles as $p) {
			$id   = $p['id'];
			$stmt = $pdo->prepare("INSERT IGNORE INTO pharm_article_tag (tag_id, pharm_article_id) VALUES ($tagId, $id)");
			$stmt->execute();
		}

		# добавляем для админки сонаты оповещение
		$this->get('session')->getFlashbag()->add('tag_set', '');

		return $this->redirect($this->generateUrl('admin_vidal_drug_tag_edit', array('id' => $tagId)));
	}

	/** @Route("/admin/users-excel", name="users_excel") */
	public function usersExcelAction()
	{
		$em = $this->getDoctrine()->getManager();

		$users = $em->getRepository('VidalMainBundle:User')->findUsersExcel();

		// ask the service for a Excel5
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->getProperties()->setCreator("Vidal.ru")
			->setLastModifiedBy("Vidal.ru")
			->setTitle("Зарегистрированные пользователи Видаля")
			->setSubject("Зарегистрированные пользователи Видаля");

		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('A1', 'Почтовый адрес')
			->setCellValue('B1', 'Фамилия')
			->setCellValue('C1', 'Имя')
			->setCellValue('D1', 'Отчество')
			->setCellValue('E1', 'Первичная специальность')
			->setCellValue('F1', 'Вторичная специальность')
			->setCellValue('G1', 'Специализация')
			->setCellValue('H1', 'Университет')
			->setCellValue('I1', 'Другое учебное заведение')
			->setCellValue('J1', 'Год выпуска')
			->setCellValue('K1', 'Форма обучения')
			->setCellValue('L1', 'Ученая степень')
			->setCellValue('M1', 'Дата рождения')
			->setCellValue('N1', 'Номер телефона')
			->setCellValue('O1', 'ICQ')
			->setCellValue('P1', 'Тема диссертации')
			->setCellValue('Q1', 'Профессиональные интересы')
			->setCellValue('R1', 'Место работы')
			->setCellValue('S1', 'Должность')
			->setCellValue('T1', 'Стаж')
			->setCellValue('U1', 'Достижения')
			->setCellValue('V1', 'Публикации')
			->setCellValue('W1', 'О себе');

		$worksheet = $phpExcelObject->getActiveSheet();
		$alphabet  = explode(' ', 'A B C D E F G H I J K L N O P Q R S T U V W');
		foreach ($alphabet as $letter) {
			$worksheet->getColumnDimension($letter)->setAutoSize('true');
		}

		for ($i = 0; $i < 100; $i++) {
			$index = $i + 2;
			$worksheet
				->setCellValue("A{$index}", $users[$i]['username'])
				->setCellValue("B{$index}", $users[$i]['lastName'])
				->setCellValue("C{$index}", $users[$i]['firstName'])
				->setCellValue("D{$index}", $users[$i]['surName'])
			;
		}

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);

		// create the writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
		// create the response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// adding headers
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Content-Disposition', 'attachment;filename=stream-file.xls');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');

		return $response;

		############################################################################################################

		//		$eventSpecialties = $em->getRepository('EvrikaMainBundle:Event')->findSpecialtiesByEventIds($eventIds);
		//		$title            = "Выгрузка событий Эврики за $year год";
		//
		//		$excelService->excelObj->getProperties()->setCreator("Evrika.ru")
		//			->setLastModifiedBy("Evrika.ru")
		//			->setTitle($title)
		//			->setSubject($title)
		//			->setDescription($title);
		//
		//		$excelService->excelObj->setActiveSheetIndex(0)
		//			->setCellValue('A1', 'Событие')
		//			->setCellValue('B1', 'Начинается')
		//			->setCellValue('C1', 'Заканчивается')
		//			->setCellValue('D1', 'Специальности')
		//			->setCellValue('E1', 'Ссылка');
		//
		//		$worksheet = $excelService->excelObj->getActiveSheet();
		//		$worksheet->getColumnDimension('A')->setAutoSize('true');
		//		$worksheet->getColumnDimension('B')->setAutoSize('true');
		//		$worksheet->getColumnDimension('C')->setAutoSize('true');
		//		$worksheet->getColumnDimension('D')->setAutoSize('true');
		//		$worksheet->getColumnDimension('E')->setAutoSize('true');
		//
		//		$worksheet->getStyle('A1')->getFont()->getColor()->setRGB('FF0000');
		//		$worksheet->getStyle('B1')->getFont()->getColor()->setRGB('FF0000');
		//		$worksheet->getStyle('C1')->getFont()->getColor()->setRGB('FF0000');
		//		$worksheet->getStyle('D1')->getFont()->getColor()->setRGB('FF0000');
		//		$worksheet->getStyle('E1')->getFont()->getColor()->setRGB('FF0000');
		//
		//		for ($i = 0; $i < count($events); $i++) {
		//			$key = $events[$i]['id'];
		//			$excelService->excelObj->setActiveSheetIndex(0)
		//				->setCellValue('A' . ($i + 2), $events[$i]['title'])
		//				->setCellValue('B' . ($i + 2), $events[$i]['starts'] ? $events[$i]['starts']->format('d.m.Y') : '')
		//				->setCellValue('C' . ($i + 2), $events[$i]['ends'] ? $events[$i]['ends']->format('d.m.Y') : '')
		//				->setCellValue('D' . ($i + 2), $eventSpecialties[$key])
		//				->setCellValue('E' . ($i + 2), $events[$i]['sourceUrl']);
		//		}
		//
		//		$excelService->excelObj->getActiveSheet()->setTitle('События');
		//		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		//		$excelService->excelObj->setActiveSheetIndex(0);
		//
		//		//create the response
		//		$filename = "Evrika.ru: события за $year год.xls";
		//		$response = $excelService->getResponse();
		//		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		//		$response->headers->set('Content-Disposition', "attachment;filename=\"$filename\"");
		//
		//		// If you are using a https connection, you have to set those two headers and use sendHeaders() for compatibility with IE <9
		//		$response->headers->set('Pragma', 'public');
		//		$response->headers->set('Cache-Control', 'maxage=1');
		//
		//		return $response;
	}
}