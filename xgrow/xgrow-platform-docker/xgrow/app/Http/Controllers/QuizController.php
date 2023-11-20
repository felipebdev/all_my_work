<?php

namespace App\Http\Controllers;

use App\File;
use App\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;

class QuizController extends Controller
{
    private $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $quizzes = $this->quiz->where('platform_id', Auth::user()->platform_id)->get();

        return view('quiz.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quiz = new stdClass;
        $quiz->id = 0;

        return view('quiz.create', compact('quiz'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $quiz = $this->save($request);

        return redirect()->route('question.index', $quiz->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz)
    {
        return view('quiz.create', compact('quiz'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $quiz = $this->save($request, $id);

        return redirect()->route('quiz.edit', $quiz->id);
    }


    /**
     * Save the data.
     *
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function save($request, $id = 0)
    {

        $thumb = File::setUploadedFile($request, 'thumb');

        $rules['name'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $request->request->add(['platform_id' => Auth::user()->platform_id]);

        $quiz = $this->quiz->updateOrCreate(
            ['id' => $id],
            $request->all()
        );

        File::saveUploadedFile($quiz, $thumb, 'thumb_id');

        return $quiz;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('quiz.index');
    }
}
