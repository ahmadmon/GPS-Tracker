<?php

namespace App\Http\Services\Protocol\GT06\Parser;

use App\Http\Services\Protocol\ParserAbstract;

class HeartBeat extends ParserAbstract
{

    /**
     * @return array
     */
    public function resources(): array
    {
        $this->values = [];

        if ($this->messageIsValid() === false) {
            return [];
        }

        $this->addIfValid($this->resourceHeartbeat());

        return $this->resources;
    }


    /**
     * @return bool
     */
    public function messageIsValid(): bool
    {
        return ($this->data['device_id'] ?? false)
            && (bool)preg_match($this->messageIsValidRegExp(), $this->message, $this->values);
    }

    /**
     * @return string
     */
    protected function messageIsValidRegExp(): string
    {
        return '/^'
            . '(7878)'        // 1 - start
            . '([0-9a-f]{2})' // 2 - length
            . '(13|23)'       // 3 - protocol
            . '/';
    }

    /**
     * @return string
     */
    protected function serial(): string
    {
        return $this->data['device_id'];
    }

    /**
     * @return string
     */
    protected function response(): string
    {
        return hex2bin("{$this->values[1]}05{$this->values[3]}0001D9DC0D0A");
    }
}