<?php

namespace App\Services\PandaVideo;

use Psr\Http\Message\ResponseInterface;

class PandaVideoRequestService extends BaseRequestService
{
    /**
     * API INTERFACE FOR PANDA VIDEO
     * @url https://pandavideo.readme.io/reference/get-videos
     */

    /**
     * METHODS FOR VIDEOS
     */

    /** Get all videos */
    public function getVideos()
    {
        return $this->get('videos');
    }

    /** Get video by ID
     * @param $id
     * @return ResponseInterface|string
     */
    public function getVideo($id)
    {
        return $this->get(sprintf('%s/%s', 'videos', $id));
    }

    public function uploadVideo($data)
    {
        return $this->customPost('files', $data);
    }

    /**
     * METHODS FOR FOLDER
     */

    /** Get all folders */
    public function getFolders()
    {
        return $this->get('folders');
    }

    /** Get one folder and list all videos
     * @param $id
     * @return ResponseInterface|string
     */
    public function getFolder($id)
    {
        return $this->get(sprintf('%s/%s', 'folders', $id));
    }

    /** Create folder
     * @param $data
     * @return ResponseInterface|string
     */
    public function createFolder($data)
    {
        return $this->post('folders', $data);
    }

    /** Update folder info
     * @param $id
     * @param $data
     * @return ResponseInterface|string
     */
    public function updateFolderInfo($id, $data)
    {
        return $this->put(sprintf('%s/%s', 'folders', $id), $data);
    }

    /** Delete folder with id
     * @param $id
     * @return ResponseInterface|string
     */
    public function deleteFolder($id)
    {
        return $this->delete(sprintf('%s/%s', 'folders', $id));
    }
}
