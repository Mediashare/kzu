<?php

namespace Kzu;

use Kzu\Config;
use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;

Trait Assets {
    static public function css() {
        $minifier = new CSS();
        foreach (Config::get('framework', 'assets.css') ?? [] as $file):
            $minifier->add(Config::getProjectDir().'/public/'.$file);
        endforeach;
        return $minifier->minify();
    }

    static public function js() {
        $minifier = new JS();
        foreach (Config::get('framework', 'assets.js') ?? [] as $file):
            $minifier->add(Config::getProjectDir().'/public/'.$file);
        endforeach;
        return $minifier->minify();
    }
}
