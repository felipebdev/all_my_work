<?php

namespace App\Console\Commands;

use App\Author;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AddPhotoAuthorToS3Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:author-photo-to-s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script for upload the photo of author to S3 and save on author_photo_url';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('### ADD PHOTO AUTHOR TO S3 COMMAND START ###');
        $authors = Author::select(['id', 'name_author', 'author_photo', 'author_photo_url', 'platform_id'])->get();
        foreach ($authors as $author) {
            if ($author->author_photo !== null) {
                $hasImage = Storage::disk('authorsProfiles')->exists($author->author_photo);
                if ($hasImage) {
                    $filename = $author->author_photo;
                    $path =  Storage::disk('authorsProfiles')->path('');
                    $oldFile = File::get($path . $filename);
                    $file = Storage::disk('images')->put("PLATFORM_UPLOADS/" . $author->platform_id . "/" . $filename, $oldFile);

                    $author->author_photo_url = Storage::disk('images')->url("PLATFORM_UPLOADS/" . $author->platform_id . "/" . $filename, $file);
                    $author->save();
                }
            }
        }
        Log::info('### ADD PHOTO AUTHOR TO S3 COMMAND FINISHED ###');
    }
}
