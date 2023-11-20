<?php

namespace App\Services\Objects;

class Result
{
    private bool $error;
    private string $message;
    private $data;

    public static function ok(string $message, $data = []): self
    {
        return new self(false, $message, $data);
    }

    public static function failed(string $message, $data = []): self
    {
        return new self(true, $message, $data);
    }


    protected function __construct(bool $error, string $message, $data)
    {
        $this->error = $error;
        $this->message = $message;
        $this->data = $data;
    }


    public function isError(): bool
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * WARNING: this method can return anything (null, object, array, etc)
     *
     * @return mixed
     */
    public function getUnsafeData()
    {
        return $this->data;
    }




}
