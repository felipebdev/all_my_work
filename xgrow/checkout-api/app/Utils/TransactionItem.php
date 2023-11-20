<?php

namespace App\Utils;

class TransactionItem
{   
    public $id;
    public $amount;
    public $description;
    public $category;
    public $code;

    public function __construct(
        string $id,
        int $amount,
        string $description,
        string $code,
        ?string $category = null
    ) {
        $this->id = $id;
        $this->amount = $amount;
        $this->description = $description;
        $this->code = $code;
        $this->category = (
            (empty($category) || $category === 'product') ? 
            'default' : 
            $category
        );
    }
}