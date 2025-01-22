<?php

namespace App\Http\Services\Protocol\Resource;

use App\Http\Services\Protocol\Resource\ResourceAbstract;

class Auth extends ResourceAbstract
{

    /**
     * @return array
     */
    protected function attributesAvailable(): array
    {
        return ['message', 'serial' , 'data' , 'response'];
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return 'auth';
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return boolval($this->serial());
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function serial(): string
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @inheritDoc
     */
    public function response(): string
    {
        return $this->attribute(__FUNCTION__);
    }


}
