<?php

namespace Any\Serializer;


class A
{
    public $closure;

    public $pdo;

    private $privateClosure;

    private $array = [];

    private $que;

    private $ref;

    private $b;

    public function __construct()
    {
        $this->closure = $this->privateClosure = function () {};
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo2 = [new \PDO('sqlite::memory:')];
        $this->array = [$this->pdo];
        $this->netedArray = [[$this->pdo]];
        $this->netedArray = [[$this->pdo], $this];
        $this->que = new \SplPriorityQueue;
        $this->ref = &$this->pdo;
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

    public function testSerializeArrayType()
    {
        $serialized = (new Serializer)->serialize([new A, new B, [new A]]);
        $this->assertInternalType('string', $serialized);

        return $serialized;
    }

    /**
     * @param $serialized
     *
     * @depends testSerializeArrayType
     */
    public function testSerializeArrayValue($serialized)
    {
        $array = unserialize($serialized);
        $this->assertCount(3, $array);
        $this->assertInstanceOf('Any\Serializer\A', $array[0]);
    }

    public function testScalarInt()
    {
        $result = (new Serializer)->serialize(1);
        $this->assertSame(1, unserialize($result));
    }

    public function testScalarRecursievArray()
    {
        $a = new \stdClass;
        $b = ['b' => $a];
        $a->b = $b;
        $a = (new Serializer)->serialize($a);
        $this->assertSame('O:8:"stdClass":1:{s:1:"b";a:1:{s:1:"b";N;}}', $a);
    }
}
