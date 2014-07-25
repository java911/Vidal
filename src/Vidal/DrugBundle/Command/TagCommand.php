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

		############################################################################################
		############################################################################################
		############################################################################################
		# проставляем все теги

		$tags = $em->createQuery('
			SELECT t.text, t.id
			FROM VidalDrugBundle:Tag t
		')->getResult();

		foreach ($tags as $tag) {
			$text  = trim($tag['text']);
			$tagId = $tag['id'];

			# проставляем тег у статей энкициклопедии
			$articles = $em->createQuery('
					SELECT a.id
					FROM VidalDrugBundle:Article a
					WHERE (a.title LIKE :text1 OR a.title LIKE :text2 OR a.title LIKE :text3 OR a.title LIKE :text4 OR a.title LIKE :text5)
						OR (a.announce LIKE :text1 OR a.announce LIKE :text2 OR a.announce LIKE :text3 OR a.announce LIKE :text4 OR a.announce LIKE :text5)
						OR (a.body LIKE :text1 OR a.body LIKE :text2 OR a.body LIKE :text3 OR a.body LIKE :text4 OR a.body LIKE :text5)
				')->setParameters(array(
					'text1' => '% ' . $text . '%',
					'text2' => '%"' . $text . '%',
					'text3' => '%,' . $text . '%',
					'text4' => '%.' . $text . '%',
					'text5' => '%(' . $text . '%',
				))->getResult();

			foreach ($articles as $a) {
				$id   = $a['id'];
				$stmt = $pdo->prepare("INSERT IGNORE INTO article_tag (tag_id, article_id) VALUES ($tagId, $id)");
				$stmt->execute();
			}

			# проставляем тег у статей специалистам
			$arts = $em->createQuery('
					SELECT a.id
					FROM VidalDrugBundle:Art a
					WHERE (a.title LIKE :text1 OR a.title LIKE :text2 OR a.title LIKE :text3 OR a.title LIKE :text4 OR a.title LIKE :text5)
						OR (a.announce LIKE :text1 OR a.announce LIKE :text2 OR a.announce LIKE :text3 OR a.announce LIKE :text4 OR a.announce LIKE :text5)
						OR (a.body LIKE :text1 OR a.body LIKE :text2 OR a.body LIKE :text3 OR a.body LIKE :text4 OR a.body LIKE :text5)
				')->setParameters(array(
					'text1' => '% ' . $text . '%',
					'text2' => '%"' . $text . '%',
					'text3' => '%,' . $text . '%',
					'text4' => '%.' . $text . '%',
					'text5' => '%(' . $text . '%',
				))->getResult();

			foreach ($arts as $a) {
				$id   = $a['id'];
				$stmt = $pdo->prepare("INSERT IGNORE INTO art_tag (tag_id, art_id) VALUES ($tagId, $id)");
				$stmt->execute();
			}

			# проставляем тег у новостей
			$publications = $em->createQuery('
					SELECT a.id
					FROM VidalDrugBundle:Publication a
					WHERE (a.title LIKE :text1 OR a.title LIKE :text2 OR a.title LIKE :text3 OR a.title LIKE :text4 OR a.title LIKE :text5)
						OR (a.announce LIKE :text1 OR a.announce LIKE :text2 OR a.announce LIKE :text3 OR a.announce LIKE :text4 OR a.announce LIKE :text5)
						OR (a.body LIKE :text1 OR a.body LIKE :text2 OR a.body LIKE :text3 OR a.body LIKE :text4 OR a.body LIKE :text5)
				')->setParameters(array(
					'text1' => '% ' . $text . '%',
					'text2' => '%"' . $text . '%',
					'text3' => '%,' . $text . '%',
					'text4' => '%.' . $text . '%',
					'text5' => '%(' . $text . '%',
				))->getResult();

			foreach ($publications as $p) {
				$id   = $p['id'];
				$stmt = $pdo->prepare("INSERT IGNORE INTO publication_tag (tag_id, publication_id) VALUES ($tagId, $id)");
				$stmt->execute();
			}

			# проставляем тег у новостей фарм-компаний
			$pharmArticles = $em->createQuery('
					SELECT a.id
					FROM VidalDrugBundle:PharmArticle a
					WHERE a.text LIKE :text1 OR a.text LIKE :text2 OR a.text LIKE :text3 OR a.text LIKE :text4 OR a.text LIKE :text5
				')->setParameters(array(
					'text1' => '% ' . $text . '%',
					'text2' => '%"' . $text . '%',
					'text3' => '%,' . $text . '%',
					'text4' => '%.' . $text . '%',
					'text5' => '%(' . $text . '%',
				))->getResult();

			foreach ($pharmArticles as $p) {
				$id   = $p['id'];
				$stmt = $pdo->prepare("INSERT IGNORE INTO pharmarticle_tag (tag_id, pharmarticle_id) VALUES ($tagId, $id)");
				$stmt->execute();
			}
		}

		$output->writeln('+++ vidal:tag completed');
	}
}