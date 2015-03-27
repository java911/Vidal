<?php

namespace Vidal\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
	public function testRegistration()
	{
		$client  = static::createClient();
		$crawler = $client->request('GET', '/registration');

		$this->assertTrue($crawler->filter('.thanks')->count() > 0);
	}
}
