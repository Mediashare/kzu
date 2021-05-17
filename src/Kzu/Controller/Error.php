<?php

namespace Kzu\Controller;

use Kzu\Http\Response;
use Kzu\Web\Twig;

Trait Error {
    static public function error_404() {
        Response::return(Twig::view('app/404.html.twig'), [], 404);
    }
}