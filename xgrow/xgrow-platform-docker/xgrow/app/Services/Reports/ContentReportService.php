<?php

namespace App\Services\Reports;

use App\Comment;
use App\Content;
use App\Section;
use App\Services\LAService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContentReportService
{

    /**
     * Get LA Credentials
     * @return LAService
     */
    public function getLAConnection(): LAService
    {
        return new LAService(Auth::user()->platform_id, Auth::user()->id);
    }

    /**
     * Search comments and count then
     * limit to 20
     *
     * @param mixed $initialDate
     * @param mixed $finalDate
     * @param string $actionType
     * @return mixed
     */
    public function countComments($initialDate, $finalDate, $actionType)
    {
        try {
            $response = $this->getLAConnection()->get("/logs?starttime={$initialDate}&endtime={$finalDate}&actionType={$actionType}");

            $commentIds = collect($response->data)->map(function ($comment) {
                return $comment->actionId;
            });

            $comments = Comment::select(DB::raw('files.filename, contents.title, COUNT(comments.contents_id) AS count_comments'))
                ->join('contents', 'comments.contents_id', '=', 'contents.id')
                ->leftJoin('sections', 'contents.section_id', '=', 'sections.id')
                ->leftJoin('files', 'contents.thumb_small_id', '=', 'files.id')
                ->whereIn('comments.id', $commentIds->flatten())
                ->groupBy('comments.contents_id')
                ->get();

            return $comments ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Search comments and return
     * in desc order the top 20 most accessed
     *
     * @param mixed $initialDate
     * @param mixed $finalDate
     * @param string $order
     * @return mixed
     */
    public function mostAccessedContent($initialDate, $finalDate, $actionType)
    {
        try {
            $response = $this->getLAConnection()->get("/logs?starttime={$initialDate}&endtime={$finalDate}&actionType={$actionType}");

            $contentIds = collect($response->data)->map(function ($comment) {
                return $comment->actionId;
            });

            $contents = Content::select(DB::raw('contents.id as cid, files.filename, contents.title'))
                ->leftJoin('files', 'contents.thumb_small_id', '=', 'files.id')
                ->whereIn('contents.id', $contentIds->flatten())
                ->get();

            $contents = collect($contents)->map(function ($content) use ($contentIds) {
                return [
                    "filename" => $content->filename,
                    "title" => $content->title,
                    "amount" => isset($contentIds->countBy()[$content->cid]) ? $contentIds->countBy()[$content->cid] : $content->amount
                ];
            });

            return $contents ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Search top 20 most liked contents
     * in desc order on top
     *
     * @param mixed $initialDate
     * @param mixed $finalDate
     * @param string $order
     * @return mixed
     */
    public function mostLikedContent($initialDate, $finalDate, $actionType)
    {
        try {
            $response = $this->getLAConnection()->get("/logs?starttime={$initialDate}&endtime={$finalDate}&actionType={$actionType}");

            $contentIds = collect($response->data)->map(function ($comment) {
                return $comment->actionId;
            });

            $contents = Content::select(DB::raw('contents.id as cid, files.filename, contents.title'))
                ->leftJoin('files', 'contents.thumb_small_id', '=', 'files.id')
                ->whereIn('contents.id', $contentIds->flatten())
                ->get();

            $likes = collect($contents)->map(function ($content) use ($contentIds) {
                return [
                    "filename" => $content->filename,
                    "title" => $content->title,
                    "likes" => isset($contentIds->countBy()[$content->cid]) ? $contentIds->countBy()[$content->cid] : $content->amount
                ];
            });

            return $likes ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Count most accessed sections on course
     *
     * @param mixed $initialDate
     * @param mixed $finalDate
     * @return mixed
     */
    public function mostAccessedSection($initialDate, $finalDate, $actionType)
    {
        try {
            $response = $this->getLAConnection()->get("/logs?starttime={$initialDate}&endtime={$finalDate}&actionType={$actionType}");

            $sectionIds = collect($response->data)->map(function ($section) {
                return $section->actionId;
            });

            $sections = Section::select(DB::raw('sections.id as sid, files.filename, sections.name'))
                ->leftJoin('files', 'sections.image_id', '=', 'files.id')
                ->whereIn('sections.id', $sectionIds->flatten())
                ->get();

            $sections = collect($sections)->map(function ($section) use ($sectionIds) {
                return [
                    "filename" => $section->filename,
                    "name" => $section->name,
                    "amount" => isset($sectionIds->countBy()[$section->sid]) ? $sectionIds->countBy()[$section->sid] : $section->amount
                ];
            });

            return $sections ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Search most popular content by author
     *
     * @param mixed $initialDate
     * @param mixed $finalDate
     * @return mixed
     */
    public function contentMostPopularByAuthor($initialDate, $finalDate, $actionType)
    {
        try {
            $response = $this->getLAConnection()->get("/logs?starttime={$initialDate}&endtime={$finalDate}&actionType={$actionType}");

            $contentIds = collect($response->data)->map(function ($content) {
                if (trim($content->actionId) !== "") return $content->actionId;
            });

            $contents = Content::select(DB::raw('contents.id as cid, authors.name_author'))
                ->join('authors', 'contents.author_id', '=', 'authors.id')
                ->whereIn('contents.id', $contentIds->flatten())
                ->get();

            $authors = collect($contents)->map(function ($content) use ($contentIds) {
                return [
                    "author" => $content->name_author,
                    "amount" => isset($contentIds->countBy()[$content->cid]) ? $contentIds->countBy()[$content->cid] : $content->amount
                ];
            })->groupBy('author')->map(function ($content) {
                return $content->max('amount');
            });

            return $authors ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}

// public function getTotalViewsContentByAuthor($initialDate, $finalDate, $allDate, $platform_id)
//     {
//         $sql = "SELECT c.name_author, count(c.name_author) AS total FROM content_views a
//                 INNER JOIN contents b ON a.content_id = b.id
//                 INNER JOIN authors c ON b.author_id = c.id
//                 WHERE c.platform_id = '$platform_id'";
//         if ($allDate == 0) {
//             $sql .= "AND a.created_at BETWEEN '$initialDate' AND '$finalDate'";
//         }
//         $sql .= "GROUP BY c.name_author";
//         return $sql;
//     }
