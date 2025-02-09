<?php

namespace App\Http\Services\Protocol\GT06\Parser;

use App\Http\Services\Protocol\ParserAbstract;
use App\Http\Services\Buffer\Byte as BufferByte;
use App\Http\Services\Buffer\Bit as BufferBit;

class LocationGpsModular extends ParserAbstract
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

        $this->modules();

        $this->addIfValid($this->resourceLocation());

        return $this->resources;
    }


    /**
     * @return bool
     */
    public function messageIsValid(): bool
    {
        return ($this->serial() ?? false)
            && (bool)preg_match($this->messageIsValidRegExp(), $this->message, $this->values);
    }

    /**
     * @return string
     */
    protected function messageIsValidRegExp(): string
    {
        return '/^'
            . '(7979)'        //  1 - start
            . '([0-9a-f]{4})' //  2 - length
            . '(70)'          //  3 - protocol
            . '/';
    }

    /**
     * @return string
     */
    protected function serial(): string
    {
        return self::getSerial($this->connectionKey());
    }

    /**
     * @return void
     */
    protected function modules(): void
    {
        $buffer = new BufferByte(substr($this->message, 10, -8));

        while ($buffer->length() > 6){
            $type = $buffer->string(2);
            $content = $buffer->new($buffer->int(2));

            match ($type) {
                '0011' => $this->moduleCellTower($content),
                '0033' => $this->moduleGps($content),
                '002C' => $this->moduleTimestamp($content),
                default => null,
            };
        }
    }


    /**
     * @param BufferByte $buffer
     *
     * @return void
     */
    protected function moduleGps(BufferByte $buffer): void
    {
        $this->cache['datetime'] = date("Y-m-d H:i:s", $buffer->int(4));

        $this->cache['latitude'] = round($buffer->int(4,7) / 60 / 30000, 5);
        $this->cache['longitude'] = round($buffer->int(4) / 60 / 30000, 5);

        $this->cache['speed'] =  round($buffer->int(1) * 1.852, 2);

        $flags = $buffer->int();

        $this->cache['direction'] = BufferBit::to($flags, 10);
        $this->cache['signal'] = intval(BufferBit::check($flags, 12));

        if(BufferBit::check($flags,10) === false) $this->cache['latitude'] = -$this->cache['latitude'];
        if(BufferBit::check($flags,11)) $this->cache['longitude'] = -$this->cache['longitude'];
    }

    /**
     * @param BufferByte $buffer
     *
     * @return void
     */
    protected function moduleCellTower(BufferByte $buffer): void
    {
        $this->cache['mcc'] = $buffer->int(2);
        $this->cache['mnc'] = $buffer->int(2);
    }

    /**
     * @param BufferByte $buffer
     *
     * @return void
     */
    protected function moduleTimestamp(BufferByte $buffer): void
    {
        $this->cache['datetime'] = date('Y-m-d H:i:s', $buffer->int());
    }

    /**
     * @return ?float
     */
    protected function latitude(): ?float
    {
        return $this->cache[__FUNCTION__] ?? null;
    }

    /**
     * @return ?float
     */
    protected function longitude(): ?float
    {
        return $this->cache[__FUNCTION__] ?? null;
    }

    /**
     * @return ?float
     */
    protected function speed(): ?float
    {
        return $this->cache[__FUNCTION__] ?? null;
    }

    /**
     * @return ?int
     */
    protected function signal(): ?int
    {
        return $this->cache[__FUNCTION__] ?? null;
    }

    /**
     * @return ?int
     */
    protected function direction(): ?int
    {
        return $this->cache[__FUNCTION__] ?? null;
    }

    /**
     * @return ?string
     */
    protected function datetime(): ?string
    {
        return $this->cache[__FUNCTION__] ?? null;
    }


    /**
     * @return string
     */
    protected function response(): string
    {
        return hex2bin("{$this->values[1]}05{$this->values[3]}0001D9DC0D0A");
    }
}
