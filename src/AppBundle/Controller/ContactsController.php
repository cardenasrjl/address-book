<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/***
 * Class created to manage CRUD contacts requests
 * Class ContactsController
 * @package AppBundle\Controller
 */
class ContactsController extends BaseController
{

	/**
	 * Shows the contacts
	 *
	 * @Route("/", name="index_contacts")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showIndex()
	{

		$contacts = $this->getDoctrine()
			->getRepository('AppBundle:Contact')
			->findAll();

		return $this->render('contacts/index.html.twig',
			['contacts' => $contacts]);
	}

	/**
	 * Show details for the contacts, allowing to modify
	 *
	 * @Route("/contacts/{contactId}/modify", name="modify_contact")
	 * @ParamConverter("contact", options={"mapping": {"contactId"   : "id"}} )
	 *
	 * @param Contact $contact
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 */
	public function showDetails(Request $request, Contact $contact)
	{


		try {
			$data = ['operation' => 'modify', 'contact' => $contact];

			//form builder
			$form = $this->getFormBuilder(ContactRepository::$columns);
			$form->add('picture', FileType::class);
			$form = $form->getForm();
			$form->handleRequest($request);

			if ($form->isSubmitted()) { //save

				//validate mandatory params
				$formData = $form->getData();
				$this->validateParams(ContactRepository::$columns, $formData);

				//store data
				$formData['birthDate'] = new \DateTime($formData['birthDate']);
				array_walk(ContactRepository::$columns, function ($param) use (&$contact, $formData) {
					$contact->{'set' . (ucfirst($param))}($formData[$param]);
				});

				if (isset($formData['picture'])) {
					$contact->setPicture($formData['picture']);
					$this->uploadImage($contact);
				}
				
				$em = $this->getDoctrine()->getManager();
				$em->flush();
				$data['success_message'] = 'The data has been saved successfully';
			}
		} catch (\Exception $ex) {
			var_dump($ex->getMessage());die();
			$data['error_message'] = $ex->getMessage();
		}


		return $this->render('contacts/form.html.twig',
			$data);
	}

	/**
	 * Manages creation of a new contact
	 *
	 * @Route("/contacts/new", name="new_contact")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 */
	public function showNew(Request $request)
	{

		try {
			$data = ['operation' => 'new'];

			//form builder
			$form = $this->getFormBuilder(ContactRepository::$columns);
			$form->add('picture', FileType::class);
			$form = $form->getForm();

			$form->handleRequest($request);

			if ($form->isSubmitted()) { //save

				//validate mandatory params
				$formData = $form->getData();
				$this->validateParams(ContactRepository::$columns, $formData);

				//new contact
				$contact = new Contact();
				$formData['birthDate'] = new \DateTime($formData['birthDate']);
				array_walk(ContactRepository::$columns, function ($param) use (&$contact, $formData) {
					$contact->{'set' . (ucfirst($param))}($formData[$param]);
				});

				if (isset($formData['picture'])) {
					$contact->setPicture($formData['picture']);
					$this->uploadImage($contact);
				}

				//store data
				$em = $this->getDoctrine()->getManager();
				$em->persist($contact);
				$em->flush();
				$data = ['operation'       => 'modify',
						 'contact'         => $contact,
						 'success_message' => 'Contact created successfully'];
			}
		} catch (\Exception $ex) {
			$data['contact'] = $formData ?? ''; //to better user experience
			$data['error_message'] = $ex->getMessage();
		}

		return $this->render('contacts/form.html.twig',
			$data);
	}

	/**
	 * Removes the contact
	 *
	 * @Route("/contacts/{contactId}/remove", name="remove_contact")
	 * @ParamConverter("contact", options={"mapping": {"contactId"   : "id"}} )
	 *
	 * @param Contact $contact
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function removeContact(Contact $contact)
	{
		if ($contact) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($contact);
			$em->flush();
		}
		return $this->redirectToRoute('index_contacts');
	}

	/***
	 * Uploads image to the server
	 */
	private function uploadImage(Contact $contact)
	{
		$file = $contact->getPicture();
		$fileName = md5(uniqid()) . '.' . $file->guessExtension();
		$file->move(
			$this->getParameter('pictures_path'),
			$fileName
		);
		$contact->setPicture($fileName);
	}

}
