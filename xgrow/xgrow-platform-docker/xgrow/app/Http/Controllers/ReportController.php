<?php

namespace App\Http\Controllers;

use App\AccessLog;
use App\Charts\AgeGenderChart;
use App\Charts\GenderChart;
use App\Charts\HitsDayWeekChart;
use App\Charts\HushHourChart;
use App\Charts\StateChart;
use App\Content;
use App\ContentLog;
use App\ContentSubscriber;
use App\Course;
use App\Section;
use App\Subscriber;
use Auth;
use DB;
use Illuminate\Http\Request;
use stdClass;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    private $accessLog;
    private $contentLog;
    private $content;
    private $subscriber;
    private $course;
    private $contentSubscriber;

    public function __construct(
        AccessLog $accessLog,
        ContentLog $contentLog,
        Content $content,
        Subscriber $subscriber,
        Course $course,
        ContentSubscriber $contentSubscriber
    )
    {
        $this->accessLog = $accessLog;
        $this->contentLog = $contentLog;
        $this->content = $content;
        $this->subscriber = $subscriber;
        $this->course = $course;
        $this->contentSubscriber = $contentSubscriber;
    }

    public function access(Request $request)
    {
        if ($request->has(['daterange'])) {
            list($initial, $final) = explode("-", $request->daterange);

            $initialDate = date("Y-m-d", strtotime(str_replace("/", "-", $initial))) . ' 00:00:00';
            $finalDate = date("Y-m-d", strtotime(str_replace("/", "-", $final))) . ' 23:59:59';

        } else {
            $initialDate = date('Y-m-d', strtotime(date("Y-m-d") . "-1 month")) . ' 00:00:00';
            $finalDate = date('Y-m-d', strtotime(date('Y-m-d'))) . ' 23:59:59';
        }

        $datePeriod = date("d/m/Y", strtotime($initialDate)) . ' - ' . date("d/m/Y", strtotime($finalDate));

        $search = ['period' => $datePeriod];

        $allDate = (int)$request->allDate;

        return view('reports.access')
            ->with('hitsHourDay', $this->hitsHourDay($initialDate, $finalDate, $allDate))
            ->with('hitsPerDay', $this->hitsPerDay($initialDate, $finalDate, $allDate))
            ->with('hitsDayWeek', $this->hitsDayWeek($initialDate, $finalDate, $allDate))
            ->with('averageAccessTime', $this->averageAccessTime())
            ->with('ageGenderChart', $this->ageGender($initialDate, $finalDate, $allDate))
            ->with('genderChart', $this->gender($initialDate, $finalDate, $allDate))
            ->with('stateChart', $this->state($initialDate, $finalDate, $allDate))
            ->with('search', $search)
            ->with('allDate', $request->allDate);
    }

    public function content()
    {
        return view('reports.content')
            ->with('mostAccessedContent', $this->mostAccessedContent())
            ->with('mostLikedContent', $this->mostLikedContent())
            ->with('mostCommentedContent', $this->mostCommentedContent())
            ->with('lessAccessedContent', $this->lessAccessedContent())
            ->with('accessedSections', $this->accessedSections());
    }

    public function contentSearch()
    {
        $contents = $this->content
            ->join('sections', 'sections.id', '=', 'contents.section_id')
            ->join('content_logs', 'content_logs.content_id', '=', 'contents.id')
            ->join('subscribers', 'content_logs.user_id', '=', 'subscribers.id')
            ->where('sections.platform_id', Auth::user()->platform_id)
            ->where('content_logs.user_type', 'subscribers');
        $total_label = getTotalLabel($contents, 'conteúdo');
        return view('reports.content-search', compact('total_label'));
    }

    public function contentSearchData()
    {
        try {
            $columns = [
                'content_logs.created_at AS access_date',
                'contents.title AS content_title', 'sections.name AS section_name',
                'subscribers.name AS subscriber_name',
                'content_logs.ip',
                DB::raw('TIMESTAMPDIFF(MINUTE, content_logs.created_at, content_logs.finished_at) AS minutes')
            ];

            $contents = Content::query()
                ->select($columns)
                ->join('sections', 'sections.id', '=', 'contents.section_id')
                ->join('content_logs', 'content_logs.content_id', '=', 'contents.id')
                ->join('subscribers', 'content_logs.user_id', '=', 'subscribers.id')
                ->where('sections.platform_id', Auth::user()->platform_id)
                ->where('content_logs.user_type', 'subscribers')
                ->orderBy('content_logs.created_at', 'DESC');

            return Datatables::of($contents)->toJson();
        } catch (\Exception $e) {
            return response(['Error' => $e->getMessage()], 400);
        }
    }

    // Esse método provavelmente não será utilizado, aguardar o fechamento dos gráficos para eliminar. Obg. [Fernanda]
    public function rushHour()
    {
        $rushHour = $this->accessLog->where('type', 'LOGIN')
            ->orderBy(DB::raw('HOUR(created_at)'))
            ->get();

        $sunday = $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = 0;

        $sundayOne = $sundayTwo = $sundayThree = $sundayFour = $sundayFive = $sundaySix = $sundaySeven = 0;
        $sundayEight = $sundayNine = $sundayTen = $sundayEleven = $sundayTwelve = $sundayThirteen = 0;
        $sundayFourteen = $sundayFifteen = $sundaySixteen = $sundaySeventeen = $sundayEighteen = 0;
        $sundayNineteen = $sundayTwenty = $sundayTwentyOne = $sundayTwentyTwo = $sundayTwentyThree = $sundayTwentyFour = 0;

        $mondayOne = $mondayTwo = $mondayThree = $mondayFour = $mondayFive = $mondaySix = $mondaySeven = 0;
        $mondayEight = $mondayNine = $mondayTen = $mondayEleven = $mondayTwelve = $mondayThirteen = 0;
        $mondayFourteen = $mondayFifteen = $mondaySixteen = $mondaySeventeen = $mondayEighteen = 0;
        $mondayNineteen = $mondayTwenty = $mondayTwentyOne = $mondayTwentyTwo = $mondayTwentyThree = $mondayTwentyFour = 0;

        $tuesdayOne = $tuesdayTwo = $tuesdayThree = $tuesdayFour = $tuesdayFive = $tuesdaySix = $tuesdaySeven = 0;
        $tuesdayEight = $tuesdayNine = $tuesdayTen = $tuesdayEleven = $tuesdayTwelve = $tuesdayThirteen = 0;
        $tuesdayFourteen = $tuesdayFifteen = $tuesdaySixteen = $tuesdaySeventeen = $tuesdayEighteen = 0;
        $tuesdayNineteen = $tuesdayTwenty = $tuesdayTwentyOne = $tuesdayTwentyTwo = $tuesdayTwentyThree = $tuesdayTwentyFour = 0;

        $wednesdayOne = $wednesdayTwo = $wednesdayThree = $wednesdayFour = $wednesdayFive = $wednesdaySix = $wednesdaySeven = 0;
        $wednesdayEight = $wednesdayNine = $wednesdayTen = $wednesdayEleven = $wednesdayTwelve = $wednesdayThirteen = 0;
        $wednesdayFourteen = $wednesdayFifteen = $wednesdaySixteen = $wednesdaySeventeen = $wednesdayEighteen = 0;
        $wednesdayNineteen = $wednesdayTwenty = $wednesdayTwentyOne = $wednesdayTwentyTwo = $wednesdayTwentyThree = $wednesdayTwentyFour = 0;

        $thursdayOne = $thursdayTwo = $thursdayThree = $thursdayFour = $thursdayFive = $thursdaySix = $thursdaySeven = 0;
        $thursdayEight = $thursdayNine = $thursdayTen = $thursdayEleven = $thursdayTwelve = $thursdayThirteen = 0;
        $thursdayFourteen = $thursdayFifteen = $thursdaySixteen = $thursdaySeventeen = $thursdayEighteen = 0;
        $thursdayNineteen = $thursdayTwenty = $thursdayTwentyOne = $thursdayTwentyTwo = $thursdayTwentyThree = $thursdayTwentyFour = 0;

        $fridayOne = $fridayTwo = $fridayThree = $fridayFour = $fridayFive = $fridaySix = $fridaySeven = 0;
        $fridayEight = $fridayNine = $fridayTen = $fridayEleven = $fridayTwelve = $fridayThirteen = 0;
        $fridayFourteen = $fridayFifteen = $fridaySixteen = $fridaySeventeen = $fridayEighteen = 0;
        $fridayNineteen = $fridayTwenty = $fridayTwentyOne = $fridayTwentyTwo = $fridayTwentyThree = $fridayTwentyFour = 0;

        $saturdayOne = $saturdayTwo = $saturdayThree = $saturdayFour = $saturdayFive = $saturdaySix = $saturdaySeven = 0;
        $saturdayEight = $saturdayNine = $saturdayTen = $saturdayEleven = $saturdayTwelve = $saturdayThirteen = 0;
        $saturdayFourteen = $saturdayFifteen = $saturdaySixteen = $saturdaySeventeen = $saturdayEighteen = 0;
        $saturdayNineteen = $saturdayTwenty = $saturdayTwentyOne = $saturdayTwentyTwo = $saturdayTwentyThree = $saturdayTwentyFour = 0;

        foreach ($rushHour as $item) {
            switch (date('l', strtotime($item->created_at))) {
                case 'Monday':
                    $monday++;
                    switch (date('H', strtotime($item->created_at))) {
                        case "1":
                            $mondayOne++;
                            break;
                        case "2":
                            $mondayTwo++;
                            break;
                        case "3":
                            $mondayThree++;
                            break;
                        case "4":
                            $mondayFour++;
                            break;
                        case "5":
                            $mondayFive++;
                            break;
                        case "6":
                            $mondaySix++;
                            break;
                        case "7":
                            $mondaySeven++;
                            break;
                        case "8":
                            $mondayEight++;
                            break;
                        case "9":
                            $mondayNine++;
                            break;
                        case "10":
                            $mondayTen++;
                            break;
                        case "11":
                            $mondayEleven++;
                            break;
                        case "12":
                            $mondayTwelve++;
                            break;
                        case "13":
                            $mondayThirteen++;
                            break;
                        case "14":
                            $mondayFourteen++;
                            break;
                        case "15":
                            $mondayFifteen++;
                            break;
                        case "16":
                            $mondaySixteen++;
                            break;
                        case "17":
                            $mondaySeventeen++;
                            break;
                        case "18":
                            $mondayEighteen++;
                            break;
                        case "19":
                            $mondayNineteen++;
                            break;
                        case "20":
                            $mondayTwenty++;
                            break;
                        case "21":
                            $mondayTwentyOne++;
                            break;
                        case "22":
                            $mondayTwentyTwo++;
                            break;
                        case "23":
                            $mondayTwentyThree++;
                            break;
                        case "24":
                            $mondayTwentyFour++;
                            break;
                    }
                    break;
                case 'Tuesday':
                    $tuesday++;
                    switch (date('H', strtotime($item->created_at))) {
                        case "1":
                            $tuesdayOne++;
                            break;
                        case "2":
                            $tuesdayTwo++;
                            break;
                        case "3":
                            $tuesdayThree++;
                            break;
                        case "4":
                            $tuesdayFour++;
                            break;
                        case "5":
                            $tuesdayFive++;
                            break;
                        case "6":
                            $tuesdaySix++;
                            break;
                        case "7":
                            $tuesdaySeven++;
                            break;
                        case "8":
                            $tuesdayEight++;
                            break;
                        case "9":
                            $tuesdayNine++;
                            break;
                        case "10":
                            $tuesdayTen++;
                            break;
                        case "11":
                            $tuesdayEleven++;
                            break;
                        case "12":
                            $tuesdayTwelve++;
                            break;
                        case "13":
                            $tuesdayThirteen++;
                            break;
                        case "14":
                            $tuesdayFourteen++;
                            break;
                        case "15":
                            $tuesdayFifteen++;
                            break;
                        case "16":
                            $tuesdaySixteen++;
                            break;
                        case "17":
                            $tuesdaySeventeen++;
                            break;
                        case "18":
                            $tuesdayEighteen++;
                            break;
                        case "19":
                            $tuesdayNineteen++;
                            break;
                        case "20":
                            $tuesdayTwenty++;
                            break;
                        case "21":
                            $tuesdayTwentyOne++;
                            break;
                        case "22":
                            $tuesdayTwentyTwo++;
                            break;
                        case "23":
                            $tuesdayTwentyThree++;
                            break;
                        case "24":
                            $tuesdayTwentyFour++;
                            break;
                    }
                    break;
                case 'Wednesday':
                    $wednesday++;
                    switch (date('H', strtotime($item->created_at))) {
                        case "1":
                            $wednesdayOne++;
                            break;
                        case "2":
                            $wednesdayTwo++;
                            break;
                        case "3":
                            $wednesdayThree++;
                            break;
                        case "4":
                            $wednesdayFour++;
                            break;
                        case "5":
                            $wednesdayFive++;
                            break;
                        case "6":
                            $wednesdaySix++;
                            break;
                        case "7":
                            $wednesdaySeven++;
                            break;
                        case "8":
                            $wednesdayEight++;
                            break;
                        case "9":
                            $wednesdayNine++;
                            break;
                        case "10":
                            $wednesdayTen++;
                            break;
                        case "11":
                            $wednesdayEleven++;
                            break;
                        case "12":
                            $wednesdayTwelve++;
                            break;
                        case "13":
                            $wednesdayThirteen++;
                            break;
                        case "14":
                            $wednesdayFourteen++;
                            break;
                        case "15":
                            $wednesdayFifteen++;
                            break;
                        case "16":
                            $wednesdaySixteen++;
                            break;
                        case "17":
                            $wednesdaySeventeen++;
                            break;
                        case "18":
                            $wednesdayEighteen++;
                            break;
                        case "19":
                            $wednesdayNineteen++;
                            break;
                        case "20":
                            $wednesdayTwenty++;
                            break;
                        case "21":
                            $wednesdayTwentyOne++;
                            break;
                        case "22":
                            $wednesdayTwentyTwo++;
                            break;
                        case "23":
                            $wednesdayTwentyThree++;
                            break;
                        case "24":
                            $wednesdayTwentyFour++;
                            break;
                    }
                    break;
                case 'Thursday':
                    $thursday++;
                    switch (date('H', strtotime($item->created_at))) {
                        case "1":
                            $thursdayOne++;
                            break;
                        case "2":
                            $thursdayTwo++;
                            break;
                        case "3":
                            $thursdayThree++;
                            break;
                        case "4":
                            $thursdayFour++;
                            break;
                        case "5":
                            $thursdayFive++;
                            break;
                        case "6":
                            $thursdaySix++;
                            break;
                        case "7":
                            $thursdaySeven++;
                            break;
                        case "8":
                            $thursdayEight++;
                            break;
                        case "9":
                            $thursdayNine++;
                            break;
                        case "10":
                            $thursdayTen++;
                            break;
                        case "11":
                            $thursdayEleven++;
                            break;
                        case "12":
                            $thursdayTwelve++;
                            break;
                        case "13":
                            $thursdayThirteen++;
                            break;
                        case "14":
                            $thursdayFourteen++;
                            break;
                        case "15":
                            $thursdayFifteen++;
                            break;
                        case "16":
                            $thursdaySixteen++;
                            break;
                        case "17":
                            $thursdaySeventeen++;
                            break;
                        case "18":
                            $thursdayEighteen++;
                            break;
                        case "19":
                            $thursdayNineteen++;
                            break;
                        case "20":
                            $thursdayTwenty++;
                            break;
                        case "21":
                            $thursdayTwentyOne++;
                            break;
                        case "22":
                            $thursdayTwentyTwo++;
                            break;
                        case "23":
                            $thursdayTwentyThree++;
                            break;
                        case "24":
                            $thursdayTwentyFour++;
                            break;
                    }
                    break;
                case 'Friday':
                    $friday++;
                    switch (date('H', strtotime($item->created_at))) {
                        case "1":
                            $fridayOne++;
                            break;
                        case "2":
                            $fridayTwo++;
                            break;
                        case "3":
                            $fridayThree++;
                            break;
                        case "4":
                            $fridayFour++;
                            break;
                        case "5":
                            $fridayFive++;
                            break;
                        case "6":
                            $fridaySix++;
                            break;
                        case "7":
                            $fridaySeven++;
                            break;
                        case "8":
                            $fridayEight++;
                            break;
                        case "9":
                            $fridayNine++;
                            break;
                        case "10":
                            $fridayTen++;
                            break;
                        case "11":
                            $fridayEleven++;
                            break;
                        case "12":
                            $fridayTwelve++;
                            break;
                        case "13":
                            $fridayThirteen++;
                            break;
                        case "14":
                            $fridayFourteen++;
                            break;
                        case "15":
                            $fridayFifteen++;
                            break;
                        case "16":
                            $fridaySixteen++;
                            break;
                        case "17":
                            $fridaySeventeen++;
                            break;
                        case "18":
                            $fridayEighteen++;
                            break;
                        case "19":
                            $fridayNineteen++;
                            break;
                        case "20":
                            $fridayTwenty++;
                            break;
                        case "21":
                            $fridayTwentyOne++;
                            break;
                        case "22":
                            $fridayTwentyTwo++;
                            break;
                        case "23":
                            $fridayTwentyThree++;
                            break;
                        case "24":
                            $fridayTwentyFour++;
                            break;
                    }
                    break;
                case 'Saturday':
                    $saturday++;
                    switch (date('H', strtotime($item->created_at))) {
                        case "1":
                            $saturdayOne++;
                            break;
                        case "2":
                            $saturdayTwo++;
                            break;
                        case "3":
                            $saturdayThree++;
                            break;
                        case "4":
                            $saturdayFour++;
                            break;
                        case "5":
                            $saturdayFive++;
                            break;
                        case "6":
                            $saturdaySix++;
                            break;
                        case "7":
                            $saturdaySeven++;
                            break;
                        case "8":
                            $saturdayEight++;
                            break;
                        case "9":
                            $saturdayNine++;
                            break;
                        case "10":
                            $saturdayTen++;
                            break;
                        case "11":
                            $saturdayEleven++;
                            break;
                        case "12":
                            $saturdayTwelve++;
                            break;
                        case "13":
                            $saturdayThirteen++;
                            break;
                        case "14":
                            $saturdayFourteen++;
                            break;
                        case "15":
                            $saturdayFifteen++;
                            break;
                        case "16":
                            $saturdaySixteen++;
                            break;
                        case "17":
                            $saturdaySeventeen++;
                            break;
                        case "18":
                            $saturdayEighteen++;
                            break;
                        case "19":
                            $saturdayNineteen++;
                            break;
                        case "20":
                            $saturdayTwenty++;
                            break;
                        case "21":
                            $saturdayTwentyOne++;
                            break;
                        case "22":
                            $saturdayTwentyTwo++;
                            break;
                        case "23":
                            $saturdayTwentyThree++;
                            break;
                        case "24":
                            $saturdayTwentyFour++;
                            break;
                    }
                    break;
                case 'Sunday':
                    $sunday++;
                    switch (date('H', strtotime($item->created_at))) {
                        case "1":
                            $sundayOne++;
                            break;
                        case "2":
                            $sundayTwo++;
                            break;
                        case "3":
                            $sundayThree++;
                            break;
                        case "4":
                            $sundayFour++;
                            break;
                        case "5":
                            $sundayFive++;
                            break;
                        case "6":
                            $sundaySix++;
                            break;
                        case "7":
                            $sundaySeven++;
                            break;
                        case "8":
                            $sundayEight++;
                            break;
                        case "9":
                            $sundayNine++;
                            break;
                        case "10":
                            $sundayTen++;
                            break;
                        case "11":
                            $sundayEleven++;
                            break;
                        case "12":
                            $sundayTwelve++;
                            break;
                        case "13":
                            $sundayThirteen++;
                            break;
                        case "14":
                            $sundayFourteen++;
                            break;
                        case "15":
                            $sundayFifteen++;
                            break;
                        case "16":
                            $sundaySixteen++;
                            break;
                        case "17":
                            $sundaySeventeen++;
                            break;
                        case "18":
                            $sundayEighteen++;
                            break;
                        case "19":
                            $sundayNineteen++;
                            break;
                        case "20":
                            $sundayTwenty++;
                            break;
                        case "21":
                            $sundayTwentyOne++;
                            break;
                        case "22":
                            $sundayTwentyTwo++;
                            break;
                        case "23":
                            $sundayTwentyThree++;
                            break;
                        case "24":
                            $sundayTwentyFour++;
                            break;
                    }
                    break;
            }

        }

//        $dataGraph = [
//            'mondayOne' => $mondayOne,
//            'mondayTwo' => $mondayTwo,
//            'mondayThree' => $mondayThree,
//            'mondayFour' => $mondayFour,
//            'mondayFive' => $mondayFive,
//            'mondaySix' => $mondaySix,
//            'mondaySeven' => $mondaySeven,
//            'mondayEight' => $mondayEight,
//            'mondayNine' => $mondayNine,
//            'mondayTen' => $mondayTen,
//            'mondayEleven' => $mondayEleven,
//            'mondayTwelve' => $mondayTwelve,
//            'mondayThirteen' => $mondayThirteen,
//            'mondayFourteen' => $mondayFourteen,
//            'mondayFifteen' => $mondayFifteen,
//            'mondaySixteen' => $mondaySixteen,
//            'mondaySeventeen' => $mondaySeventeen,
//            'mondayEighteen' => $mondayEighteen,
//            'mondayNineteen' => $mondayNineteen,
//            'mondayTwenty' => $mondayTwenty,
//            'mondayTwentyOne' => $mondayTwentyOne,
//            'mondayTwentyTwo' => $mondayTwentyTwo,
//            'mondayTwentyThree' => $mondayTwentyThree,
//            'mondayTwentyFour' => $mondayTwentyFour,
//            'tuesdayOne' => $tuesdayOne,
//            'tuesdayTwo' => $tuesdayTwo,
//            'tuesdayThree' => $tuesdayThree,
//            'tuesdayFour' => $tuesdayFour,
//            'tuesdayFive' => $tuesdayFive,
//            'tuesdaySix' => $tuesdaySix,
//            'tuesdaySeven' => $tuesdaySeven,
//            'tuesdayEight' => $tuesdayEight,
//            'tuesdayNine' => $tuesdayNine,
//            'tuesdayTen' => $tuesdayTen,
//            'tuesdayEleven' => $tuesdayEleven,
//            'tuesdayTwelve' => $tuesdayTwelve,
//            'tuesdayThirteen' => $tuesdayThirteen,
//            'tuesdayFourteen' => $tuesdayFourteen,
//            'tuesdayFifteen' => $tuesdayFifteen,
//            'tuesdaySixteen' => $tuesdaySixteen,
//            'tuesdaySeventeen' => $tuesdaySeventeen,
//            'tuesdayEighteen' => $tuesdayEighteen,
//            'tuesdayNineteen' => $tuesdayNineteen,
//            'tuesdayTwenty' => $tuesdayTwenty,
//            'tuesdayTwentyOne' => $tuesdayTwentyOne,
//            'tuesdayTwentyTwo' => $tuesdayTwentyTwo,
//            'tuesdayTwentyThree' => $tuesdayTwentyThree,
//            'tuesdayTwentyFour' => $tuesdayTwentyFour,
//            'wednesdayOne' => $wednesdayOne,
//            'wednesdayTwo' => $wednesdayTwo,
//            'wednesdayThree' => $wednesdayThree,
//            'wednesdayFour' => $wednesdayFour,
//            'wednesdayFive' => $wednesdayFive,
//            'wednesdaySix' => $wednesdaySix,
//            'wednesdaySeven' => $wednesdaySeven,
//            'wednesdayEight' => $wednesdayEight,
//            'wednesdayNine' => $wednesdayNine,
//            'wednesdayTen' => $wednesdayTen,
//            'wednesdayEleven' => $wednesdayEleven,
//            'wednesdayTwelve' => $wednesdayTwelve,
//            'wednesdayThirteen' => $wednesdayThirteen,
//            'wednesdayFourteen' => $wednesdayFourteen,
//            'wednesdayFifteen' => $wednesdayFifteen,
//            'wednesdaySixteen' => $wednesdaySixteen,
//            'wednesdaySeventeen' => $wednesdaySeventeen,
//            'wednesdayEighteen' => $wednesdayEighteen,
//            'wednesdayNineteen' => $wednesdayNineteen,
//            'wednesdayTwenty' => $wednesdayTwenty,
//            'wednesdayTwentyOne' => $wednesdayTwentyOne,
//            'wednesdayTwentyTwo' => $wednesdayTwentyTwo,
//            'wednesdayTwentyThree' => $wednesdayTwentyThree,
//            'wednesdayTwentyFour' => $wednesdayTwentyFour,
//            'thursdayOne' => $thursdayOne,
//            'thursdayTwo' => $thursdayTwo,
//            'thursdayThree' => $thursdayThree,
//            'thursdayFour' => $thursdayFour,
//            'thursdayFive' => $thursdayFive,
//            'thursdaySix' => $thursdaySix,
//            'thursdaySeven' => $thursdaySeven,
//            'thursdayEight' => $thursdayEight,
//            'thursdayNine' => $thursdayNine,
//            'thursdayTen' => $thursdayTen,
//            'thursdayEleven' => $thursdayEleven,
//            'thursdayTwelve' => $thursdayTwelve,
//            'thursdayThirteen' => $thursdayThirteen,
//            'thursdayFourteen' => $thursdayFourteen,
//            'thursdayFifteen' => $thursdayFifteen,
//            'thursdaySixteen' => $thursdaySixteen,
//            'thursdaySeventeen' => $thursdaySeventeen,
//            'thursdayEighteen' => $thursdayEighteen,
//            'thursdayNineteen' => $thursdayNineteen,
//            'thursdayTwenty' => $thursdayTwenty,
//            'thursdayTwentyOne' => $thursdayTwentyOne,
//            'thursdayTwentyTwo' => $thursdayTwentyTwo,
//            'thursdayTwentyThree' => $thursdayTwentyThree,
//            'thursdayTwentyFour' => $thursdayTwentyFour,
//            'fridayOne' => $fridayOne,
//            'fridayTwo' => $fridayTwo,
//            'fridayThree' => $fridayThree,
//            'fridayFour' => $fridayFour,
//            'fridayFive' => $fridayFive,
//            'fridaySix' => $fridaySix,
//            'fridaySeven' => $fridaySeven,
//            'fridayEight' => $fridayEight,
//            'fridayNine' => $fridayNine,
//            'fridayTen' => $fridayTen,
//            'fridayEleven' => $fridayEleven,
//            'fridayTwelve' => $fridayTwelve,
//            'fridayThirteen' => $fridayThirteen,
//            'fridayFourteen' => $fridayFourteen,
//            'fridayFifteen' => $fridayFifteen,
//            'fridaySixteen' => $fridaySixteen,
//            'fridaySeventeen' => $fridaySeventeen,
//            'fridayEighteen' => $fridayEighteen,
//            'fridayNineteen' => $fridayNineteen,
//            'fridayTwenty' => $fridayTwenty,
//            'fridayTwentyOne' => $fridayTwentyOne,
//            'fridayTwentyTwo' => $fridayTwentyTwo,
//            'fridayTwentyThree' => $fridayTwentyThree,
//            'fridayTwentyFour' => $fridayTwentyFour,
//            'saturdayOne' => $saturdayOne,
//            'saturdayTwo' => $saturdayTwo,
//            'saturdayThree' => $saturdayThree,
//            'saturdayFour' => $saturdayFour,
//            'saturdayFive' => $saturdayFive,
//            'saturdaySix' => $saturdaySix,
//            'saturdaySeven' => $saturdaySeven,
//            'saturdayEight' => $saturdayEight,
//            'saturdayNine' => $saturdayNine,
//            'saturdayTen' => $saturdayTen,
//            'saturdayEleven' => $saturdayEleven,
//            'saturdayTwelve' => $saturdayTwelve,
//            'saturdayThirteen' => $saturdayThirteen,
//            'saturdayFourteen' => $saturdayFourteen,
//            'saturdayFifteen' => $saturdayFifteen,
//            'saturdaySixteen' => $saturdaySixteen,
//            'saturdaySeventeen' => $saturdaySeventeen,
//            'saturdayEighteen' => $saturdayEighteen,
//            'saturdayNineteen' => $saturdayNineteen,
//            'saturdayTwenty' => $saturdayTwenty,
//            'saturdayTwentyOne' => $saturdayTwentyOne,
//            'saturdayTwentyTwo' => $saturdayTwentyTwo,
//            'saturdayTwentyThree' => $saturdayTwentyThree,
//            'saturdayTwentyFour' => $saturdayTwentyFour,
//            'sundayOne' => $sundayOne,
//            'sundayTwo' => $sundayTwo,
//            'sundayThree' => $sundayThree,
//            'sundayFour' => $sundayFour,
//            'sundayFive' => $sundayFive,
//            'sundaySix' => $sundaySix,
//            'sundaySeven' => $sundaySeven,
//            'sundayEight' => $sundayEight,
//            'sundayNine' => $sundayNine,
//            'sundayTen' => $sundayTen,
//            'sundayEleven' => $sundayEleven,
//            'sundayTwelve' => $sundayTwelve,
//            'sundayThirteen' => $sundayThirteen,
//            'sundayFourteen' => $sundayFourteen,
//            'sundayFifteen' => $sundayFifteen,
//            'sundaySixteen' => $sundaySixteen,
//            'sundaySeventeen' => $sundaySeventeen,
//            'sundayEighteen' => $sundayEighteen,
//            'sundayNineteen' => $sundayNineteen,
//            'sundayTwenty' => $sundayTwenty,
//            'sundayTwentyOne' => $sundayTwentyOne,
//            'sundayTwentyTwo' => $sundayTwentyTwo,
//            'sundayTwentyThree' => $sundayTwentyThree,
//            'sundayTwentyFour' => $sundayTwentyFour,
//        ];

        $chart = new HushHourChart;
        $chart->labels(['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom']);

        $oneArray = [$mondayOne, $tuesdayOne, $wednesdayOne, $thursdayOne, $fridayOne, $saturdayOne, $sundayOne];
        $twoArray = [$mondayTwo, $tuesdayTwo, $wednesdayTwo, $thursdayTwo, $fridayTwo, $saturdayTwo, $sundayTwo];
        $threeArray = [$mondayThree, $tuesdayThree, $wednesdayThree, $thursdayThree, $fridayThree, $saturdayThree, $sundayThree];
        $fourArray = [$mondayFour, $tuesdayFour, $wednesdayFour, $thursdayFour, $fridayFour, $saturdayFour, $sundayFour];
        $fiveArray = [$mondayFive, $tuesdayFive, $wednesdayFive, $thursdayFive, $fridayFive, $saturdayFive, $sundayFive];
        $sixArray = [$mondaySix, $tuesdaySix, $wednesdaySix, $thursdaySix, $fridaySix, $saturdaySix, $sundaySix];
        $sevenArray = [$mondaySeven, $tuesdaySeven, $wednesdaySeven, $thursdaySeven, $fridaySeven, $saturdaySeven, $sundaySeven];
        $eightArray = [$mondayEight, $tuesdayEight, $wednesdayEight, $thursdayEight, $fridayEight, $saturdayEight, $sundayEight];
        $nineArray = [$mondayNine, $tuesdayNine, $wednesdayNine, $thursdayNine, $fridayNine, $saturdayNine, $sundayNine];
        $tenArray = [$mondayTen, $tuesdayTen, $wednesdayTen, $thursdayTen, $fridayTen, $saturdayTen, $sundayTen];
        $elevenArray = [$mondayEleven, $tuesdayEleven, $wednesdayEleven, $thursdayEleven, $fridayEleven, $saturdayEleven, $sundayEleven];
        $twelveArray = [$mondayTwelve, $tuesdayTwelve, $wednesdayTwelve, $thursdayTwelve, $fridayTwelve, $saturdayTwelve, $sundayTwelve];
        $thirteenArray = [$mondayThirteen, $tuesdayThirteen, $wednesdayThirteen, $thursdayThirteen, $fridayThirteen, $saturdayThirteen, $sundayThirteen];
        $fourteenArray = [$mondayFourteen, $tuesdayFourteen, $wednesdayFourteen, $thursdayFourteen, $fridayFourteen, $saturdayFourteen, $sundayFourteen];
        $fifteenArray = [$mondayFifteen, $tuesdayFifteen, $wednesdayFifteen, $thursdayFifteen, $fridayFifteen, $saturdayFifteen, $sundayFifteen];
        $sixteenArray = [$mondaySixteen, $tuesdaySixteen, $wednesdaySixteen, $thursdaySixteen, $fridaySixteen, $saturdaySixteen, $sundaySixteen];
        $seventeenArray = [$mondaySeventeen, $tuesdaySeventeen, $wednesdaySeventeen, $thursdaySeventeen, $fridaySeventeen, $saturdaySeventeen, $sundaySeventeen];
        $eighteenArray = [$mondayEighteen, $tuesdayEighteen, $wednesdayEighteen, $thursdayEighteen, $fridayEighteen, $saturdayEighteen, $sundayEighteen];
        $nineteenArray = [$mondayNineteen, $tuesdayNineteen, $wednesdayNineteen, $thursdayNineteen, $fridayNineteen, $saturdayNineteen, $sundayNineteen];
        $twentyArray = [$mondayTwenty, $tuesdayTwenty, $wednesdayTwenty, $thursdayTwenty, $fridayTwenty, $saturdayTwenty, $sundayTwenty];
        $twentyOneArray = [$mondayTwentyOne, $tuesdayTwentyOne, $wednesdayTwentyOne, $thursdayTwentyOne, $fridayTwentyOne, $saturdayTwentyOne, $sundayTwentyOne];
        $twentyTwoArray = [$mondayTwentyTwo, $tuesdayTwentyTwo, $wednesdayTwentyTwo, $thursdayTwentyTwo, $fridayTwentyTwo, $saturdayTwentyTwo, $sundayTwentyTwo];
        $twentyThreeArray = [$mondayTwentyThree, $tuesdayTwentyThree, $wednesdayTwentyThree, $thursdayTwentyThree, $fridayTwentyThree, $saturdayTwentyThree, $sundayTwentyThree];
        $twentyFourArray = [$mondayTwentyFour, $tuesdayTwentyFour, $wednesdayTwentyFour, $thursdayTwentyFour, $fridayTwentyFour, $saturdayTwentyFour, $sundayTwentyFour];

        $chart->dataset('1 da manhã', 'bar', $oneArray)->color('#007bff');
        $chart->dataset('2 da manhã', 'bar', $twoArray)->color('#28a745');
        $chart->dataset('3 da manhã', 'bar', $threeArray)->color('#dc3545');
        $chart->dataset('4 da manhã', 'bar', $fourArray)->color('#FF00BF');
        $chart->dataset('5 da manhã', 'bar', $fiveArray)->color('#FF4B4B');
        $chart->dataset('6 da manhã', 'bar', $sixArray)->color('#BA49FF');
        $chart->dataset('7 da manhã', 'bar', $sevenArray)->color('#ffc407');
        $chart->dataset('8 da manhã', 'bar', $eightArray)->color('#5DFF3E');
        $chart->dataset('9 da manhã', 'bar', $nineArray)->color('#4BFFFF');
        $chart->dataset('10 da manhã', 'bar', $tenArray)->color('#2A31FF');
        $chart->dataset('11 da manhã', 'bar', $elevenArray)->color('#C7FF8E');
        $chart->dataset('12 da tarde', 'bar', $twelveArray)->color('#FF768C');
        $chart->dataset('13 da tarde', 'bar', $thirteenArray)->color('#8F49FF');
        $chart->dataset('14 da tarde', 'bar', $fourteenArray)->color('#D2FFFF');
        $chart->dataset('15 da tarde', 'bar', $fifteenArray)->color('#FF8F35');
        $chart->dataset('16 da tarde', 'bar', $sixteenArray)->color('#FF709C');
        $chart->dataset('17 da tarde', 'bar', $seventeenArray)->color('#5C66FF');
        $chart->dataset('18 da noite', 'bar', $eighteenArray)->color('#000');
        $chart->dataset('19 da noite', 'bar', $nineteenArray)->color('#FFFD7C');
        $chart->dataset('20 da noite', 'bar', $twentyArray)->color('#5DFF3E');
        $chart->dataset('21 da noite', 'bar', $twentyOneArray)->color('#60E9FF');
        $chart->dataset('22 da noite', 'bar', $twentyTwoArray)->color('#FF4B4B');
        $chart->dataset('23 da noite', 'bar', $twentyThreeArray)->color('#FFBF00');
        $chart->dataset('24 da noite', 'bar', $twentyFourArray)->color('#EC53FF');

        return $chart;
    }

    public function hitsDayWeek($initialDate, $finalDate, $allDate)
    {
        $hitsDayWeek = $this->accessLog->where('type', 'LOGIN')
            ->where(function ($query) use ($initialDate, $finalDate, $allDate) {
                if ($allDate === 0) {
                    $query->whereBetween('created_at', [$initialDate, $finalDate]);
                }
            })
            ->orderBy(DB::raw('HOUR(created_at)'))
            ->get();

        $sunday = $monday = $tuesday = $wednesday = $thursday = $friday = $saturday = 0;

        foreach ($hitsDayWeek as $item) {
            switch (date('l', strtotime($item->created_at))) {
                case 'Monday':
                    $monday++;
                    break;
                case 'Tuesday':
                    $tuesday++;
                    break;
                case 'Wednesday':
                    $wednesday++;
                    break;
                case 'Thursday':
                    $thursday++;
                    break;
                case 'Friday':
                    $friday++;
                    break;
                case 'Saturday':
                    $saturday++;
                    break;
                case 'Sunday':
                    $sunday++;
                    break;
            }
        }

        $data = [$sunday, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday];

        $hitsDayWeekChart = new HitsDayWeekChart();

        $hitsDayWeekChart->labels(['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom']);
        $hitsDayWeekChart->dataset('', 'line', $data)->color("rgb(255, 70, 131)");

        $hitsDayWeekChart->options([
            'tooltip' => [
                'trigger' => 'axis',
            ],
            'title' => [
                'left' => 'left',
                'text' => 'Horário de pico por dia da semana',
            ],
            'xAxis' => [
                'type' => 'category',
                'boundaryGap' => false,
                'data' => 'date'
            ],
            'yAxis' => [
                'type' => 'value',
                'boundaryGap' => [0, '100%']
            ],
            'series' => [
                [
                    'name' => '',
                    'type' => 'line',
                    'symbol' => 'none',
                    'sampling' => 'average',
                    'itemStyle' => [
                        'color' => 'rgb(255, 70, 131)'
                    ],
                    'areaStyle' => [],
                    'data' => $data,
                ]
            ]
        ]);

        return $hitsDayWeekChart;
    }

    public function hitsHourDay($initialDate, $finalDate, $allDate)
    {
        $hitsHourDay = $this->accessLog->where('type', 'LOGIN')
            ->where(function ($query) use ($initialDate, $finalDate, $allDate) {
                if ($allDate === 0) {
                    $query->whereBetween('created_at', [$initialDate, $finalDate]);
                }
            })
            ->orderBy(DB::raw('HOUR(created_at)'))
            ->get();

        $one = $two = $three = $four = $five = $six = $seven = 0;
        $eight = $nine = $ten = $eleven = $twelve = $thirteen = 0;
        $fourteen = $fifteen = $sixteen = $seventeen = $eighteen = 0;
        $nineteen = $twenty = $twentyOne = $twentyTwo = $twentyThree = $twentyFour = 0;

        foreach ($hitsHourDay as $item) {
            switch (date('H', strtotime($item->created_at))) {
                case '1':
                    $one++;
                    break;
                case '2':
                    $two++;
                    break;
                case '3':
                    $three++;
                    break;
                case '4':
                    $four++;
                    break;
                case '5':
                    $five++;
                    break;
                case '6':
                    $six++;
                    break;
                case '7':
                    $seven++;
                    break;
                case '8':
                    $eight++;
                    break;
                case '9':
                    $nine++;
                    break;
                case '10':
                    $ten++;
                    break;
                case '11':
                    $eleven++;
                    break;
                case '12':
                    $twelve++;
                    break;
                case '13':
                    $thirteen++;
                    break;
                case '14':
                    $fourteen++;
                    break;
                case '15':
                    $fifteen++;
                    break;
                case '16':
                    $sixteen++;
                    break;
                case '17':
                    $seventeen++;
                    break;
                case '18':
                    $eighteen++;
                    break;
                case '19':
                    $nineteen++;
                    break;
                case '20':
                    $twenty++;
                    break;
                case '21':
                    $twentyOne++;
                    break;
                case '22':
                    $twentyTwo++;
                    break;
                case '23':
                    $twentyThree++;
                    break;
                case '24':
                    $twentyFour++;
                    break;
            }
        }

        $data = [
            $twentyFour, $one, $two, $three, $four, $five, $six, $seven, $eight, $nine, $ten,
            $eleven, $twelve, $thirteen, $fourteen, $fifteen, $sixteen, $seventeen, $eighteen, $nineteen, $twenty,
            $twentyOne, $twentyTwo, $twentyThree
        ];

        $hitsDayWeekChart = new HitsDayWeekChart();

        $hitsDayWeekChart->labels([
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
            '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
            '21', '22', '23'
        ]);
        $hitsDayWeekChart->dataset('', 'line', $data)->color("rgb(255, 70, 131)");

        $hitsDayWeekChart->options([
            'tooltip' => [
                'trigger' => 'axis',
            ],
            'title' => [
                'left' => 'left',
                'text' => 'Horário de pico por hora',
            ],
            'xAxis' => [
                'type' => 'category',
                'boundaryGap' => false,
                'data' => 'date'
            ],
            'yAxis' => [
                'type' => 'value',
                'boundaryGap' => [0, '100%']
            ],
            'series' => [
                [
                    'name' => '',
                    'type' => 'line',
                    'symbol' => 'none',
                    'sampling' => 'average',
                    'itemStyle' => [
                        'color' => 'rgb(255, 70, 131)'
                    ],
                    'areaStyle' => [],
                    'data' => $data,
                ]
            ]
        ]);

        return $hitsDayWeekChart;
    }

    public function hitsPerDay($initialDate, $finalDate, $allDate)
    {
        $hitsPerDay = $this->accessLog->select(DB::raw('DATE(created_at) AS day, COUNT(id) AS amount'))
            ->where('type', 'LOGIN')
            ->where(function ($query) use ($initialDate, $finalDate, $allDate) {
                if ($allDate === 0) {
                    $query->whereBetween('created_at', [$initialDate, $finalDate]);
                }
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        $labels = $data = [];

        $hitsPerDayChart = new HushHourChart;

        foreach ($hitsPerDay as $item) {
            $weekDay = dayOfWeek($item->day);
            $labels[] = ucfirst(substr($weekDay, 0, 3)) . ' ' . date('d/m', strtotime($item->day));
            $data[] = $item->amount;
        }

        $hitsPerDayChart->labels($labels);
        $hitsPerDayChart->dataset('Horário de pico por dia', 'bar', $data)->color('#007bff');

        return $hitsPerDayChart;
    }

    public function averageAccessTime()
    {

        $amountPass = $this->contentLog->where('user_type', 'subscribers')
            ->whereNotNull('finished_at')
            ->where(DB::raw('MONTH(created_at)'), DB::raw('MONTH(date_add(NOW(), INTERVAL -1 MONTH))'))
            ->where('content_logs.platform_id', Auth::user()->platform_id)
            ->count();

        $averageAccessPass = $this->contentLog->select('content_logs.finished_at', 'content_logs.created_at')
            ->where('content_logs.user_type', 'subscribers')
            ->whereNotNull('finished_at')
            ->where(DB::raw('MONTH(created_at)'), DB::raw('MONTH(date_add(NOW(), INTERVAL -1 MONTH))'))
            ->where('content_logs.platform_id', Auth::user()->platform_id)
            ->get();

        $totalTime = 0;

        if ($averageAccessPass !== null) {
            foreach ($averageAccessPass as $item) {
                $diff = strtotime($item->finished_at) - strtotime($item->created_at);
                $totalTime += $diff;
            }
        }

        $avgMinutesPass = ($totalTime === 0) ? 0 : round(($totalTime / 60) / $amountPass);

        $amount = $this->contentLog->where('user_type', 'subscribers')
            ->whereNotNull('finished_at')
            ->where('content_logs.platform_id', Auth::user()->platform_id)
            ->count();

        $averageAccess = $this->contentLog->select('content_logs.finished_at', 'content_logs.created_at')
            ->where('content_logs.user_type', 'subscribers')
            ->whereNotNull('finished_at')
            ->where('content_logs.platform_id', Auth::user()->platform_id) // 'Auth::user()->platform_id'
            ->get();

        $totalTime = 0;

        if ($averageAccess !== null) {
            foreach ($averageAccess as $item) {
                $diff = strtotime($item->finished_at) - strtotime($item->created_at);
                $totalTime += $diff;
            }
        }

        $avgMinutes = ($totalTime === 0) ? 0 : round(($totalTime / 60) / $amount);

        $percent = ($avgMinutesPass > 0) ? round((($avgMinutes * 100) / $avgMinutesPass) - 100) : 0;

        return [
            'data' => $averageAccess,
            'avgPass' => $avgMinutesPass,
            'avg' => $avgMinutes,
            'percent' => $percent,
            'plat' => Auth::user()->platform_id,
            'progressBar' => ($percent < 0) ? ($percent * -1) . '%' : $percent . '%'
        ];
    }

    public function ageRange($initialDate, $finalDate, $allDate, $type = false)
    {
        $std = new stdClass;

        $groupThirteen = $groupEighteen = $groupTwentyFive = 0;
        $groupThirtyFive = $groupFortyFive = $groupFiftyFive = $groupSixtyFive = 0;

        $users = $this->getUsersAccess($initialDate, $finalDate, $allDate);

        $subscribers = $this->subscriber->select('subscribers.gender', 'subscribers.birthday')
            ->whereNotNull('subscribers.birthday')
            ->where('subscribers.platform_id', Auth::user()->platform_id)
            ->where(function ($query) use ($type) {
                if ($type) {
                    $query->where('subscribers.gender', $type);
                } else {
                    $query->whereNull('subscribers.gender')->orWhere('subscribers.gender', '=', '');
                }
            })
            ->whereIn('id', $users)
            ->get();

        $std->amount = $subscribers->count();

        foreach ($subscribers as $item) {
            $age = ageCalc($item->birthday);

            if ($age >= 13 && $age <= 17) {
                $groupThirteen++;
            }

            if ($age >= 18 && $age <= 24) {
                $groupEighteen++;
            }

            if ($age >= 25 && $age <= 34) {
                $groupTwentyFive++;
            }

            if ($age >= 35 && $age <= 44) {
                $groupThirtyFive++;
            }

            if ($age >= 45 && $age <= 54) {
                $groupFortyFive++;
            }

            if ($age >= 55 && $age <= 64) {
                $groupFiftyFive++;
            }

            if ($age >= 65) {
                $groupSixtyFive++;
            }
        }

        $std->ages = [$groupSixtyFive, $groupFiftyFive, $groupFortyFive, $groupThirtyFive, $groupTwentyFive, $groupEighteen, $groupThirteen];

        return $std;
    }

    public function ageGender($initialDate, $finalDate, $allDate)
    {
        $objGender = $this->ageRange($initialDate, $finalDate, $allDate, 'female');
        $dataFemale = $objGender->ages;
        $female = $objGender->amount;

        $objGender = $this->ageRange($initialDate, $finalDate, $allDate, 'male');
        $dataMale = $objGender->ages;
        $male = $objGender->amount;

        $objGender = $this->ageRange($initialDate, $finalDate, $allDate);
        $dataUndefined = $objGender->ages;
        $male = $objGender->amount;

        $ageGenderChart = new AgeGenderChart();

        $ageGenderChart->labels(['13-17', '18-24', '25-34', '35-44', '45-54', '55-64', '+65']);
        $ageGenderChart->dataset('Feminino', 'bar', $dataFemale);
        $ageGenderChart->dataset('Masculino', 'bar', $dataMale);
        $ageGenderChart->dataset('Indefinido', 'bar', $dataUndefined);

        $ageGenderChart->options([
            'tooltip' => [
                'trigger' =>
                    'axis',
                'axisPointer' => [
                    'type' =>
                        'shadow'
                ]
            ],
            'legend' => [
                'data' =>
                    ['Feminino', 'Masculino', 'Indefinido']
            ],
            'grid' => [
                'left' =>
                    '3%',
                'right' => '4%',
                'bottom' => '3%',
                'containLabel' => true
            ],
            'xAxis' => [
                'type' =>
                    'value'
            ],
            'yAxis' => [
                'type' =>
                    'category',
                'data' => ['+65', '55-64', '45-54', '35-44', '25-34', '18-24', '13-17']
            ],
            'series' => [
                [
                    'name' => 'Feminino',
                    'type' => 'bar',
                    'stack' => 'Total',
                    'label' => [
                        'show' => true,
                        'position' => 'insideRight'
                    ],
                    'data' => $dataFemale
                ],
                [
                    'name' => 'Masculino',
                    'type' => 'bar',
                    'stack' => 'Total',
                    'label' => [
                        'show' => true,
                        'position' => 'insideRight'
                    ],
                    'data' => $dataMale
                ],
                [
                    'name' => 'Indefinido',
                    'type' => 'bar',
                    'stack' => 'Total',
                    'label' => [
                        'show' => true,
                        'position' => 'insideRight'
                    ],
                    'data' => $dataUndefined
                ],

            ]

        ]);

        return $ageGenderChart;
    }

    public function gender($initialDate, $finalDate, $allDate)
    {
        $users = $this->getUsersAccess($initialDate, $finalDate, $allDate);

        $subscribersMale = $this->subscriber->where('subscribers.platform_id', Auth::user()->platform_id)
            ->where('subscribers.gender', 'male')
            ->whereIn('id', $users)
            ->count();

        $subscribersFemale = $this->subscriber->where('subscribers.platform_id', Auth::user()->platform_id)
            ->where('subscribers.gender', 'female')
            ->whereIn('id', $users)
            ->count();

        $genderChart = new GenderChart();

        $genderChart->dataset('Feminino', 'bar', [$subscribersFemale])->color('#FF0000');
        $genderChart->dataset('Masculino', 'bar', [$subscribersMale])->color('#49A5FF');

        return $genderChart;
    }

    public function state($initialDate, $finalDate, $allDate)
    {
        $users = $this->getUsersAccess($initialDate, $finalDate, $allDate);

        $subscribers = $this->subscriber->select(DB::raw(' COUNT(subscribers.address_city) AS total'))
            ->where('subscribers.platform_id', Auth::user()->platform_id)
            ->whereIn('id', $users)
            ->groupBy('subscribers.address_city')
            ->addSelect(['subscribers.address_city', 'subscribers.address_state'])
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $stateChart = new StateChart();

        $labels = $data = [];

        if ($subscribers !== null) {
            foreach ($subscribers as $item) {
                $labels[] = $item->address_city . ' (' . $item->address_state . ')';
                $data[] = $item->total;
            }
        }

        $stateChart->labels($labels);
        $stateChart->dataset('', 'bar', array_reverse($data))->color('#49A5FF');
        $stateChart->options([
            'title' => [
                'left' => 'center',
                'text' => 'Principais localizações',
            ],
            'tooltip' => [
                'trigger' =>
                    'axis',
                'axisPointer' => [
                    'type' =>
                        'shadow'
                ]
            ],
            'legend' => [
                'data' =>
                    ['Feminino', 'Masculino', 'Indefinido']
            ],
            'grid' => [
                'left' =>
                    '3%',
                'right' => '4%',
                'bottom' => '3%',
                'containLabel' => true
            ],
            'xAxis' => [
                'type' =>
                    'value'
            ],
            'yAxis' => [
                'type' =>
                    'category',
                'data' => array_reverse($labels)
            ],
        ]);

        return $stateChart;
    }

    public function getUsersAccess($initialDate, $finalDate, $allDate)
    {
        return $this->accessLog::select('user_id')
            ->where('platform_id', Auth::user()->platform_id)
            ->where(function ($query) use ($initialDate, $finalDate, $allDate) {
                if ($allDate === 0) {
                    $query->whereBetween('created_at', [$initialDate, $finalDate]);
                }
            })
            ->distinct()
            ->pluck('user_id');
    }

    public function mostAccessedContent()
    {
        return $this->contentLog->select('content_logs.content_id', DB::raw('COUNT(content_logs.content_id) AS amount'), 'files.filename', 'contents.title')
            ->join('contents', 'contents.id', '=', 'content_logs.content_id')
            ->join('files', 'files.id', '=', 'contents.thumb_small_id')
            ->join('sections', 'sections.id', '=', 'contents.section_id')
            ->where('content_logs.platform_id', Auth::user()->platform_id)
            ->where('content_logs.content_id', '>', 0)
            ->where('sections.active', 1)
            ->groupBy('content_logs.content_id')
            ->orderBy('amount', 'DESC')
            ->limit(20)
            ->get();
    }

    public function mostLikedContent()
    {
        return $this->content->select('contents.title', 'contents.likes', 'contents.id', 'files.filename')
            ->leftJoin('files', 'files.id', '=', 'contents.thumb_small_id')
            ->whereHas('section', function ($query) {
                $query->where('platform_id', Auth::user()->platform_id)->where('active', 1);
            })
            ->where('contents.published', 1)
            ->where('contents.likes', '>', 0)
            ->orderBy('contents.likes', 'DESC')
            ->limit(20)
            ->get();
    }

    public function mostCommentedContent()
    {
        return Section::select('comments.commentable_id', DB::raw('COUNT(comments.commentable_id) AS total'), 'contents.title', 'files.filename', 'contents.title')
            ->join('contents', 'contents.section_id', '=', 'sections.id')
            ->join('comments', 'contents.id', '=', 'comments.commentable_id')
            ->leftJoin('files', 'files.id', '=', 'contents.thumb_small_id')
            ->where('sections.active', 1)
            ->where('contents.published', 1)
            ->where('comments.commentable_type', 'App\\Content')
            ->where('sections.platform_id', Auth::user()->platform_id)
            ->groupBy('comments.commentable_id')
            ->orderBy('total', 'DESC')
            ->limit(20)
            ->get();
    }

    public function lessAccessedContent()
    {
        return $this->contentLog->select('content_logs.content_id', DB::raw('COUNT(content_logs.content_id) AS total'), 'files.filename', 'contents.title')
            ->join('contents', 'contents.id', '=', 'content_logs.content_id')
            ->join('files', 'files.id', '=', 'contents.thumb_small_id')
            ->join('sections', 'sections.id', '=', 'contents.section_id')
            ->where('content_logs.platform_id', Auth::user()->platform_id)
            ->where('content_logs.content_id', '>', 0)
            ->where('sections.active', 1)
            ->groupBy('content_logs.content_id')
            ->orderBy('total', 'ASC')
            ->limit(20)
            ->get();
    }

    public function accessedSections()
    {
        $accessedSections = Section::select('sections.name', DB::raw('COUNT(content_logs.section_key) AS total'))
            ->join('content_logs', 'content_logs.section_key', '=', 'sections.section_key')
            ->where('content_logs.platform_id', Auth::user()->platform_id)
            ->where('content_logs.user_type', 'subscribers')
            ->whereNotNull('content_logs.section_key')
            ->groupBy('sections.section_key')
            ->orderBy('total', 'DESC')
            ->limit(20)
            ->get();

        //$accessedSectionsChart = new SectionsChart();

        //$data = $labels = [];
        $data = [];

        if ($accessedSections !== null) {
            foreach ($accessedSections as $item) {

                $data[] = ['label' => $item->name, 'data' => [$item->total], 'backgroundColor' => $this->gera_cor()];

                // $labels[] = $item->name;
                // $data[] = $item->total;
            }
        }

        // $accessedSectionsChart->labels($labels);
        // $accessedSectionsChart->dataset('', 'bar', $data)->color('#ff0000');
        // $accessedSectionsChart->options([
        //     'title' => [
        //         'left' => 'center',
        //         'text' => 'Seções mais acessadas',
        //     ]
        // ]);

        return $data;
    }


    //Função para gerar cores automaticas no grafico
    private function gera_cor()
    {
        $hexadecimais = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
        $cor = '#';

        // Pega um número aleatório no array acima
        for ($i = 0; $i < 6; $i++) {
            //E concatena à variável cor
            $num = round((mt_rand(0 * 10, 1 * 10) / 10) * 15);
            $cor .= $hexadecimais[$num];
        }
        return $cor;
    }


    public function topUsersPlatform()
    {
        $topUsers = $this->accessLog->select('access_logs.user_id', DB::raw('COUNT(user_id) AS amount'))
            ->with('platform_user:id,name')
            ->where('access_logs.type', 'LOGIN')
            ->where('access_logs.user_type', 'platforms_users')
            ->groupBy('user_id')
            ->orderBy('amount', 'DESC')
            ->limit(20)
            ->get();

        return response()->json(['data' => $topUsers]);
    }

    public function topUsersSite()
    {
        $topUsers = $this->accessLog->select('access_logs.user_id', DB::raw('COUNT(user_id) AS amount'))
            ->with('subscriber:id,name')
            ->where('access_logs.type', 'LOGIN')
            ->where('access_logs.user_type', 'subscribers')
            ->groupBy('user_id')
            ->orderBy('amount', 'DESC')
            ->limit(20)
            ->get();

        return response()->json(['data' => $topUsers]);
    }

    public function usersWithoutAccessSite()
    {
        $usersWithoutAccess = $this->accessLog->select('access_logs.user_id')
            ->with('subscriber:id,name')
            ->where('access_logs.type', 'LOGIN')
            ->where('access_logs.created_at', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL -7 DAY)'))
            ->where('access_logs.user_type', 'subscribers')
            ->get();

        return response()->json(['data' => $usersWithoutAccess]);
    }

    public function courseSearch(Request $request)
    {
        $dataIni = date('d/m/Y', strtotime('-100 month'));
        $dataFin = date('d/m/Y');
        $courseName = $request->course_name ?? null;

        $courses = $this->prepareReturnCourseReport($dataIni, $dataFin, $courseName);

        return view('reports.course-search', compact('courses', 'dataIni', 'dataFin'));
    }

    public function getLastAccessCourse($courseId, $subscriberId)
    {
        $row = $this->course->select('content_subscriber.updated_at AS last_access')
            ->join('course_subscribers', 'course_subscribers.course_id', '=', 'courses.id')
            ->join('subscribers', 'subscribers.id', '=', 'course_subscribers.subscriber_id')
            ->join('content_subscriber', 'content_subscriber.subscriber_id', '=', 'subscribers.id')
            ->where('courses.platform_id', Auth::user()->platform_id)
            ->where('courses.id', $courseId)
            ->where('subscribers.id', $subscriberId)
            ->orderBy('content_subscriber.updated_at', 'DESC')
            ->first();

        return $row->last_access ?? null;
    }

    private function convertDate($d)
    {
        $date = str_replace("/", "-", $d);
        return date("Y-m-d", strtotime($date));
    }

    public function courseSearchData(Request $request)
    {
        $courses = $this->prepareReturnCourseReport();

        return Datatables::of($courses)->make(true);
    }

    public function getTotalClasses($courseId)
    {
        return $this->content->where('course_id', $courseId)
            ->where('module_id', '>', 0)
            ->where('published', 1)
            ->where('is_course', 1)
            ->count();
    }

    private function prepareReturnCourseReport()
    {
        $columns = [
            'courses.id AS course_id',
            'courses.name AS course_name',
            'subscribers.id AS subscriber_id',
            'subscribers.name AS subscriber_name',
            'course_subscribers.created_at AS first_access',
            'course_subscribers.total_classes_attended'
        ];

        $query = $this->course
            ->leftJoin('course_subscribers', 'courses.id', '=', 'course_subscribers.course_id')
            ->join('subscribers', 'course_subscribers.subscriber_id', '=', 'subscribers.id')
            ->groupBy('subscribers.id')
            ->where('courses.platform_id', Auth::user()->platform_id)
            ->select($columns);

        $courses = $query->get();

        return $courses->each(function ($course) {
            $totalClasses = $this->getTotalClasses($course->course_id);
            $course->progress = ($totalClasses > 0) ? ($course->total_classes_attended * 100 / $totalClasses) : 0;

            $course->last_access = null;
            if ($course->first_access !== null && $course->subscriber_id !== null) {
                $course->last_access = $this->getLastAccessCourse($course->course_id, $course->subscriber_id);
            }
        });
    }

}
