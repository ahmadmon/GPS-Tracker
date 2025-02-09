<?php

namespace App\Http\Services\Protocol;

use Workerman\Connection\TcpConnection;

abstract class ProtocolAbstract
{
    /**
     * @return string
     */
    abstract public function code(): string;

    /**
     * @return string
     */
    abstract public function name(): string;

    /**
     * @param string $message
     *
     * @return array
     */
    abstract public function messages(string $message): array;

    /**
     * @return array
     */
    abstract protected function parsers(): array;


    /**
     * @param string $message
     * @param TcpConnection $connection
     * @param array $data = []
     *
     * @return array
     */
    public function resources(string $message, TcpConnection $connection, array $data = []): array
    {
        $resources = [];

        foreach ($this->messages($message) as $message) {
            foreach ($this->parsers() as $parser) {
                $valid = $parser::new($message, $connection, $data)->resources();

                if (empty($valid)) {
                    continue;
                }

                $resources = array_merge($resources, $valid);

                break;
            }
        }

        return $resources;
    }
}
