Any.Serializer
===============

Any.Serializer enables to serialize any object. It removes all contained unserializable item (such as resource, closure) for logging purpose.

Requirement
-----------

 * PHP 5.3+

Usage
-----
```php
$serializedText = (new Serializer)->serialize($object);
```
