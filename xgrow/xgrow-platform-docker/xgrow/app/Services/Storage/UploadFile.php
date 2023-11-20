<?php

namespace App\Services\Storage;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFile
{

    private string $platformId;
    private string $originalFilename;
    private string $originalExtension;
    private string $uuid;
    private UploadedFile $file;
    private FilesystemAdapter $disk;

    /**
     * UploadFile constructor.
     *
     * @param  string  $platformId
     * @param  \Illuminate\Http\UploadedFile  $uploadedFile
     * @param  \Illuminate\Filesystem\FilesystemAdapter|null  $disk  Set Storage Disk, if null uses default
     */
    public function __construct(string $platformId, UploadedFile $uploadedFile, ?FilesystemAdapter $disk = null)
    {
        $this->setPlatformId($platformId);

        $this->originalFilename = $uploadedFile->getClientOriginalName();
        $this->originalExtension = $uploadedFile->getClientOriginalExtension();
        $this->file = $uploadedFile;
        $this->uuid = Str::uuid();

        is_null($disk) ? $this->setDisk(Storage::disk('images')) : $this->setDisk($disk);
    }

    public function setDisk(FilesystemAdapter $disk)
    {
        $this->disk = $disk;
        return $this;
    }

    public function setPlatformId(string $platformId)
    {
        $this->platformId = $platformId;
        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Store file on defined Storage Disk
     *
     * @return \App\Services\Objects\ConvertedImageUrl
     */
    public function store(): string
    {
        $path = $this->generateImagePath($this->originalExtension);
        return $this->putAndGetUrl($path, $this->file);
    }

    protected function generateImagePath(string $extension): string
    {
        $filenameWithoutExtension = pathinfo($this->originalFilename, PATHINFO_FILENAME);
        return "PLATFORM_UPLOADS/{$this->platformId}/{$this->uuid}-{$filenameWithoutExtension}.{$extension}";
    }

    private function putAndGetUrl(string $path, UploadedFile $file): ?string
    {
        $stored = $this->disk->put($path, $file);

        if (!$stored) {
            return null;
        }

        return $this->disk->url($path);
    }
}
