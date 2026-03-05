<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;

/* PageController */

class PageController extends Controller
{
    public function cgv(): void
    {
        $this->render('pages/cgv');
    }

    public function mentions(): void
    {
        $this->render('pages/mentions');
    }
}
