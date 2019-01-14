<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Email;
use AppBundle\Repository\ContactRepository;
use AppBundle\Repository\EmailRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/***
 * Manages emails requests associated to contacts
 * Class EmailsController
 * @package AppBundle\Controller
 */
class EmailsController extends BaseController
{

    /**
     * Manages creation of a new email account
     *
     * @Route("/contacts/{contactId}/emails/new", name="new_email")
     * @ParamConverter("contact", options={"mapping": {"contactId"   : "id"}} )
     *
     * @param Request $request
     * @param Contact $contact
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showNew(Request $request, Contact $contact)
    {


        $data['emailTypes'] = EmailRepository::$emailTypes;
        $data['contact'] = $contact;
        try {

            //form builder
            $form = ($this->getFormBuilder(EmailRepository::$columns))->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted()) { //save

                //validate mandatory params
                $formData = $form->getData();
                $this->validateParams(EmailRepository::$columns, $formData);

                $email = $this->getDoctrine()
                    ->getRepository(Email::class)
                    ->findByEmail($formData['email']);

                if ($email)
                    throw new \Exception("Email Account already exists for another contact");

                //new contact
                $email = new Email();
                $email->setType($formData['type']);
                $email->setEmail($formData['email']);

                //store datail
                $em = $this->getDoctrine()->getManager();
                $email->setContact($contact);
                $em->persist($email);
                $em->flush();


                return $this->redirectToRoute('modify_contact', ['contactId' => $email->getContact()->getId()]);
            }


        } catch (\Exception $ex) {
            $data['phone'] = $formData ?? ''; //to better user experience
            $data['error_message'] = $ex->getMessage();
        }

        return $this->render('contacts/formEmail.html.twig',
            $data);

    }

    /**
     * Removes email from contact
     *
     * @Route("/contacts/emails/{emailId}/remove", name="remove_email")
     * @ParamConverter("email", options={"mapping": {"emailId"   : "id"}} )
     *
     * @param Email $email
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeEmail(Email $email)
    {

        if ($email) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($email);
            $em->flush();
        }
        return $this->redirectToRoute('modify_contact', ['contactId' => $email->getContact()->getId()]);

    }

}
