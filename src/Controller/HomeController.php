<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Repository\AvisRepository;
use Repository\HoraireRepository;

/*HomeController */

class HomeController extends Controller
{
    private AvisRepository    $avisRepository;
    private HoraireRepository $horaireRepository;

    public function __construct()
    {
        $this->avisRepository    = new AvisRepository();
        $this->horaireRepository = new HoraireRepository();
    }

    public function index(): void
    {
        $avis    = $this->avisRepository->findValides();
        $horaire = $this->horaireRepository->getTexte();

        $this->render('home/index', [
            'avis'    => $avis,
            'horaire' => $horaire,
            'error'   => Session::getFlash('error'),
            'success' => Session::getFlash('success'),
        ]);
    }
}
