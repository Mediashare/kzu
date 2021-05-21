<?php

namespace Kzu;

use Kzu\Web\Route;
use Tracy\Debugger;
use Kzu\Storage\Session;
use Kzu\Security\Crypto;
use Kzu\Database\Database;
use phpseclib3\Crypt\Random;
use Kzu\Filesystem\Filesystem;

Trait Command {
    static public $arguments;
    static public $commands;
    static public $output;
    static public $name;

    static public function run(array $arguments) {
        if (empty(Config::get('framework', 'env')) || Config::get('framework', 'env') !== 'prod'):
            Debugger::enable();
            debug_backtrace();
        endif;
        
        // Security
        Crypto::$secret = Filesystem::read($private_key = Config::getProjectDir() . '/config/private.key');
        if (!Crypto::$secret): Filesystem::write($private_key, Crypto::$secret = Random::string(32)); endif;
        Filesystem::$crypto_secret = Crypto::$secret;
        // Database
        Database::$databases_directory = Config::getProjectDir() . Config::get('framework', 'storage.database.directory');
        // Session
        Session::$session_lifetime = Config::get('framework', 'storage.session.lifetime');
        
        // Commands
        Command::$commands = Config::get('framework', 'commands');
        $command = Command::getCommand($arguments);
        if ($command):
            $command['class']::{$command['method']}();
        else: echo "Command not found."; return false; endif;
    }
    
    /**
     * Get command from arguments parsed
     * @param array $arguments ($argv)
     * @return array|null
     */
    static public function getCommand(array $arguments): ?array {
        Command::$arguments = $arguments;
        if (empty($arguments[1])): return null; endif;
        Command::$name = Command::$arguments[1];
        $command = Command::getAction();
        if ($command):
            return $command;
        else: return null; endif;
    }

    /**
     * Get controller & method correlated with Command::$name
     * @return array|null [$controller, $method, $name]
     */
    static public function getAction(): ?array {
        foreach (Command::$commands ?? [] as $command_name => $command):
            if (Command::$name === $command_name):
                if (strpos($command, '::') !== false):
                    $route = explode('::', $command);
                    return [
                        'name' => $command_name,
                        'class' => $route[0],
                        'method' => $route[1],
                    ];
                endif;
            endif;
        endforeach;
        return null; // Route not found
    }
}
