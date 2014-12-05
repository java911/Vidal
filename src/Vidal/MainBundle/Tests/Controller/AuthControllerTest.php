<?php

namespace Vidal\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
	public function testRegistration()
	{
		$client  = static::createClient();
		$crawler = $client->request('GET', '/registration');

		$form = $crawler->selectButton('register[submit]')->form();

		// set some values
		//$form['name'] = 'Lucas';
		//$form['form_name[subject]'] = 'Hey there!';

		// submit the form
		//$crawler = $client->submit($form);

		//$this->assertTrue($crawler->filter('.thanks')->count() > 0);
	}
}
