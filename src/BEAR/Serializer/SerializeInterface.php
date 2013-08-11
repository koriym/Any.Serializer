<?php
/**
 * This file is part of the BEAR.Serializer package
 *
 * @package BEAR.Serializer
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace BEAR\Serializer;

/**
 * Interface for Serialize
 *
 * @package BEAR.Serializer
 */
interface SerializeInterface
{
    /**
     * Serialize
     *
     * @param $object
     *
     * @return string
     */
    public function serialize($object);

    /**
     * Remove unserializable item
     *
     * @param $object
     *
     * @return mixed
     */
    public function removeUnserializable($object);
}