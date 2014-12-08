<?php

namespace Vidal\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateControllerTest extends WebTestCase
{
	public function testCountProducts()
	{
		$client    = static::createClient();
		$container = $client->getContainer();
		$em        = $container->get('doctrine')->getManager('drug');

		$entities = array('ATC', 'ClinicoPhPointers', 'Nozology', 'Company', 'InfoPage');

		foreach ($entities as $entity) {
			$qb    = $em->createQueryBuilder();
			$count = $qb->select('COUNT(o)')
				->where('o.countProducts > 0')
				->from('VidalDrugBundle:' . $entity, 'o')
				->getQuery()
				->getResult();

			$this->assertTrue($count > 0);
		}
	}
}
