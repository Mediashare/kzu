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
    }
}