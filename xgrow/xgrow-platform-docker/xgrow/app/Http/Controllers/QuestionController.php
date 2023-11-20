<?php

namespace App\Http\Controllers;

use App\File;
use App\Question;
use App\QuestionOption;
use App\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

class QuestionController extends Controller
{
    private $quiz;
    private $question;
    private $options;

    public function __construct(Quiz $quiz, Question $question, QuestionOption $option)
    {
        $this->quiz = $quiz;
        $this->question = $question;
        $this->option = $option;
    }

    /**
     * Display a listing of the resource.
     * @param int $quiz_id
     * @return \Illuminate\Http\Response
     */
    public function index($quiz_id){
        $quiz = $this->quiz->find($quiz_id);
        $questions = $quiz->questions()->orderBy('order', 'ASC')->get();

        return view('quiz.create', compact('quiz', 'questions'));
    }

    /**
     * Show the form for creating a new resource.
     * @param int $quiz_id
     * @return \Illuminate\Http\Response
     */
    public function create($quiz_id)
    {

        $quiz = $this->quiz->find($quiz_id); 

        $question = new stdClass;
        $question->id = 0;
        $question->type = 1;

        $question_total_default = Quiz::QUESTION_TOTAL_DEFAULT;

        $count = $quiz->questions()->count();
        $question->order = $count + 1;

        $orders = setVetorOrder($question->order);

        $options = [];

        $types = $this->question->listTypes();

        return view('quiz.create', compact('quiz', 'question', 'orders', 'options', 'types', 'question_total_default'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $quiz_id)
    {
        
        $request->request->add(['quiz_id' => $quiz_id]);

        $this->save($request);

        return redirect()->route('question.index', $quiz_id);
    }

    /**
     * Display the specified resource.
     *
     * @param int $quiz_id
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show($quiz_id, Question $question)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param int $quiz_id
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit($quiz_id, Question $question)
    {
        $quiz = $this->quiz->find($quiz_id); 

        $orders = setVetorOrder($quiz->questions()->count());

        $types = $this->question->listTypes();

        $options = $question->options()->get();


        return view('quiz.create', compact('quiz', 'question', 'orders', 'types', 'options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $quiz_id, Question $question)
    {

        $this->save($request, $question->id);

        return redirect()->route('question.index', $quiz_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @param  int $quiz_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $quiz_id, Question $question)
    {
        $this->removeItemOrder($question->order, $quiz_id);
        $question->delete();
        return redirect()->route('question.index', $quiz_id);
    }


    public function save($request, $id = 0)
    {

        $thumb = File::setUploadedFile($request, 'thumb');

        $this->updateOrder($id, $request, $this->question, $request->quiz_id);   

        $question = $this->question->updateOrCreate(
            ['id' => $id],
            $request->all()
        );

        if($request->type != Question::TYPE_TEXT){
            foreach ($request->options as $key => $id) {
               $excluded = $request->excluded[$key];
               $description = $request->options_description[$key];
                $correct = (in_array($key, $request->correct)) ? 1 : 0;
                $data = [
                    'description' => $description,
                    'correct' => $correct
                ];

               if($id == 0 and $excluded == 0) //novo item
                    $question->options()->create($data);
               else if($id > 0){  //somente já cadastrados
                    $option = $this->option->find($id);
                    if($excluded == 0) //atualiza dados
                        $option->update($data);
                    else
                        $option->delete();
               }
            };
        }
        else{
            $question->options()->delete();
        }
        

        File::saveUploadedFile($question, $thumb, 'thumb_id');

        return $question;
    }

    private function removeItemOrder($order, $quiz_id){
         $this->question->where('quiz_id',  $quiz_id)
                     ->where('order', '>=', $order)
                     ->update(['order' => DB::raw('`order`-1')]);
    }

    private function updateOrder($id, $request, $quiz_id){
        if($id > 0){
            $model = $this->question->find($id);
            if($model->order < $request->order){ // adiar a ordem
                $this->question->where('quiz_id',  $quiz_id)
                     ->where('order', '>=', $model->order)
                     ->where('order', '<=', $request->order)
                     ->update(['order' => DB::raw('`order`-1')]);
            }
            else if($model->order > $request->order){ //antecipar ordem
                $this->question->where('quiz_id',  $quiz_id)
                     ->where('order', '>=', $request->order)
                     ->where('order', '<', $model->order)
                     ->update(['order' => DB::raw('`order`+1')]);
            }
        }
        if($id == 0){ // ao criar
            $this->question->where('quiz_id',  $quiz_id)
                     ->where('order', '>=', $request->order)
                     ->update(['order' => DB::raw('`order`+1')]);
        }
    }   

}
