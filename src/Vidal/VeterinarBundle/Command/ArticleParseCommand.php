<?php
namespace Vidal\VeterinarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\VeterinarBundle\Entity\Article;

class ArticleParseCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:article_parse')
			->setDescription('Command to copy');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em       = $this->getContainer()->get('doctrine')->getManager('veterinar');
		$fileName = __DIR__ . DIRECTORY_SEPARATOR . 'e1.xml';
		$xml      = simplexml_load_file($fileName);
		$i        = 0;

		foreach ($xml->disease as $d) {
			$rubriqueCategory = null;
			$rubriqueTitle    = $d->rubrique . '';
			$title            = $d->title . '';
			$link             = $d->link . '';
			$announce         = $d->announce . '';
			$pubDate          = new \DateTime($d->pubDate . '');
			$author           = $d->author . '';
			$text             = $d->text . '';

			//     http://www.vidal.ru/patsientam/entsiklopediya/nevrologia/
			if (!empty($link)) {
				# лат. название рубрики
				$rubriqueCategory = preg_replace(
					'/http:\\/\\/www\\.vidal\\.ru\\/patsientam\\/entsiklopediya\\/(.+)\\/(.+)/',
					'$1',
					$link
				);
				if ($pos = strpos($rubriqueCategory, '/')) {
					$rubriqueCategory = substr($rubriqueCategory, $pos + 1);
				}
				# последняя часть адреса статьи
				$link = preg_replace(
					'/^(.+)\\/(.+)\\.html?$/',
					'$2',
					$link
				);
			}

			# получить или создать рубрику по названию
			$rubrique = $em->getRepository('VidalVeterinarBundle:ArticleRubrique')->getByTitle(
				$rubriqueTitle,
				$rubriqueCategory
			);

			$now = new \DateTime('now');

			# создаем статью
			$article = new Article();
			$article->setRubrique($rubrique);
			$article->setTitle($title);
			$article->setLink($link);
			$article->setAnnounce($announce);
			$article->setBody($text);
			$article->setCreated($pubDate);
			$article->setUpdated($now);
			$article->setAuthor($author);
			$em->persist($article);
			$em->flush($article);
			$em->refresh($article);

			# надо прикрепить к статьи документы по ссылке
			$documentIds = array();

			foreach ($d->drugs as $drugs) {
				foreach ($drugs as $name) {
					$document = $em->getRepository('VidalVeterinarBundle:Document')->findByName($name);
					if ($document) {
						$documentId = $document->getDocumentID();
						if (!in_array($documentId, $documentIds)) {
							$article->addDocument($document);
							$documentIds[] = $documentId;
						}
					}
				}
			}

			$em->flush();

			$output->writeln('... ' . ++$i);
		}
	}
}