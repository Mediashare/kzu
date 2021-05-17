<?php

namespace App\Controller;

use Kzu\Web\Twig;
use Kzu\Http\Response;

Trait Main {
    static public function index() {
        return Response::return(Twig::view('app/index.html.twig', []));
    }
}
