<?php

namespace Vidal\DrugBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Vidal\DrugBundle\Entity\Article;
use Vidal\DrugBundle\Entity\Art;
use Vidal\DrugBundle\Entity\Publication;
use Vidal\DrugBundle\Entity\Product;
use Vidal\DrugBundle\Entity\Document;
use Vidal\DrugBundle\Entity\PharmPortfolio;

class DoctrineEventSubscriber implements EventSubscriber
{
	private $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Возвращает список имён событий, которые обрабатывает данный класс. Callback-методы должны иметь такие же имена
	 */
	public function getSubscribedEvents()
	{
		return array(
			'prePersist',
			'postPersist',
			'preUpdate',
			'postUpdate',
			'preRemove',
		);
	}

	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		# проставляем ссылку, если пустая
		if ($entity instanceof Article || $entity instanceof Art) {
			$this->setLink($entity);
		}
		elseif ($entity instanceof Document) {
			$this->checkDuplicateDocument($args);
		}
		elseif ($entity instanceof Product) {
			$this->checkDuplicateProduct($args);
		}
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		# проставляем мета к видео, если его загрузили
		if ($entity instanceof Article || $entity instanceof Art || $entity instanceof Publication || $entity instanceof PharmPortfolio) {
			$this->setVideoMeta($entity);
		}

		# добавили препарат - генерируем автодополнение ElasticSearch
		if ($entity instanceof Product) {
			$this->autocompleteProduct($entity);
		}

		# добавили документ - генерируем автодополнение ElasticSearch
		if ($entity instanceof Document) {
			$this->autocompleteDocument($entity);
		}
	}

	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		# проставляем ссылку, если пустая
		if ($entity instanceof Article || $entity instanceof Art) {
			$this->setLink($entity);
		}
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		# проставляем мета к видео, если его загрузили
		if ($entity instanceof Article || $entity instanceof Art || $entity instanceof Publication || $entity instanceof PharmPortfolio) {
			$this->setVideoMeta($entity);
		}
	}

	public function preRemove(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof Product) {
			$em   = $args->getEntityManager();
			$pdo  = $em->getConnection();
			$stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
			$stmt->execute();
			$stmt = $pdo->prepare('DELETE FROM product WHERE ProductID = ' . $entity->getProductID());
			$stmt->execute();
		}
	}

	private function setVideoMeta($entity)
	{
		$video = $entity->getVideo();

		if ($video && isset($video['path'])) {
			$rootDir = $this->container->get('kernel')->getRootDir() . '/../';
			require_once $rootDir . 'src/getID3/getid3.php';

			$getID3   = new \getID3;
			$filename = $rootDir . 'web' . $video['path'];
			$file     = $getID3->analyze($filename);

			$entity->setVideoWidth($file['video']['resolution_x']);
			$entity->setVideoHeight($file['video']['resolution_y']);
			$this->container->get('doctrine')->getManager('drug')->flush($entity);
		}
	}

	private function setLink($entity)
	{
		$link = $entity->getLink();

		if (empty($link)) {
			$link = $this->translit($entity->getTitle());
			$entity->setLink($link);
		}
	}

	private function translit($text)
	{
		$pat  = array('/&[a-z]+;/', '/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
		$rep  = array('', '$1', '$1');
		$text = preg_replace($pat, $rep, $text);
		$text = mb_strtolower($text, 'utf-8');

		// Русский алфавит
		$rus_alphabet = array(
			'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
			'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
			'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
			'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
			'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
			'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
			' ', '.', '(', ')', ',', '/', '?'
		);

		// Английская транслитерация
		$rus_alphabet_translit = array(
			'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'Y',
			'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
			'H', 'TS', 'CH', 'SH', 'SCH', '', 'Y', '', 'E', 'YU', 'IA',
			'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y',
			'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
			'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ia',
			'-', '-', '-', '-', '-', '-', '-'
		);

		return str_replace($rus_alphabet, $rus_alphabet_translit, $text);
	}

	private function autocompleteProduct($product)
	{
		try {
			$patterns     = array('/<SUP>.*<\/SUP>/', '/<SUB>.*<\/SUB>/');
			$replacements = array('', '');
			$RusName      = preg_replace($patterns, $replacements, $product->getRusName());
			$RusName      = mb_strtolower($RusName, 'UTF-8');
			$EngName      = preg_replace($patterns, $replacements, $product->getEngName());
			$EngName      = mb_strtolower($EngName, 'UTF-8');

			if (!empty($RusName)) {
				$this->createAutocomplete('autocomplete', $product->getProductID(), $RusName);
				$this->createAutocomplete('autocompleteext', $product->getProductID(), $RusName);
			}

			if (!empty($EngName)) {
				$this->createAutocomplete('autocomplete', $product->getProductID() + 1, $EngName);
				$this->createAutocomplete('autocompleteext', $product->getProductID() + 1, $EngName);
			}
		}
		catch (\Exception $e) {
		}
	}

	private function autocompleteDocument($document)
	{
		try {
			# autocomplete_document2
			$elasticaClient = new \Elastica\Client();
			$elasticaIndex  = $elasticaClient->getIndex('website');
			$elasticaType   = $elasticaIndex->getType('autocomplete_document2');
			$id             = $document->getDocumentID();

			$document = new \Elastica\Document(
				$id + 100000,
				array('name' => $id . ' - ' . $this->strip($document->getName()))
			);

			$elasticaType->addDocument($document);
			$elasticaType->getIndex()->refresh();
		}
		catch (\Exception $e) {
		}
	}

	private function createAutocomplete($indexName, $id, $name)
	{
		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');
		$elasticaType   = $elasticaIndex->getType($indexName);

		$document = new \Elastica\Document($id + 100000, array('name' => $name));

		$elasticaType->addDocument($document);
		$elasticaType->getIndex()->refresh();
	}

	private function strip($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
		$rep = array('', '', '&');

		return preg_replace($pat, $rep, $string);
	}

	private function checkDuplicateDocument($args)
	{
		$document     = $args->getEntity();
		$DocumentID   = $document->getDocumentID();
		$em           = $args->getEntityManager();
		$documentInDb = $em->getRepository('VidalDrugBundle:Document')->findOneByDocumentID($DocumentID);

		$pdo  = $em->getConnection();
		$stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
		$stmt->execute();

		# если документ с таким идентификатором уже есть - его надо удалить, не проверяя внешних ключей
		if ($documentInDb) {
			$stmt = $pdo->prepare("DELETE FROM document WHERE DocumentID = $DocumentID");
			$stmt->execute();
		}

		# надо почистить старые связи документа
		$tables = explode(' ', 'document_indicnozology document_clphpointers documentoc_atc document_infopage art_document article_document molecule_document pharm_article_document publication_document');
		foreach ($tables as $table) {
			$stmt = $pdo->prepare("DELETE FROM {$table} WHERE DocumentID = {$DocumentID}");
			$stmt->execute();
		}
	}

	private function checkDuplicateProduct($args)
	{
		$product     = $args->getEntity();
		$ProductID   = $product->getProductID();
		$em          = $args->getEntityManager();
		$productInDb = $em->getRepository('VidalDrugBundle:Product')->findByProductID($ProductID);
		
		$pdo  = $em->getConnection();
		$stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
		$stmt->execute();

		# если продукт с таким идентификатором уже есть - его надо удалить, не проверяя внешних ключей
		if ($productInDb) {
			$stmt = $pdo->prepare("DELETE FROM product WHERE ProductID = $ProductID");
			$stmt->execute();
		}

		# надо почистить старые связи документа
		$tables = explode(' ', 'product_atc product_clphgroups product_company product_document product_moleculename product_phthgrp');
		foreach ($tables as $table) {
			$stmt = $pdo->prepare("DELETE FROM {$table} WHERE ProductID = {$ProductID}");
			$stmt->execute();
		}
	}
}