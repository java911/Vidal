<?php

namespace Vidal\MainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;

class VidalMainBundle extends Bundle
{
//	public function boot()
//	{
//		Type::addType('bit', 'Vidal\MainBundle\Types\BitType');
//
//		$em         = $this->container->get('doctrine.orm.default_entity_manager');
//		$dbPlatform = $em->getConnection()->getDatabasePlatform();
//		$dbPlatform->registerDoctrineTypeMapping('bit', 'boolean');
//	}
}
