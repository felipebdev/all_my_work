<?php

namespace App\Services;
use App\Subscriber;
use App\Content;
use App\Charts\Subscribers;
use App\Charts\PlansSubscriber;
use App\Plan;
use App\Course;
use App\Subscription;
use DB;


class DashboardService
{

    private $actives;
    private $canceleds;

    public function getSubscriptionsInfo($id,$dt_incial=null,$dt_final=null)
    {
        if($dt_final==null)
        {
            $dt_final = date('Y-m-d').' '.'23:59:59';
            $dt_incial= date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))).' '.'00:00:00';
        }

       $this->actives =  Subscriber::select('subscribers.id','plans.price','subscribers.plan_id','subscribers.created_at','subscriptions.created_at as subscriptionCreation','subscriptions.canceled_at as subscriptionCanceled')
       ->join('plans','subscribers.plan_id','=','plans.id')
       ->leftJoin('subscriptions','subscribers.id','=','subscriptions.subscriber_id')
       ->where([
            ['subscribers.platform_id', $id],
            //['subscriptions.canceled_at',null]
        ])->where(function($query) {
            $query->orWhere('subscribers.status', 'trial');

            $query->orWhere('subscribers.status', 'active');

            $query->orWhere('subscribers.status','pending_payment');
        })
        ->where('subscribers.created_at','<=',$dt_final)
        ->orderBy('subscribers.created_at')
        ->get();


        $this->canceleds = Subscriber::select('subscriptions.canceled_at','subscriptions.subscriber_id')
        ->leftJoin('subscriptions','subscribers.id','=','subscriptions.subscriber_id')
        ->where([
            ['subscribers.platform_id', $id]
        ])
        ->where(function($query){
            $query->orWhere('subscribers.status', 'canceled');
            $query->orWhere('subscribers.status','inactive');

        })
        ->whereBetween('subscriptions.canceled_at', [$dt_incial, $dt_final])
        ->orderBy('subscriptions.subscriber_id')
        ->get();

        //pegar a quantia de usuarios cancelados no periodo, pela subscriptions
        $cancel = 0;
        $tot  = sizeof($this->canceleds);
        for($i=0;$i<$tot;$i++)
        {
            $aux = $this->canceleds[$i]['subscriber_id'];
            if($i>0)
            {
                if($this->canceleds[$i-1]['subscriber_id']==$aux)
                {
                    continue;
                }else{
                    $cancel+=1;
                }
            }else{
                $cancel +=1;
            }
        }


        //calculo churn
        $ativ = sizeof($this->actives);
        if($cancel!=0 && $ativ!=0)
        {
            $number = ($cancel/$ativ)*100;
           $churn =  number_format($number, 2, '.', '');
        }else{
            $churn = 0;
        }

        //calculo da previsão de ganhos
         $valor = 0;
         foreach($this->actives as $active)
         {
            $valor+= $active['price'];
         }
        $revenues = number_format($valor, 2, ',', '.');
        $periodo =  date("d/m/Y", strtotime($dt_incial)).'-'.date("d/m/Y", strtotime($dt_final));

        $periodo_consult = $dt_incial.'_'.$dt_final;

        return [
            'periodo'=>$periodo,
            'periodo_consult'=>$periodo_consult,
            'actives'=>$ativ,
            'canceleds' => $cancel,
            'churn'=>$churn,
            'revenues'=>$revenues,
            //'ativos'=>json_decode($actives)
        ];


    }



    public function getChart2Data($id,$dt_incial=null,$dt_final=null)//grafico 1
    {
        if($dt_final==null)
        {
            $dt_final = date('Y-m-d').' '.'23:59:59';
            $dt_incial= date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))).' '.'00:00:00';
        }

        $actives = json_decode($this->actives);
        $canceleds = json_decode($this->canceleds);


        $diferenca = strtotime($dt_final)-strtotime($dt_incial);
        $dias = floor($diferenca/(60*60*24));

        if($dias<60 && $dias>29)
        {
            $dados = $this->get2Data($dt_incial,$dt_final,$actives,$canceleds,'week');


        }
        elseif($dias<30)
        {
            $dados=$this->get2Data($dt_incial,$dt_final,$actives,$canceleds,'day');

        }
        else{
            $dados = $this->get2Data($dt_incial,$dt_final,$actives,$canceleds,'month');

        }




        if($dados!=-1)
        {
            $chart = new Subscribers;
            $chart->labels($dados['label']);
            $chart->dataset('Ativos', 'line',$dados['actives'])->color('#007bff');
            $chart->dataset('Cancelados', 'line',$dados['canceleds'])->color('#28a745');
            $chart->dataset('Cadastrados','line',$dados['subscriber'])->color('#dc3545');
            $chart->dataset('Total','line',$dados['total'])->color('#ffc107');
        }else{
            $chart = new Subscribers;
        }

        return [
            'chart'=>$chart,
            'actives'=>$actives,
        ];


    }




    public function getChartSales($id)
    {
        $title = 'Planos mais vendidos';

        $plans = Plan::select('id','name')->where([
                ['platform_id',$id]
        ])->get();

        $actives = $this->actives;
        // echo "<pre>";
        // print_r($actives);
        // echo "</pre>";
        // die();

        $dados = $this->getPizzaData($actives,$plans);

        $seriesTitles = $dados['labels'];

      
        $colors = ["#f62d51","#009efb","#80a2ec"];
        
        $data = $dados['dataArray'];

        return [
            'title'          => $title,
            'seriesTitles'   => $seriesTitles,
            'data'           => $data,
            'colors'         => $colors,
            'there_is_plan' => $dados['there_is_plan'],
        ];
    }


    public function getCourseSales($id,$dt_incial,$dt_final)
    {
       
        if($dt_final==null)
        {
            $dt_final = date('Y-m-d').' '.'23:59:59';
            $dt_incial= date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))).' '.'00:00:00';
        }

        $courses = Course::select('courses.id','courses.name','course_subscribers.subscriber_id')
                        ->leftJoin('course_subscribers','courses.id','=','course_subscribers.course_id')
                        ->where('courses.platform_id','=',$id)
                        ->whereBetween('course_subscribers.created_at', [$dt_incial,$dt_final])
                        ->get();
        
        if(sizeof($courses) > 0)
        {
            $actives = $this->actives;
        
        
            $dados = $this->getCourseData($actives,$courses);

       

            $data = $dados['dataArray'];

        }else
        {
            $data = [
                ['label' => 'nenhuma venda','data' => [1],'backgroundColor' =>'rgba(201, 76, 76, 0.0)']
            ];
        }
        
        
        

        

        return [
            'datasets' => $data,
        ];
       
        
        
        
    }






    private function get2Data($dt_incial,$dt_final,$actives,$canceleds,$expressao)
    {
        $dt_seguinte = '1986-01-01';
        $Actives =[];
        $Canceleds = [];
        $Total = [];
        $Subscriber=[];
        $i=0;
        $label = [];
        $ColocarNoLabel = null;
        $contActv = 0;
        while (strtotime($dt_incial) <= strtotime($dt_final)){

            $dt_seguinte = date("Y-m-d", strtotime( "{$dt_incial} +1 {$expressao}" ));
            //criacao do labels do grafico
            $i++;

            if($expressao=='day')
            {
                $ColocarNoLabel = date('d-m',strtotime($dt_seguinte));

            }elseif($expressao=='month')
            {
                $ColocarNoLabel = 'mês'.$i;
            }else{
                $ColocarNoLabel =  'semana'.$i.'('.date('d-m',strtotime($dt_seguinte)).')';
            }
            $label[] = $ColocarNoLabel;
            //////////////


            $a=0;//actives
            $c=0;//canceleds
            $t=0;//total = c + a
            $s=0;//subscribers, qtd cadastrada no dia/semana/mes
            $seguinte = substr($dt_seguinte,0,10);
            $inicial = substr($dt_incial,0,10);

            $aux = '1986-01-01';
            foreach($actives as $active)
            {

                    if(strtotime($active->created_at)<=strtotime($dt_seguinte))
                    {
                        $a++;
                    }

                    //pegar quantia de cadastrados no dia,semana, mês
                    $dataBd = substr($active->created_at,0,10);
                    if($expressao=='day' && strtotime($dataBd)==strtotime($inicial))
                    {
                        $s++;
                    }elseif($expressao=='month' && strtotime(substr($dataBd,5,2)) == strtotime(substr($inicial,5,2)))
                    {
                        $s++;
                    }else {
                        if(date('W',strtotime($dataBd)) == date('W',strtotime($inicial)))
                        {
                                $s++;
                        }
                    }
            }

            foreach($canceleds as $canceled)
            {
                if(strtotime($canceled->canceled_at)<=strtotime($dt_seguinte))
                {
                    $c++;
                }
            }

            $t = $a + $c;
            $Actives[] = $a;
            $Canceleds[] = $c;
            $Total[] = $t;
            $Subscriber[]=$s;
            $dt_incial = $dt_seguinte;

        }

        $act = sizeof($Actives);
        $cancel = sizeof($Canceleds);
        if($cancel >=0 ||  $act >=0)
        {
            return [
                'actives'=>$Actives,
                'canceleds'=>$Canceleds,
                'total'=>$Total,
                'subscriber'=>$Subscriber,
                'label'=>$label
            ];
        }else
        {
            return -1;
        }
    }


    private function getPizzaData($ListaActives,$Listaplans)
    {
        $actives = json_decode($ListaActives);
        $plans = json_decode($Listaplans);
        $label =[];
        $dados = [];
        $porcent = [];

        $t=0;
        if(sizeof($plans) != 0){
            foreach($plans as $plan)
            {
                $c=0;
                foreach($actives as $active)
                {
                    if($active->plan_id == $plan->id)
                    {
                         $c++;
                         $t++;//pegar a quantia total de planos
                    }
    
                }
                $dados[$plan->name] = $c;

                

               
            }
            $there_is_plan = 1;
            
           
            
        }else{
            $dados = [
                "nenhum plano" => 1,
            ];
            $there_is_plan = 0;
            
        }
       
        // print_r($dados);
        // die();

        
        arsort($dados);

        $i = 0;
        $arr = array();

        foreach ($dados as $k => $v) {
            $i++;
            $arr[]= ['value' => $v, 'name' => $k ];
            $label[] = $k;
            
            if($i==3){
                break;
            }

        }
        

        return [
            'labels' => $label,
            'qtd'=> $dados,
            'porcent' => $porcent,
            'dataArray' => $arr,
            'there_is_plan' => $there_is_plan
        ];


    }


    private function getCourseData($ListaActives,$ListaCourses)
    {
        $actives = json_decode($ListaActives);
        $courses = json_decode($ListaCourses);
        $label =[];
        $dados = [];
        

       
        foreach($courses as $course)
        {
            $c=0;
            foreach($actives as $active)
            {
                if($active->id == $course->subscriber_id)
                {
                     $c++;
                   
                }

            }
            $dados[$course->name] = $c;
           
        }

        
        arsort($dados);
        

        $i = 0;
        $arr = array();
        $background_colors = ["#00a6d0","#dc3545","#563d7c","#28a745","#ffc107"];

        foreach ($dados as $k => $v) {
            
            $arr[] = ['label' => $k,'data' => [$v],'backgroundColor' => $background_colors[$i]];
            $label[] = $k;
            $i++;
            if($i==4){
                break;
            }

        }
        

        return [
            'dataArray' => $arr,
        ];

    }

    


    //Função para gerar cores automaticas no grafico
    private function gera_cor()
    {
        $hexadecimais = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F'];
        $cor = '#';

        // Pega um número aleatório no array acima
        for ($i = 0; $i < 6; $i++ ) {
        //E concatena à variável cor
            $num = round((mt_rand (0*10, 1*10) / 10) * 15);
             $cor .= $hexadecimais[$num];
        }
        return $cor;
    }



    //TOP 10 Conteudos
    public function getTopContents($id,$dt_incial=null,$dt_final=null)
    {

        if($dt_final==null)
        {
            $dt_final = date('Y-m-d').' '.'23:59:59';
            $dt_incial= date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))).' '.'00:00:00';
        }

        //novos assinantes
        $subscribers = DB::table('subscribers')
        ->select('subscribers.name','files.filename','subscribers.created_at')
        ->leftJoin('files','subscribers.thumb_id','=','files.id')
       
        ->where([
            ['subscribers.platform_id', $id],
           
        ])->where(function($query) {
            $query->orWhere('subscribers.status', 'trial');

            $query->orWhere('subscribers.status', 'active');

            $query->orWhere('subscribers.status','pending_payment');
        })
        ->where('subscribers.created_at','<=',$dt_final)
        ->orderBy('subscribers.created_at','desc')
        ->limit(10)
        ->get();



        //ultimos acessos
            $last_acess =  DB::table('subscribers')
            ->select('subscribers.name','files.filename','subscribers.last_acess')
            ->leftJoin('files','subscribers.thumb_id','=','files.id')
            ->where([
                ['subscribers.platform_id', $id],
            ])->where(function($query) {
                $query->orWhere('subscribers.status', 'trial');
    
                $query->orWhere('subscribers.status', 'active');
    
                $query->orWhere('subscribers.status','pending_payment');
            })
            ->where('subscribers.last_acess','<=',$dt_final)
            ->orderBy('subscribers.last_acess','desc')
            ->limit(10)
            ->get();
    

        //conteudos

            $contents = Content::select('contents.views','contents.title','files.filename')
            ->join('files','contents.thumb_small_id','=','files.id')
            ->join('sections','contents.section_id','=','sections.id')
            ->where([
                ['sections.platform_id',$id]
            ])
            ->where('contents.created_at','<=',$dt_final)
            ->orderBy('contents.views','desc')
            ->limit(10)
            ->get();

        return [
                'subscribers'=>$subscribers,
                'last_acess' => $last_acess,
                'contents' => $contents
            ];
    }

    public function getOnlineUsers($id)
    {
        $subscribers = Subscriber::where('platform_id', $id)
            ->leftJoin('files','subscribers.thumb_id','=','files.id')
            ->whereColumn('login', '>', 'last_acess')
            ->get();

        $qtdSubscribers = sizeof($subscribers);
        return [
            'subscribers'=>$subscribers,
            'qtd' => $qtdSubscribers,
            
        ];
        
    }








}
