<?php


namespace App\Services\Objects;

class ConvertedImageUrl
{
    /**
     * @var string|null Url to original image (null if failed)
     */
    public ?string $original;

    /**
     * @var string|null Url to converted image (null if failed)
     */
    public ?string $converted;
}
