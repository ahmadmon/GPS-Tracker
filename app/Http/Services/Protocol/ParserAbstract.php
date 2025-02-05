<?php

namespace App\Http\Services\Protocol;

use App\Http\Services\Protocol\Resource\Auth as ResourceAuth;
use App\Http\Services\Protocol\Resource\HeartBeat as ResourceHeartBeat;
use App\Http\Services\Protocol\Resource\Location as ResourceLocation;
use App\Http\Services\Protocol\Resource\ResourceAbstract;
use App\Traits\Protocol\SerialHelper;

abstract class ParserAbstract
{
    use SerialHelper;

    /**
     * @var array
     */
    protected array $values = [];

    /**
     * @var array<ResourceAbstract>
     */
    protected array $resources = [];

    /**
     * @var array
     */
    protected array $cache = [];

    /**
     * @return self
     */
    public static function new(): self
    {
        return new static(...func_get_args());
    }

    /**
     * @param string $message
     * @param array $data = []
     *
     * @return void
     */
    public function __construct(protected string $message,protected string $connectionId, protected array $data = []) {}

    /**
     * @return array<ResourceAbstract>
     */
    public function resources(): array
    {
        return $this->resources;
    }


    /**
     * @param ResourceAbstract $resource
     * @return void
     */
    protected function add(ResourceAbstract $resource): void
    {
        $this->resources[] = $resource;
    }

    /**
     * @param ResourceAbstract|null $resource
     * @return void
     */
    protected function addIfValid(?ResourceAbstract $resource): void
    {
        if ($resource?->isValid()) {
            $this->add($resource);
        }
    }

    /**
     * @return string
     */
    protected function message(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return $this->data;
    }

    /**
     * @return int
     */
    protected function connection(): int
    {
        return (int)$this->connectionId;
    }

    /**
     * @return ResourceAuth
     */
    protected function resourceAuth(): ResourceAuth
    {
        return new ResourceAuth([
            'message' => $this->message(),
            'serial' => $this->serial(),
            'response' => $this->response(),
            'data' => $this->data(),
        ]);
    }


    /**
     * @return ResourceHeartBeat
     */
    protected function resourceHeartbeat(): ResourceHeartbeat
    {
        return new ResourceHeartbeat([
            'message' => $this->message(),
            'serial' => $this->serial(),
            'data' => $this->data(),
            'response' => $this->response(),
        ]);
    }


    /**
     * @return ResourceLocation
     */
    protected function resourceLocation(): ResourceLocation
    {
        return new ResourceLocation([
            'message' => $this->message(),
            'serial' => $this->serial(),
            'lat' => $this->latitude(),
            'long' => $this->longitude(),
            'speed' => $this->speed(),
            'signal' => $this->signal(),
            'direction' => $this->direction(),
            'datetime' => $this->datetime(),
            'data' => $this->data(),
            'response' => $this->response(),
        ]);
    }
}
