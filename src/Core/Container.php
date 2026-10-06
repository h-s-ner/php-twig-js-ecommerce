<?php
namespace App\Core;

class Container
{
    private array $instances = [];

    public function get(string $class): object
    {
        // Singleton-artig
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        // Kein Constructor
        if (!$constructor) {
            $object = $reflection->newInstance();
            $this->instances[$class] = $object;
            return $object;
        }
        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if (!$type) {
                throw new \RuntimeException(
                    "Keine Type-Hint für {$param->getName()}"
                );
            }
            $dependencies[] = $this->get($type->getName());
        }
        $object = $reflection->newInstanceArgs($dependencies);
        $this->instances[$class] = $object;
        return $object;
    }
}
?>