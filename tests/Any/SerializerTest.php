<?php

namespace Any\Serializer;


class A
{
    public $closure;

    public $pdo;

    private $privateClosure;

    private $array = [];

    private $globals = [];

    private $que;

    private $b;

    public function __construct()
    {
        $this->closure = $this->privateClosure = function () {};
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo2 = [new \PDO('sqlite::memory:')];
        $this->array = [$this->pdo];
        $this->globals = [$GLOBALS];
        $this->netedArray = [[$this->pdo]];
        $this->netedArray = [[$this->pdo], $this];
        $this->que = new \SplPriorityQueue;
        $this->b = new B;
    }
}

class B
{
    public $c;

    public function __construct()
    {
        $this->c = function(){};
    }
}


class SerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialize()
    {
        $serialized = (new Serializer)->serialize(new A);
        $this->assertInternalType('string', $serialized);
    }
}


