<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KfuTotalCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:kfu_total')
			->setDescription('Kfu.total generator');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('+++ vidal:kfu_total started');

		$em       = $this->getContainer()->get('doctrine')->getManager('drug');
		$repo     = $em->getRepository('VidalDrugBundle:ClinicoPhPointers');
		$kfuItems = $repo->findAll();

		if (empty($kfuItems)) {
			return;
		}

		# ставим у финальных разделов, сколько всего у них препаратов
		foreach ($kfuItems as $kfu) {
			$isFinal = $repo->isFinal($kfu);

			if ($isFinal) {
				$documentIds = $this->getDocumentIds($kfu->getDocuments());
				$total       = 0;

				if (!empty($documentIds)) {
					$products = $em->getRepository('VidalDrugBundle:Product')->findByDocumentIds($documentIds);
					$total    = count($products);
				}
			}
			else {
				$total = null;
			}

			$kfu->setTotal($total);
		}

		$em->flush();

		# ставим у родительских разделов то же поле
		for ($i = 0; $i < 4; $i++) {
			$codes = $repo->findForTree();

			# надо сгруппировать по родителю (запихпуть в list родителя дочерние)
			for ($i = 11; $i > 0; $i = $i - 3) {
				foreach ($codes as $codeValue => $code) {
					if (strlen($codeValue) == $i) {
						$key = substr($codeValue, 0, -3);
						if (isset($codes[$key]) && strlen($codeValue) > strlen($key)) {
							$codes[$key]['list'][$codeValue] = $code;
						}
					}
				}
			}

			for ($i = 11; $i > 0; $i = $i - 3) {
				foreach ($codes as $codeValue => &$code) {
					if (isset($code['list'])) {
						$total = 0;
						foreach ($code['list'] as $c) {
							$plus = $c['total'] ? $c['total'] : 0;
							$total += $plus;
						}
						$repo->updateTotal($code['ClPhPointerID'], $total);
						$code['total'] = $total;
					}
				}
			}
		}

		$output->writeln('--- vidal:drug_total completed');
	}

	private function getDocumentIds($documents)
	{
		$ids = array();

		foreach ($documents as $document) {
			$ids[] = $document->getDocumentID();
		}

		return $ids;
	}
}