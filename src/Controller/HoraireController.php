<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Repository\HoraireRepository;

/* HoraireController */

class HoraireController extends Controller
{
    private HoraireRepository $horaireRepository;

    public function __construct()
    {
        $this->horaireRepository = new HoraireRepository();
    }

  public function update(): void
{
    $this->verifyCsrf();
    $horaires = trim($this->post('horaires'));
    
    if (!empty($horaires)) {
        $this->horaireRepository->updateTexte($horaires);
        Session::setFlash('success', 'Horaires mis à jour.');
    }

    // Redirection selon le rôle
    $role = Session::getUserRole();
    if ($role === 'administrateur') {
        $this->redirect('/admin');
    } else {
        $this->redirect('/employe');
    }
}}

