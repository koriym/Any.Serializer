BEAR.Serializer
===============

BEAR.Serializer enables to serialize any object. It removes all contained unserializable items (such as resource, closure) for logging purpose.
Requirement
-----------

 * PHP 5.4+

Usage
-----
```php
$serializedText = (new Serializer)->serialize($object);
```
