<?php

namespace CacheSystem\Command;

use CacheSystem\Command\Functions\MainFunction;
use CacheSystem\Command\Interfaces\CommandInterface;
use CacheSystem\Serializer\Interfaces\SerializerInterface;

class FileCommand extends MainFunction implements CommandInterface
{
    /**
     * FileCommand constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        parent::__construct(app('cache'), $serializer);
    }

    public function setMany(array $values, int $minutes = 0): self
    {
        foreach ($values as $key => $data)
            $this->set($key, $data, $minutes);

        return $this;
    }

    public function set(string $key, $data, int $minutes = 0): self
    {
        $rawData = $this->_serializeData($data);

        if (0 === $minutes)
            $this->manager->forever($key, $rawData);
        else
            $this->manager->put($key, $rawData, $minutes);

        return $this;
    }

    public function getMany(array $keys, ?SerializerInterface $serializer = null): array
    {
        $data = array();
        foreach ($keys as $key)
            $data[$key] = $this->get($key, $serializer);
        return $data;
    }

    public function get($key, ?SerializerInterface $serializer = null)
    {
        $rawData = $this->manager->get($key);
        return $this->_unserializeData($rawData, $serializer);
    }

    public function forgetMany($keys, ...$otherKeys): array
    {
        $forgets = array();

        if (!is_array($keys))
            $forgets = (array)$keys;
        if (!empty($otherKeys))
            $forgets = array_unique(array_merge($forgets, $otherKeys));
        unset($keys, $otherKeys);

        $checks = array();
        foreach ($forgets as $index => $key)
            if (is_string($key) && !array_key_exists($key, $checks))
                $checks[$key] = $this->manager->forget($key);

        return $checks;
    }


    /**
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return $this->manager->forget($key);
    }

    /**
     * @return FileCommand
     */
    public function clear(): self
    {
        $this->manager->flush();
        return $this;
    }
}