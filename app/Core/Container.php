<?php

namespace App\Core;

use ReflectionClass;
use ReflectionParameter;

class Container
{
    private array $objects = [];
    private array $resolved = []; // Кэш для уже созданных объектов

    public function has(string $id): bool
    {
        return isset($this->objects[$id]) || isset($this->resolved[$id]) || class_exists($id);
    }

    /**
     * @throws \ReflectionException
     */
    public function get(string $id): mixed
    {
        // Проверяем, есть ли объект в кэше
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        // Проверяем, есть ли объект в контейнере как фабрика
        if (isset($this->objects[$id])) {
            $object = $this->objects[$id]();
            $this->resolved[$id] = $object; // Кэшируем результат
            return $object;
        }

        // Создаем и кэшируем новый объект
        $object = $this->prepareObject($id);
        $this->resolved[$id] = $object;
        return $object;
    }

    /**
     * @throws \ReflectionException
     */
    private function prepareObject(string $class): object
    {
        $classReflector = new ReflectionClass($class);
        $constructReflector = $classReflector->getConstructor();

        // Если конструктора нет или он не имеет параметров
        if (null === $constructReflector || empty($constructArguments = $constructReflector->getParameters())) {
            return new $class;
        }

        // Собираем аргументы конструктора
        $args = $this->resolveConstructorArguments($constructArguments);

        return new $class(...$args);
    }

    /**
     * @param ReflectionParameter[] $constructArguments
     * @return array
     * @throws \ReflectionException
     */
    private function resolveConstructorArguments(array $constructArguments): array
    {
        $args = [];
        foreach ($constructArguments as $argument) {
            $type = $argument->getType();

            // Проверяем тип аргумента
            if ($type === null || $type->isBuiltin()) {
                // Для встроенных типов устанавливаем значение по умолчанию, если оно есть
                if ($argument->isDefaultValueAvailable()) {
                    $args[$argument->getName()] = $argument->getDefaultValue();
                    continue;
                }
                throw new \RuntimeException("Не удается разрешить аргумент {$argument->getName()} без типа или со встроенным типом");
            }

            $argumentType = $type->getName();
            $args[$argument->getName()] = $this->get($argumentType);
        }
        return $args;
    }

    /**
     * Регистрирует фабрику объекта в контейнере
     */
    public function set(string $id, callable $factory): void
    {
        $this->objects[$id] = $factory;
        unset($this->resolved[$id]); // Очищаем кэш, если был
    }
}