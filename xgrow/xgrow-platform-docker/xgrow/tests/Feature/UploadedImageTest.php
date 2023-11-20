<?php

namespace Tests\Feature;

use App\Services\Storage\UploadedImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadedImageTest extends TestCase
{

    public function testSaving()
    {
        $platformId = '0123456789';
        $image = UploadedFile::fake()->image('test-saving.jpg');
        $disk = Storage::fake();

        $datastore = new UploadedImage($platformId, $image, $disk);
        $stored = $datastore->store();

        $uuid = $datastore->getUuid();

        $disk->assertExists("PLATFORM_UPLOADS/{$platformId}/{$uuid}-test-saving.jpg");

        $disk->assertExists("PLATFORM_UPLOADS/{$platformId}/{$uuid}-test-saving.webp");
    }

    public function testReturn()
    {
        $platformId = '0123456789';
        $image = UploadedFile::fake()->image('test-return.jpg');
        $disk = Storage::fake();

        $datastore = new UploadedImage($platformId, $image, $disk);
        $stored = $datastore->store();

        $uuid = $datastore->getUuid();

        $this->assertEquals(
            "/storage/PLATFORM_UPLOADS/{$platformId}/{$uuid}-test-return.jpg",
            $stored->original
        );

        $this->assertEquals(
            "/storage/PLATFORM_UPLOADS/{$platformId}/{$uuid}-test-return.webp",
            $stored->converted
        );
    }

}
