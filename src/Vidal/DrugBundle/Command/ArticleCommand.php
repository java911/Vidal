<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\Article;
use Vidal\DrugBundle\Entity\ArticleRubrique;

class ArticleCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:article');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em       = $this->getContainer()->get('doctrine')->getManager('drug');
		$articles = $em->getRepository('VidalDrugBundle:Article')->findAll();

		foreach ($articles as $a) {
			$codesRaw = $a->getNozologyCodes();

			if ($codesRaw != '') {
				$codes = explode(';', $codesRaw);

				foreach ($codes as $code) {
					$noz = $em->getRepository('VidalDrugBundle:Nozology')->findOneByNozologyCode($code);
					if ($noz) {
						$a->addNozology($noz);
					}
				}
			}
		}

		$em->flush();
	}
}

//$subId    = $a->getSubdivision();
//$sub      = $em->getRepository('VidalDrugBundle:Subdivision')->findOneById($subId);
//$title    = $sub->getName();
//$rubrique = $em->getRepository('VidalDrugBundle:ArticleRubrique')->findOneByTitle($title);
//
//if (empty($rubrique)) {
//	$rubrique = new ArticleRubrique();
//	$rubrique->setTitle($title);
//	$rubrique->setRubrique($sub->getEngName());
//	$em->persist($rubrique);
//	$em->flush($rubrique);
//	$em->refresh($rubrique);
//}
//
//$a->setRubrique($rubrique);