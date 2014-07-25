<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\Tag;

class TagCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:tag');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$em = $this->getContainer()->get('doctrine')->getManager('drug');
		$output->writeln('--- vidal:tag started');
		$pdo  = $em->getConnection();
		$date = '2014-05-14 00:00:00';

		# новости
		$publications = $em->createQuery("
					SELECT p.id
					FROM VidalDrugBundle:Publication p
					WHERE p.date < '$date'
				")->getResult();

		foreach ($publications as $o) {
			$id = $o['id'];
			$pdo->prepare("DELETE FROM publication_tag WHERE publication_id = $id")->execute();
		}

		# статьи энц
		$articles = $em->createQuery("
					SELECT a.id
					FROM VidalDrugBundle:Article a
					WHERE a.date < '$date'
				")->getResult();

		foreach ($articles as $o) {
			$id = $o['id'];
			$pdo->prepare("DELETE FROM article_tag WHERE article_id = $id")->execute();
		}

		# статьи спец
		$arts = $em->createQuery("
					SELECT a.id
					FROM VidalDrugBundle:Art a
					WHERE a.date < '$date'
				")->getResult();

		foreach ($arts as $o) {
			$id = $o['id'];
			$pdo->prepare("DELETE FROM art_tag WHERE art_id = $id")->execute();
		}

		# статьи фарм-компаний
		$pharmArticles = $em->createQuery("
					SELECT a.id
					FROM VidalDrugBundle:PharmArticle a
					WHERE a.created < '$date'
				")->getResult();

		foreach ($pharmArticles as $o) {
			$id = $o['id'];
			$pdo->prepare("DELETE FROM pharmarticle_tag WHERE pharmarticle_id = $id")->execute();
		}

		#================================ проставляем все теги =====================================================

		$tags = $em->createQuery('
			SELECT t.text, t.id
			FROM VidalDrugBundle:Tag t
		')->getResult();

		foreach ($tags as $tag) {
			$text  = trim($tag['text']);
			$tagId = $tag['id'];

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
		}

		$output->writeln('+++ vidal:tag completed');
	}
}