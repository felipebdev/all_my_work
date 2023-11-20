<?php

namespace App\Services\Storage;

use App\Services\Objects\ConvertedImageUrl;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;

class UploadedImage
{

    private string $platformId;
    private string $originalFilename;
    private string $originalExtension;
    private string $uuid;
    private InterventionImage $image;
    private FilesystemAdapter $disk;
    private UploadedFile $favicon;

    /**
     * UploadedImage constructor.
     *
     * @param  string  $platformId
     * @param UploadedFile $uploadedImage
     * @param FilesystemAdapter|null  $disk  Set Storage Disk, if null uses default
     */
    public function __construct(string $platformId, UploadedFile $uploadedImage, ?FilesystemAdapter $disk = null)
    {
        $this->setPlatformId($platformId);

        $this->originalFilename = $uploadedImage->getClientOriginalName();
        $this->originalExtension = $uploadedImage->getClientOriginalExtension();
        $this->uuid = Str::uuid();

        // Save .ico (image/vnd.microsoft.icon) files without
        // using Intervention to avoid compability errors
        if ($uploadedImage->extension() == "ico") {
            $this->favicon = $uploadedImage;
        } else {
            $this->image = Image::make($uploadedImage);
        }

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
     * Store original and converted image on defined Storage Disk
     *
     * @return ConvertedImageUrl
     */
    public function store(): ConvertedImageUrl
    {
        $return = new ConvertedImageUrl();
        $return->original = $this->storeOriginalImage();
        $return->converted = $this->storeWebpImage();
        return $return;
    }

    /**
     * Store original ico image on defined Storage Disk
     */
    public function storeFavicon(): ?string
    {
        $diskpath = "PLATFORM_UPLOADS/{$this->platformId}/";
        $filenameWithoutExtension = pathinfo($this->originalFilename, PATHINFO_FILENAME);
        $newfilename = "{$this->uuid}-{$filenameWithoutExtension}.{$this->originalExtension}";

        $stored = $this->disk->putFileAs($diskpath, $this->favicon, $newfilename);

        if (!$stored) {
            return null;
        }
        return $this->disk->url($diskpath.$newfilename);
    }

    protected function generateImagePath(string $extension): string
    {
        $filenameWithoutExtension = pathinfo($this->originalFilename, PATHINFO_FILENAME);
        $filenameWithoutExtension = sanitizeString($filenameWithoutExtension);
        return "PLATFORM_UPLOADS/{$this->platformId}/{$this->uuid}-{$filenameWithoutExtension}.{$extension}";
    }

    private function storeOriginalImage()
    {
        $path = $this->generateImagePath($this->originalExtension);
        return $this->putAndGetUrl($path, $this->image);
    }

    private function putAndGetUrl(string $path, InterventionImage $image): ?string
    {
        $stored = $this->disk->put($path, $image);

        if (!$stored) {
            return null;
        }

        return $this->disk->url($path);
    }

    private function storeWebpImage(): ?string
    {
        $path = $this->generateImagePath('webp');
        $webp = $this->convertToWebp();
        return $this->putAndGetUrl($path, $webp);
    }

    private function convertToWebp(): InterventionImage
    {
        return $this->image->encode('webp');
    }

}
