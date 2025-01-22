<?php

namespace App\Http\Services\Protocol\Resource;

abstract class ResourceAbstract
{
    /**
     * @var array
     */
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes($attributes);
    }


    /**
     * Set attributes with available keys.
     *
     * @param array $attributes
     * @return void
     */
    protected function attributes(array $attributes): void
    {
        $this->attributes = array_intersect_key($attributes, array_flip($this->attributesAvailable()));
    }

    /**
     * Get a specific attribute or return the default value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }


    /**
     * Get the data attribute.
     *
     * @return array
     */
    public function data(): array
    {
        return $this->attribute('data', []);
    }



    /**
     * Format the resource as a string.
     *
     * @return string
     */
    abstract public function format(): string;

    /**
     * Validate the resource.
     *
     * @return bool
     */
    abstract public function isValid(): bool;

    /**
     * Get the message of the resource.
     *
     * @return string
     */
    abstract public function message(): string;

    /**
     * Get the serial number of the resource.
     *
     * @return string
     */
    abstract public function serial(): string;

    /**
     * Get the response of the resource.
     *
     * @return string
     */
    abstract public function response(): string;

    /**
     * Define the allowed attributes for the resource.
     *
     * @return array
     */
    abstract protected function attributesAvailable(): array;
}
