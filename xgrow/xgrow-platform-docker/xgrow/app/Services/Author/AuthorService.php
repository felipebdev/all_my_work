<?php

namespace App\Services\Author;

use App\Author;
use App\Content;
use App\Course;
use App\Helpers\SecurityHelper;
use App\Jobs\LACache\ClearContent;
use App\Jobs\LACache\ClearCourse;
use App\Jobs\LACache\ClearSection;
use App\Repositories\Authors\AuthorRepository;
use App\Services\Storage\UploadedImage;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthorService
{
    private AuthorRepository $authorRepository;

    public function __construct(
        AuthorRepository $authorRepository
    )
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * Get all authors by platformId
     * @return mixed
     */
    public function getAuthors()
    {
        return Author::select(['id', 'name_author AS name'])
            ->where('status', 1)
            ->where('platform_id', Auth::user()->platform_id)
            ->get();
    }

    /**
     * @param string|null $searchTerm
     * @return Builder
     */
    public function list(?string $searchTerm): Builder
    {
        $platformId = Auth::user()->platform_id;
        return $this->authorRepository->list($platformId, $searchTerm);
    }

    /**
     * @param int $id
     * @return Model
     * @throws Exception
     */
    public function show(int $id): Model
    {
        $this->security($id);
        return $this->authorRepository->findById($id);
    }

    /**
     * @param array $data
     * @param $image
     * @return Model
     */
    public function store(array $data, $image): Model
    {
        $data['status'] = isset($request->status);
        $data['platform_id'] = Auth::user()->platform_id;
        $data['author_photo_url'] = $this->uploadImage($data['platform_id'], $image);
        return $this->authorRepository->baseCreate($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     * @throws GuzzleException
     */
    public function update(int $id, array $data, $image): Model
    {
        $this->security($id);
        $data['author_photo_url'] = $this->uploadImage(Auth::user()->platform_id, $image);
        $author = $this->authorRepository->baseUpdate($id, $data);
        $this->clearCache($id);
        return $author;
    }

    /**
     * Delete an author
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function delete(int $id)
    {
        $this->security($id);
        $this->authorRepository->baseDelete($id);
    }

    /**
     * Change status from author
     * @param int $id
     * @return Model
     * @throws Exception
     */
    public function changeStatus(int $id): Model
    {
        $author = $this->security($id);
        $data['status'] = (int)!$author->status;
        return $this->authorRepository->baseUpdate($id, $data);
    }

    /**
     * Delete photo an author
     * @param $id
     * @return Model
     * @throws Exception
     */
    public function deletePhoto($id): Model
    {
        $author = $this->security($id);
        Storage::disk('authorsProfiles')->delete($author['author_photo_url']);
        return $this->authorRepository->baseUpdate(
            $id,
            ['author_photo_url' => null]
        );
    }


    /**
     * Transfer content author
     * @param int $origin
     * @param int $destination
     * @param string $pass
     * @return mixed
     * @throws Exception
     */
    public function transferContent(int $origin, int $destination, string  $pass)
    {
        $this->security($origin);
        $this->security($destination);
        if (Hash::check($pass, Auth::user()->password)) {
            $data = Content::where(
                'author_id',
                $origin
            )->update(['author_id' => $destination]);
            if ($data > 0) {
                $contents = Content::where('author_id', $destination)->get();
                foreach ($contents as $content) {
                    ClearContent::dispatch($content->id);
                }
                return $data;
            }
            throw new Exception('Não haviam registros para serem atualizados!');
        } else {
            throw new Exception('Senha incorreta!');
        }
    }


    /**
     * @param $id
     * @return Model
     * @throws Exception
     */
    private function security($id): Model
    {
        $author = $this->authorRepository->findById($id);
        (new SecurityHelper)->securityUser($author);
        return $author;
    }

    private function uploadImage($platform_id, ?UploadedFile $uploadedFile): ?string
    {
        $image = null;
        if ($uploadedFile) {
            $uploadImage = new UploadedImage($platform_id, $uploadedFile, Storage::disk('images'));
            $stored = $uploadImage->store();
            $image = $stored->converted;
        }
        return $image;
    }


    /**
     * Clear cache from Author
     * @param int $id
     * @return void
     * @throws GuzzleException
     */
    private function clearCache(int $id): void
    {
        $contents = Content::where('author_id', $id)->get();
        $courses = Course::where('author_id', $id)->get();

        foreach ($contents as $content) {
            ClearContent::dispatch($content->id);
        }
        foreach ($courses as $course) {
            ClearCourse::dispatch($course->id);
        }
        ClearSection::dispatch();
    }

}
