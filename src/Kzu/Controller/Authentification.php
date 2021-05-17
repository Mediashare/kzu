<?php

namespace Kzu\Controller;


use Kzu\Web\Twig;
use Kzu\Web\Route;
use Kzu\Web\Flash;
use Kzu\Http\Request;
use Kzu\Http\Response;
use Kzu\Normalizer\Text;
use Kzu\Storage\Session;
use Kzu\Security\Crypto;
use Kzu\Database\Database;
use Kzu\Database\DatabaseQuery;

Trait Authentification {
    static public function register() {
        if (Request::getParameter('email')
        && Request::getParameter('username')
        && Request::getParameter('password')
        && Request::getParameter('password_validation')):
            $error = false;
            
            if (!Database::get('users')):
                $database = Database::create('users', [], true);
                if (!$database):
                    $error = Flash::add('errors', 'Database can not be created.');
                endif;
            endif;

            if (Request::getParameter('password') !== Request::getParameter('password_validation')):
                $error = Flash::add('errors', 'Password is not validated.');
            endif;
            if (DatabaseQuery::findOneBy('users', ['slug' => $slug = Text::slugify($username = Request::getParameter('username'))])):
                $error = Flash::add('errors', 'Username already exist.');
            endif;
            if (DatabaseQuery::findOneBy('users', ['email' => $email = Request::getParameter('email')])):
                $error = Flash::add('errors', 'Email already exist.');
            endif;

            if (!$error ?? true):
                DatabaseQuery::insert('users', [$user = [
                    'id' => uniqid(),
                    'username' => $username,
                    'email' => $email,
                    'slug' => $slug,
                    'password' => Crypto::encrypt(Request::getParameter('password')),
                    'roles' => [
                        'user' => true,
                        'moderator' => false,
                        'admin' => true
                    ]
                ]]);
                unset($user['password']);
                Session::set('user', $user);
                Flash::add('success', 'User registration success.');
                return Response::redirectTo(Route::getRoutePath('index'));
            endif;
        endif;
        return Response::return(Twig::view('app/register.html.twig'));
    }

    static public function login() {
        if (Request::getParameter('username') && Request::getParameter('password')):
            $username = Request::getParameter('username');
            $user = DatabaseQuery::findOneBy('users', [
                    'username' => $username,
                    'password' => Crypto::encrypt(Request::getParameter('password'))
                ], false) ?? DatabaseQuery::findOneBy('users', [
                    'email' => $username,
                    'password' => Crypto::encrypt(Request::getParameter('password'))
                ], false);
            if (!$user):
                Flash::add('errors', 'User not found.');
            else:
                unset($user['password']);
                Session::set('user', $user);
                Flash::add('success', 'User connected.');
                return Response::redirectTo(Route::getRoutePath('index'));
            endif;
        endif;
        return Response::return(Twig::view('app/login.html.twig'));
    }

    static public function logout() {
        Session::set('user', null);
        Flash::add('success', 'User disconnected.');
        return Response::redirectTo(Route::getRoutePath('index'));
    }
}