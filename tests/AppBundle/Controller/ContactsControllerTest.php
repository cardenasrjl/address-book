<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/***
 * Some basic checks
 * Class ContactsControllerTest
 * @package Tests\AppBundle\Controller
 */
class ContactsControllerTest extends WebTestCase
{

	private $contactId = 6;

	/***
	 * Checks that
	 */
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Address Book', $crawler->filter('h1')->text());
    }

    /***
     * New Contact
     */
    public function testNewModifyContact()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contacts/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('New Contact', $crawler->filter('h5')->text());

		//lets try to insert a contact
		$form = $crawler->selectButton('Save')->form();

		// set some values
		foreach (ContactRepository::$columns as $column)
			$form["form[{$column}]"] = '123';
		$form["form[birthDate]"] = '1986-01-01';

		// submit the form
		$crawler = $client->submit($form);
		$this->assertContains('Contact created successfully', $crawler->filter('body')->text());
    }

    /***
     * Modify Contact
     */
    public function testModifyContact()
    {

        $client = static::createClient();
        $crawler = $client->request('GET', "/contacts/{$this->contactId}/modify");
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Modify Contact', $crawler->filter('h5')->text());

		$form = $crawler->selectButton('Save')->form();

		// set some values
		foreach (ContactRepository::$columns as $column)
			$form["form[{$column}]"] = '123';
		$form["form[birthDate]"] = '1986-01-02';

		// submit the form
		$crawler = $client->submit($form);
		$this->assertContains('The data has been saved successfully', $crawler->filter('body')->text());

    }


	/***
	 * Test Add Phone
	 */
	public function testAddPhone()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', "/contacts/{$this->contactId}/phones/new");
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertContains('Add Phone Number for contact', $crawler->filter('body')->text());
	}


	/***
	 * Test Add Email
	 */
	public function testAddEmail()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', "/contacts/{$this->contactId}/emails/new");
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertContains('Add Email Account for contact', $crawler->filter('h5')->text());
	}










}
