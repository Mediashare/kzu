<?php

namespace Kzu;

use Kzu\Web\Flash;
use Kzu\Normalizer\Yaml;
use Kzu\Normalizer\Table;
use Kzu\Filesystem\Filesystem;

Trait Config {
    static public $parameters = [];
    
    static public function get(?string $filename = null, ?string $key = null) {
        if (empty(Config::$parameters)):
            Config::getConfigs(Config::getProjectDir().'/config/*');
        endif;
        if ($filename):
            $parameters = Config::$parameters[$filename][$filename] ?? Config::$parameters[$filename] ?? null;
            if (!$parameters):
                Flash::add('errors', 'File ' . $filename . ' not exist.');
            elseif ($key):
                return Table::arrayOneLine($parameters)[$key] ?? null;
            endif;
            return $parameters;
        else:
            return Config::$parameters;
        endif;
    }

    static public function getProjectDir(): string {
        return dirname(dirname(__DIR__));
    }

    static public function getConfigs(string $directory) {
        foreach (Filesystem::find($directory, ['yaml', 'yml']) as $file):
            Config::$parameters[pathinfo($file)['filename']] = Yaml::parseFile($file);
            foreach (Yaml::$errors ?? [] as $error):
                Flash::add('errors', $error);
            endforeach;
        endforeach;
        
        if (!Filesystem::find($file = Config::getProjectDir().'/config/framework.yaml')):
            Filesystem::write($file, Yaml::dump([
                'env' => 'dev',
                'storage' => [
                    'database' => [
                        'directory' => '/var/databases'
                    ],
                    'session' => [
                        'lifetime' => '+7days'
                    ]
                ],
                'twig' => [
                    'debug' => true,
                    'cache' => false,
                    'template.directory' => '/template'
                ],
                'routes' => [
                    'index' => [
                        'path' => '/',
                        'controller' => 'App\Controller\Main::index'
                    ],
                    'register' => [
                        'path' => '/register',
                        'controller' => 'Kzu\Controller\Authentification::register'
                    ],
                    'login' => [
                        'path' => '/login',
                        'controller' => 'Kzu\Controller\Authentification::login'
                    ],
                    'logout' => [
                        'path' => '/logout',
                        'controller' => 'Kzu\Controller\Authentification::logout'
                    ],
                    'admin' => [
                        'path' => '/admin',
                        'controller' => 'Kzu\Controller\Admin::index'
                    ],
                    'admin_config' => [
                        'path' => '/admin/config/{config}',
                        'controller' => 'Kzu\Controller\Admin::config'
                    ],
                    'admin_database' => [
                        'path' => '/admin/database/{database}',
                        'controller' => 'Kzu\Controller\Admin::database'
                    ],
                    'error_404' => [
                        'path' => '/error/404',
                        'controller' => 'Kzu\Controller\Error::error_404'
                    ],
                ],
                'console' => [
                    'commands' => [
                        'list' => 'Kzu\Command\App::list',
                        'help' => 'Kzu\Command\App::help',
                        'encrypt:text' => 'Kzu\Command\Crypto::encryptText',
                        'encrypt:file' => 'Kzu\Command\Crypto::encryptFile',
                        'decrypt:text' => 'Kzu\Command\Crypto::decryptText',
                        'decrypt:file' => 'Kzu\Command\Crypto::decryptFile',
                        'database:create' => 'Kzu\Command\Database::create',
                        'database:remove' => 'Kzu\Command\Database::remove',
                        'database:query' => 'Kzu\Command\Database::query',
                    ]
                ]
            ]));
            return Config::getConfigs($directory);
        endif;
    }
}