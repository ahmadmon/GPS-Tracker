<?php

namespace App\Http\Services\Protocol;

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
     * @param array $data = []
     *
     * @return array
     */
    public function resources(string $message, array $data = []): array
    {
        $resources = [];

        foreach ($this->messages($message) as $message) {
            foreach ($this->parsers() as $parser) {
                $valid = $parser::new($message, $data)->resources();

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
