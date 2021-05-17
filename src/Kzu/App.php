<?php

namespace Kzu;

use Kzu\Web\Twig;
use Kzu\Web\Route;
use Tracy\Debugger;
use Kzu\Http\Request;
use Kzu\Http\Response;
use Kzu\Security\Crypto;
use Kzu\Storage\Session;
use Kzu\Database\Database;
use phpseclib3\Crypt\Random;
use Kzu\Filesystem\Filesystem;

Class App {
    public function run() {
        if (empty(Config::get('framework', 'env')) || Config::get('framework', 'env') !== 'prod'):
            Debugger::enable();
            debug_backtrace();
        endif;
        // Routing & Template
        Route::$routes = Config::get('framework', 'routes');
        Twig::$template_directory = Config::getProjectDir().Config::get('framework', 'twig.template.directory');
        if (Config::get('framework', 'twig.cache') !== false):
            Twig::$cache = Config::getProjectDir().Config::get('framework', 'twig.cache');
        else: Twig::$cache = Config::get('framework', 'twig.cache'); endif;
        Twig::$debug = Config::get('framework', 'twig.debug');
        // Security
        Crypto::$secret = Filesystem::read($private_key = Config::getProjectDir() . '/config/private.key');
        if (!Crypto::$secret): Filesystem::write($private_key, Crypto::$secret = Random::string(32)); endif;
        Filesystem::$crypto_secret = Crypto::$secret;
        // Database
        Database::$databases_directory = Config::getProjectDir() . Config::get('framework', 'storage.database.directory');
        // Session
        Session::$session_lifetime = Config::get('framework', 'storage.session.lifetime');
        Session::set('ip', Request::getIp());

        if ($route = Route::getRoute()):
            $route['class']::{$route['method']}();
        else: Response::redirectTo(Route::getRoutePath('error_404')); endif;
    }
}