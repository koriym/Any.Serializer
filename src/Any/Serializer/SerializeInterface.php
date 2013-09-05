<?php
/**
 * This file is part of the BEAR.Serializer package
 *
 * @package BEAR.Serializer
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Any\Serializer;

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
     * @param $value
     *
     * @return string
     */
    public function serialize($value);

    /**
     * Remove unserializable item
     *
     * @param $value
     *
     * @return mixed
     */
    public function removeUnserializable($value);
}
