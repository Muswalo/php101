<?php

/**
 * Base Two Technologies (https://basetwotech.com)
 * @package   Base Two Technologies
 * @author    Emmanuel Muswalo
 * @email     emuswalo7@gmail.com
 * @copyright Copyright (c) 2023, Base Two Technologies
 * @license   MIT license 
 * @country   Zambia
 */

 namespace Muswalo\Php101;

class Async {
    private $callback; private $process;

    public function __construct(callable $callback){
        $this->callback = $callback;
    }

    public function Async (...$params) {
        $serialiseCallback = serialize(new SerializableClosure ($this->callback));
        $command = "php -r'unserialize(\"$serialiseCallback\")(...".var_export($params, true).");'";
        $descriptor_spec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'w']
        ];

        $this->process = proc_open($command, $descriptor_spec, $pipes);

        if (is_resource($this->process)) {

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
        }
    }

    public function Await () {

        if (is_resource($this->process)) {

            proc_close ($this->process);

        }
    }
}

class SerializableClosure {
    private $closure;

    public function __construct ($closure) {
        $this->closure = $closure;
    }

    public function __invoke (...$args) {
        return ($this->closure)(...$args);
    }

    public function __sleep () {
        throw new \Exception ('cannot serialize serializedclosure');
    }

    public function __awake () {
        throw new \Exception ('cnnot unserialize serializedclosure');
    }
}