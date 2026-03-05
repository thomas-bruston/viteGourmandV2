<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Entity\Contact;
use Repository\ContactRepository;
use Service\MailService;

/* ContactController */

class ContactController extends Controller
{
    private ContactRepository $contactRepository;
    private MailService       $mailService;

    public function __construct()
    {
        $this->contactRepository = new ContactRepository();
        $this->mailService       = new MailService();
    }

    public function index(): void
    {
        $this->render('pages/contact', [
            'csrf_token' => Session::generateCsrfToken(),
            'error'      => Session::getFlash('error'),
            'success'    => Session::getFlash('success'),
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        try {
            $contact = new Contact();
            $contact->setNom(trim($this->post('nom')));
            $contact->setPrenom(trim($this->post('prenom')));
            $contact->setEmail(trim($this->post('email')));
            $contact->setTitre(trim($this->post('titre')));
            $contact->setMessage(trim($this->post('message')));

            $this->contactRepository->create($contact);

            Session::setFlash('success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
            $this->redirect('/contact');

        } catch (\InvalidArgumentException $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/contact');
        }
    }

    /* Messages */
    
    public function adminIndex(): void
    {
        $messages = $this->contactRepository->findAll();

        $this->render('employee/messages/index', [
            'messages' => $messages,
            'success'  => Session::getFlash('success'),
        ]);
    }

    public function delete(): void
    {
        $this->verifyCsrf();
        $contactId = (int) $this->post('contact_id');
        $this->contactRepository->delete($contactId);
        Session::setFlash('success', 'Message supprimé.');
        $this->redirect('/employe/messages');
    }
}
