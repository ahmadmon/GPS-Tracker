<?php

namespace App\Http\Services\Protocol\Detector;

use App\Http\Services\Protocol\ProtocolAbstract;
use App\Http\Services\Protocol\ProtocolFactory;

class ProtocolDetector
{
    private const PROTOCOL_PATTERNS = [
        'gt06' => [
            'pattern' => '/^(7878|7979)/',
            'is_hex' => true
        ],
        'h02' => [
            'pattern' => '/^\*HQ,.*#$/',
            'is_hex' => false
        ],
        'gps103' => [
            'pattern' => '/^imei:[0-9]+,.*?;$/',
            'is_hex' => false
        ],
        'huabao' => [
            'pattern' => '/^7e.*7e$/',
            'is_hex' => true
        ],
        'watch' => [
            'pattern' => '/^\[([A-Z]{2})\*[0-9]+\*.*\]$/',
            'is_hex' => false
        ]
    ];

    /**
     * @var ProtocolAbstract
     */
    protected ProtocolAbstract $protocol;

    private array $buffer;


    /**
     * Detect protocol from raw message
     *
     * @param string $message
     * @return void
     */
    public function detect(string $message): void
    {
        $this->buffer = [
            'protocol' => null,
            'message' => $this->readBuffer($message)
        ];

        foreach (self::PROTOCOL_PATTERNS as $protocol => $pattern) {
            if (preg_match($pattern['pattern'], $pattern['is_hex'] ? bin2hex($message) : $message)) {
                $this->buffer = [
                    'protocol' => $protocol,
                    'message' => $this->readBuffer($message)
                ];
                break;
            }
        }
    }

    /**
     * @return void
     */
    protected function protocol(): void
    {
        $this->protocol = ProtocolFactory::get($this->buffer['protocol']);
    }

    /**
     * @param string $buffer
     * @return string
     */
    protected function readBuffer(string $buffer): string
    {
        return bin2hex($buffer);
    }


}
