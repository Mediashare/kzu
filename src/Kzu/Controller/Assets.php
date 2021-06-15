<?php

namespace Kzu\Controller;

use Kzu\Http\Response;
use Kzu\Assets as Asset;

Trait Assets {
    static public function css () {
        return Response::return(Asset::css(), ['Cache-Control' => 'public, max-age=31536000', 'Content-Type' => 'text/css'], 200);
    }
    static public function js () {
        return Response::return(Asset::js(), ['Cache-Control' => 'public, max-age=31536000', 'Content-Type' => 'application/javascript'], 200);
    }
}