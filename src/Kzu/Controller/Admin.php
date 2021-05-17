<?php

namespace Kzu\Controller;

use Kzu\Config;
use Kzu\Web\Twig;
use Kzu\Web\Route;
use Kzu\Web\Flash;
use Kzu\Http\Request;
use Kzu\Http\Response;
use Kzu\Storage\Session;
use Kzu\Normalizer\Text;
use Kzu\Normalizer\Yaml;
use Kzu\Database\Database;
use Kzu\Filesystem\Filesystem;
use Kzu\Database\DatabaseQuery;

Trait Admin {
    static public function index() {
        if (Admin::security() === true):
            return Response::return(Twig::view('admin/editor.html.twig', [
                'configs' => array_keys(Config::get()),
                'databases' => Database::list(),
            ]));
        endif;
    }

    static public function database() {
        if (Admin::security() === true):
            $filename = Request::getParameter('filename');
            if ($filename): // Post data
                $content = Request::getParameter('content');
                if (!Yaml::parse($content)):
                    if ($errors = Flash::get('errors')): // Error when Yaml parsing
                        return Response::json(['status' => 'error', 'messages' => $errors], [], 501);
                    endif;
                endif;

                if (!$content): // Content is empty
                    Database::delete($filename);
                    Flash::add('warning', 'Database was removed.');
                    return Response::json(['status' => 'warning', 'redirection' => Route::getRoutePath('admin_database')]);
                endif;

                $persit = Database::persist($filename, Yaml::parse($content));
                if (!$persit): // Error when persist database
                    return Response::json(['status' => 'error', 'messages' => ['Database was not updated.']], [], 501);
                endif;
                // Database recorded
                Flash::add('success', 'Database was updated.');
                return Response::json(['status' => 'success']);
            endif;

            $db_name = Request::getParameter('database');
            $content = Database::get($db_name);
            if (!$content):
                $content = ['config' => ['encrypted' => false, 'model' => []], 'rows' => []]; 
            endif;
            $editor = Yaml::dump($content);
            
            return Response::return(Twig::view('admin/editor.html.twig', [
                'configs' => array_keys(Config::get()),
                'databases' => Database::list(),
                'editor' => $editor,
                'filename' => $db_name
            ]));
        endif;
    }

    static public function config() {
        if (Admin::security() === true):            
            $filename = Request::getParameter('filename');
            if ($filename): // Post data
                $content = Request::getParameter('content');
                if (!Yaml::parse($content)): // Error when Yaml parsing
                    if ($errors = Flash::get('errors')):
                        return Response::json(['status' => 'error', 'messages' => $errors], [], 501);
                    endif;
                endif;
                
                $file = Filesystem::find('../config/'.$filename.'*', ['yaml', 'yml'])[0] ?? null;
                if ((!$file || pathinfo($file)['filename'] !== $filename) && $content): // File not exist
                    Filesystem::write('../config/'.$filename.'.yaml', $content = Text::tabToSpace($content)); // Create config
                    Config::$parameters = null;
                    Flash::add('success', 'Config was created.');
                    return Response::json(['status' => 'success', 'redirection' => Route::getRoutePath('admin_config', ['config' => $filename])]);
                endif;
                
                if (!$content): // Content is empty
                    Filesystem::delete($file);
                    Flash::add('warning', 'Config was removed.');
                    return Response::json(['status' => 'warning', 'redirection' => Route::getRoutePath('admin_config')]);
                endif;

                Filesystem::write($file, $content);
                Flash::add('success', 'Config was updated.');
                return Response::json(['status' => 'success']);
            endif;

            $config_name = Request::getParameter('config');
            if ($config_name):
                $editor = Yaml::dump(Config::get($config_name));
            endif;

            return Response::return(Twig::view('admin/editor.html.twig', [
                'configs' => array_keys(Config::get()),
                'databases' => Database::list(),
                'editor' => $editor ?? "",
                'filename' => $config_name
            ]));
        endif;
    }

    static private function security() {
        if (Session::get('user')):
            $user = DatabaseQuery::findOneBy('users', ['id' => Session::get('user')['id']]);
            if (!$user):
                Flash::add('errors', 'You have not permissions for acces to this page.');
                return Response::redirectTo(Route::getRoutePath('login'));
            elseif (!Session::get('user')['roles']['admin']):
                return Response::redirectTo(Route::getRoutePath('error_404'), 404);
            endif;
        else:
            Flash::add('errors', 'You are not connected.');
            return Response::redirectTo(Route::getRoutePath('login'));
        endif;
        return true;
    }
}