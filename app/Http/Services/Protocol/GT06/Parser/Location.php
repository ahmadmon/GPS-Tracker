<?php

namespace App\Http\Services\Protocol\GT06\Parser;

use App\Http\Services\Protocol\ParserAbstract;

class Location extends ParserAbstract
{

    /**
     * @return array
     */
    public function resources(): array
    {
        $this->cache = [];
        $this->values = [];

        if ($this->messageIsValid() === false) {
            return [];
        }

        $this->addIfValid($this->resourceLocation());

        return $this->resources;
    }

    /**
     * @return bool
     */
    public function messageIsValid(): bool
    {
        return ($this->data()['serial'] ?? false)
            && (bool)preg_match($this->messageIsValidRegExp(), $this->message, $this->values);
    }

    /**
     * @return string
     */
    protected function messageIsValidRegExp(): string
    {
        return '/^'
            . '(7878)'         //  1 - start
            . '([0-9a-f]{2})'  //  2 - length
            . '(12|22)'        //  3 - protocol
            . '([0-9a-f]{12})' //  4 - datetime
            . '([0-9a-f]{2})'  //  5 - satellites
            . '([0-9a-f]{8})'  //  6 - latitude
            . '([0-9a-f]{8})'  //  7 - longitude
            . '([0-9a-f]{2})'  //  8 - speed
            . '([0-9a-f]{4})'  //  9 - status/signal/direction
            . '([0-9a-f]{4})'  // 10 - mcc
            . '([0-9a-f]{2})'  // 11 - mnc
            . '/';
    }


    /**
     * @return string
     */
    protected function statusValue(): string
    {
        return $this->cache[__FUNCTION__] ??= str_pad(base_convert($this->values[9], 16, 2), 16, '0', STR_PAD_LEFT);
    }

    /**
     * @return string
     */
    protected function serial(): string
    {
        return self::getSerial($this->connectionKey());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ['serial' => $this->serial()];
    }

    /**
     * @return float
     */
    protected function latitude(): float
    {
        return $this->cache[__FUNCTION__] ??= $this->latitudeLongitude(
            $this->values[6],
            ($this->statusValue()[4] === '1')
        );
    }

    /**
     * @return float
     */
    protected function longitude(): float
    {
        return $this->cache[__FUNCTION__] ??= $this->latitudeLongitude(
            $this->values[7],
            ($this->statusValue()[5] === '0')
        );
    }

    /**
     * @param string $hex
     * @param bool $negative
     *
     * @return float
     */
    protected function latitudeLongitude(string $hex, bool $negative): float
    {
        $value = round(hexdec($hex) / 60 / 30000, 5);

        if ($negative) {
            $value *= -1;
        }

        return $value;
    }

    /**
     * @return float
     */
    protected function speed(): float
    {
        return $this->cache[__FUNCTION__] ??= hexdec($this->values[8]);
    }

    /**
     * @return int
     */
    protected function signal(): int
    {
        return $this->cache[__FUNCTION__] ??= intval($this->statusValue()[3]);
    }

    /**
     * @return int
     */
    protected function direction(): int
    {
        return $this->cache[__FUNCTION__] ??= bindec(substr($this->statusValue(), 6));
    }

    /**
     * @return ?string
     */
    protected function datetime(): ?string
    {
        $date = array_map(
            static fn($value) => str_pad(strval(hexdec($value)), 2, '0', STR_PAD_LEFT),
            str_split($this->values[4], 2)
        );

        return '20' . $date[0] . '-' . $date[1] . '-' . $date[2] . ' ' . $date[3] . ':' . $date[4] . ':' . $date[5];
    }

    /**
     * @return string
     */
    protected function response(): string
    {
        return hex2bin("{$this->values[1]}05{$this->values[3]}0001D9DC0D0A");
    }
}
