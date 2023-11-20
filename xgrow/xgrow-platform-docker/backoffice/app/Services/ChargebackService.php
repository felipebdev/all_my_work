<?php
namespace App\Services;

use App\Subscriber;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;
use Codedge\Fpdf\Fpdf\Fpdf;
use Zip;


class ChargebackService
{
    
    /**
     * Get zip link
     * @param Request $request
     * @return string
     */
    public function getLink($file)
    {

        $arr_pdf_path = [];
        $readerXlsx = new ReaderXlsx();

        if ($loadXlsx = $readerXlsx->load($file)) {

            $data = $loadXlsx->getSheet(0)->toArray();
            $zipName = '';

            foreach ($data as $number => $line) {

                if ($number == 0) continue;

                $pdf_filename = "ID TRANSAÇÃO - {$line[0]}.pdf"; 
                $email = $line[14];
                $metadata = json_decode($line[15], true);
                $order_code = $metadata['order_code'];

                $datum = "$email - $order_code";

                $resultTable = Subscriber::select(
                    'subscribers.name',
                    'subscribers.email',
                    'subscribers.login',
                    'subscribers.created_at',
                    'subscriptions.status',
                    'payments.payment_date',
                    'plans.name'
                )->join('subscriptions', 'subscriptions.subscriber_id', '=', 'subscribers.id')
                ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                ->join('payments', 'payments.order_number', '=', 'subscriptions.order_number')
                ->where('payments.order_code', $order_code)
                ->where('subscribers.email', $email)
                ->first();

                //Data envio email
                $dadosEmail = $this->initPostmark("https://api.postmarkapp.com/messages/outbound?recipient=$email&count=25&offset=0");
                $dateDelivered = '-';
                if (isset($dadosEmail['Messages'][0])) {
                    $messageId = $dadosEmail['Messages'][0]['MessageID'];
                    $returnEmailData = $this->initPostmark("https://api.postmarkapp.com/messages/outbound/$messageId/details");
                    if ($returnEmailData['MessageEvents']) {
                        foreach ($returnEmailData['MessageEvents'] as $messageEvents) {

                            if ($messageEvents['Type'] == 'Delivered') {
                                if ($messageEvents['ReceivedAt']) {
                                    $dateRAW = explode('.', $messageEvents['ReceivedAt']);
                                    list($date1, $date2) = explode('T', $dateRAW[0]);
                                    $date1 = implode(preg_match("~\/~", $date1) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $date1) == 0 ? "-" : "/", $date1)));
                                    $dateDelivered = "$date1 $date2";
                                }
                            }
                        }
                    }
                }

                if ($resultTable) {
                    $resultTable = $resultTable->toArray();

                    //Tratamento das datas
                    $dateDelivered = $dateDelivered;
                    $dateLastAccess = implode(preg_match("~\/~", $resultTable['login']) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $resultTable['login']) == 0 ? "-" : "/", $resultTable['login'])));
                    $datePayment = implode(preg_match("~\/~", $resultTable['payment_date']) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $resultTable['payment_date']) == 0 ? "-" : "/", $resultTable['payment_date'])));
                    

                    //pdf
                    $fpdf = new Fpdf();
                    $fpdf->AddPage();
                    $fpdf->SetMargins(20,30,5);
                    $fpdf->Ln(6);
                    $fpdf->SetFont('Arial','B',12);
                    $fpdf->SetTextColor(000,000,000);
                    $fpdf->SetFillColor(0,0,255);
                    $fpdf->Cell(0,8,'ID TRANSACAO - '.$line[0],'LRT',1,'L');
                    $fpdf->SetFillColor(255,255,255);
                    $fpdf->Cell(0,8,'ENVIO DO EMAIL - DATA ENVIO '.$dateDelivered,'LRBT',1,'L');
                    $fpdf->Cell(0,8,'ULTIMO ACESSO NA PLATAFORMA '.$dateLastAccess,'LRBT',1,'L');
                    $fpdf->Cell(0,8,'GARANTIA 7 DIAS DATA DA COMPRA '.$datePayment.' DATA DE HOJE '.date('d/m/Y'),'LRBT',1,'L');
                    $fpdf->Ln(12);
                    $fpdf->Cell(0,8,'ID TRANSACAO '.$line[0],0,1,'L');
                    $fpdf->Ln(6);
                    $fpdf->SetFont('Arial','',12);
                    $fpdf->Cell(0,8,"- Envio de email ".$dateDelivered,0,1,'L');
                    $fpdf->Cell(0,8,"- Ultimo acesso na plafatorma ".$dateLastAccess,0,1,'L');
                    $fpdf->Cell(0,8,"- Prazo de garantia dos 7 dias dessa pessoa ".$datePayment,0,1,'L');

                    $pdf_path = public_path().'/uploads/'.$pdf_filename;
                    $fpdf->Output($pdf_path,'F');
                    $arr_pdf_path[] = $pdf_path;

                }
            }

            //Zipando arquivo
            if(count($arr_pdf_path) > 0) {

                $zipName = public_path().'/uploads/chargeback.zip';
                $zipNamePublic = url('/').'/uploads/chargeback.zip';
                $zip = Zip::create($zipName);
                $zip->add($arr_pdf_path);
                $zip->close();

                return $zipNamePublic;

            }
        }
        return false;


    }
    
    private function initPostmark(string $url, string $httpVerb = 'GET')
    {
        $postmarkToken = env('POSTMARK_TOKEN');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpVerb,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Requested-With: XMLHttpRequest',
                "X-Postmark-Server-Token: {$postmarkToken}"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }    

}
