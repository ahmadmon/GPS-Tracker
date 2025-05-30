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
     * @return bool
     */
    public function isValid(): bool
    {
        return boolval($this->serial());
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function serial(): string
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function response(): string
    {
        return $this->attribute(__FUNCTION__);
    }


}
