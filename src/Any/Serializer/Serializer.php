<?php
/**
 * This file is part of the BEAR.Serializer package
 *
 * @package BEAR.Serializer
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Any\Serializer;

/**
 * Serialize any object for logging purpose.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
class Serializer implements SerializeInterface
{
    /**
     * @var array
     */
    private $hash = [];

    /**
     * {@inheritdoc}
     */
    public function serialize($value)
    {
        return serialize(
            $this->removeUnserializable($value)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnserializable($value)
    {
        if (is_array($value)) {
            $this->serializeArray($value);
            return $value;
        }
        $hash = spl_object_hash($value);
        if (in_array($hash, $this->hash)) {
            $value = null;

            return $value;
        }
        $this->hash[] = $hash;
        $props = (new \ReflectionObject($value))->getProperties();
        foreach ($props as &$prop) {
            $prop->setAccessible(true);
            $propVal = $prop->getValue($value);
            if (is_array($propVal)) {
                $this->removeUnrealizableInArray($propVal);
                $prop->setValue($value, $propVal);
            }
            if (is_object($propVal)) {
                $propVal = $this->removeUnserializable($propVal);
                $prop->setValue($value, $propVal);

            }
            if ($this->isUnserializable($propVal)) {
                $prop->setValue($value, null);
            }
        }

        return $value;
    }

    /**
     * @param array $array
     */
    public function serializeArray(array &$array)
    {
        foreach ($array as &$item) {
            $this->removeUnserializable($item);
        }
    }
    /**
     * remove Unrealizable In Array
     *
     * @param array &$array
     *
     * @return array
     */
    private function removeUnrealizableInArray(array &$array)
    {
        $this->removeReferenceItemInArray($array);
        foreach ($array as &$value) {
            if (is_object($value)) {
                $value = $this->removeUnserializable($value);
            }
            if (is_array($value)) {
                $this->removeUnrealizableInArray($value);
            }
            if ($this->isUnserializable($value)) {
                $value = null;
            }
        }

        return $array;
    }

    /**
     * Remove reference item in array
     *
     * @param array &$room
     *
     * @return void
     *
     * @see http://stackoverflow.com/questions/3148125/php-check-if-object-array-is-a-reference
     * @author Chris Smith (original source)
     */
    private function removeReferenceItemInArray(array &$room)
    {
        $roomCopy = $room;
        $keys = array_keys($room);
        foreach ($keys as $key) {
            if (is_array($roomCopy[$key])) {
                $roomCopy[$key]['_test'] = true;
                if (isset($room[$key]['_test'])) {
                    // It's a reference
                    unset($room[$key]);
                }
            }
        }
    }

    /***
     * Return is unserialize
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function isUnserializable($value)
    {
        return (is_callable($value) || is_resource($value) || $value instanceof \PDO);
    }
}
