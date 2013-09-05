<?php
/**
 * This file is part of the BEAR.Serializer package
 *
 * @package BEAR.Serializer
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Any\Serializer;

/**
 * Serializer
 *
 * Serialize any object for logging purpose.
 *
 * @package BEAR.Serializer
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
final class Serializer implements SerializeInterface
{
    /**
     * @var array
     */
    private $hash = [];

    /**
     * {@inheritdoc}
     */
    public function serialize($object)
    {
        return serialize(
            $this->removeUnserializable($object)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnserializable($object)
    {
        $hash = spl_object_hash($object);
        if (in_array($hash, $this->hash)) {
            $object = null;

            return $object;
        }
        $this->hash[] = $hash;
        $props = (new \ReflectionObject($object))->getProperties();
        foreach ($props as &$prop) {
            $prop->setAccessible(true);
            $propVal = $prop->getValue($object);
            if (is_array($propVal)) {
                $this->removeUnserializableInArray($propVal);
                $prop->setValue($object, $propVal);
            }
            if (is_object($propVal)) {
                $propVal = $this->removeUnserializable($propVal);
                $prop->setValue($object, $propVal);

            }
            if ($this->isUnserializable($propVal)) {
                $prop->setValue($object, null);
            }
        }

        return $object;
    }

    /**
     * removeUnserializableInArray
     *
     * @param array &$array
     *
     * @return array
     */
    private function removeUnserializableInArray(array &$array)
    {
        $this->removeReferenceItemInArray($array);
        foreach ($array as &$value) {
            if (is_object($value)) {
                $value = $this->removeUnserializable($value);
            }
            if (is_array($value)) {
                $this->removeUnserializableInArray($value);
            }
            if ($this->isUnserializable($value)) {
                $value = null;
            }
        }
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
     * Return is unserializable
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
