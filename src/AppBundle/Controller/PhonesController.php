<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Repository\ContactRepository;
use AppBundle\Entity\Phone;
use AppBundle\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PhonesController extends BaseController
{


    /**
     * Manages creation of a new phone
     *
     * @Route("/contacts/{contactId}/phones/new", name="new_phone")
     * @ParamConverter("contact", options={"mapping": {"contactId"   : "id"}} )
     *
     * @param Request $request
     * @param Contact $contact
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showNew(Request $request, Contact $contact)
    {


        $data['phoneTypes'] = PhoneRepository::$phoneTypes;
        $data['contact'] = $contact;
        try {

            //form builder
            $form = ($this->getFormBuilder(PhoneRepository::$columns))->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted()) { //save

                //validate mandatory params
                $formData = $form->getData();
                $this->validateParams(PhoneRepository::$columns, $formData);

                $phone = $this->getDoctrine()
                    ->getRepository(Phone::class)
                    ->findBy(['countryCode'=>$formData['countryCode'],'phone'=>$formData['phone']] );

                if ($phone)
                    throw new \Exception("Country code, phone combination already exists for another contact ");

                //new contact
                $phone = new Phone();
                array_walk(PhoneRepository::$columns, function ($param) use (&$phone, $formData) {
                    $phone->{'set' . (ucfirst($param))}($formData[$param]);
                });


                //store data
                $em = $this->getDoctrine()->getManager();
                $phone->setContact($contact);
                $em->persist($phone);
                $em->flush();


                return $this->redirectToRoute('modify_contact', ['contactId' => $phone->getContact()->getId()]);
            }


        } catch (\Exception $ex) {
            $data['phone'] = $formData ?? ''; //to better user experience
            $data['error_message'] = $ex->getMessage();
        }


        return $this->render('contacts/formPhone.html.twig',
            $data);

    }

    /**
     * Removes a phone from a contact
     *
     * @Route("/contacts/phone/{phoneId}/remove", name="remove_phone")
     * @ParamConverter("phone", options={"mapping": {"phoneId"   : "id"}} )
     *
     * @param Phone $phone
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removePhone(Phone $phone)
    {
        if ($phone) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($phone);
            $em->flush();
        }
        return $this->redirectToRoute('modify_contact', ['contactId' => $phone->getContact()->getId()]);
    }

}
