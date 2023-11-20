<?php

namespace App\Jobs\Imports\Subscribers;

use App\Downloads;
use App\Subscriber;
use App\Subscription;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SubscriberImportQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $platform_id;
    protected $filename;
    protected $platform_user_id;
    protected $plan_id;
    protected $status;
    protected $sendMail;
    protected $emailService;
    protected $delimiter;

    protected $s3Path;
    protected $s3Filename;

    protected $saveDirectory = 'xgrow_reports';
    protected $diskStorage;
    public $tries = 5;
    protected $filePath;

    public function __construct($user, $filePath, $plan_id, $status, $sendMail, $emailService, $delimiter, $s3Path, $s3Filename)
    {
        $this->platform_id = $user->platform_id;
        $this->platform_user_id = $user->id;
        $this->diskStorage = env('STORAGE_DIR', 'images');

        $this->s3Path = $s3Path;
        $this->s3Filename = $s3Filename;

        $this->filePath = $filePath;
        $this->plan_id = $plan_id;
        $this->status = $status;
        $this->sendMail = $sendMail;
        $this->emailService = $emailService;
        $this->delimiter = $delimiter;
    }

    public function handle()
    {
        try {
            ini_set('max_execution_time', 12000);
            ini_set('memory_limit', '2G');
            $x = 0;
            $downloadId = null;
            $this->getFileToLocal($this->s3Path, $this->s3Filename);
            // Create Log file
            $filenameLog = date('Y_m_d') . "_log_importar_alunos_" . rand(11111111, 99999999) . ".csv";
            $fileLog = tmpfile();
            $pathLog = stream_get_meta_data($fileLog)['uri'];
            $downloadLog = Downloads::create([
                'status' => 'pending',
                'period' => '',
                'filters' => '',
                'filename' => $filenameLog ?? ' - ',
                'platform_id' => $this->platform_id,
                'platforms_users_id' => $this->platform_user_id,
            ]);

            $downloadId = $downloadLog->id;

            $writerLog = WriterEntityFactory::createCSVWriter();
            $writerLog->openToFile($pathLog);
            $writerLog->addRow(WriterEntityFactory::createRowFromArray(['logs']));

            // Import CSV
            $reader = ReaderEntityFactory::createCSVReader();
            $reader->setFieldDelimiter($this->delimiter);
            $reader->open($this->filePath);

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    if ($x >= 1) { // First Line ALWAYS is IGNORED
                        $cells = $row->getCells();

                        // Verify if exist email and name in csv file
                        if (!$cells[0]->getValue() || !$cells[1]->getValue()) {
                            $nome = $cells[0]->getValue() ?? '';
                            $email = $cells[1]->getValue() ?? '';
                            $cellsLog = [WriterEntityFactory::createCell("Nome e/ou e-mail nao encontrado [Nome: $nome | Email: $email]")];
                            $singleRowLog = WriterEntityFactory::createRow($cellsLog);
                            $writerLog->addRow($singleRowLog);
                            continue;
                        }

                        // Validate the email is correct
                      /*  if (!validateEmail($cells[1]->getValue())) {
                            $cellsLog = [WriterEntityFactory::createCell("E-mail: {$cells[1]->getValue()} não é válido.")];
                            $singleRowLog = WriterEntityFactory::createRow($cellsLog);
                            $writerLog->addRow($singleRowLog);
                            continue;
                        }*/

                        // Verify if the subscriber exist
                        $subscriber = $this->subscriberExist($this->platform_id, $cells[1]->getValue());
                        if ($subscriber) {
                            if( $subscriber->status != $this->status ) {
                                if( $this->status != Subscriber::STATUS_CANCELED ) {
                                    $subscriber->status = $this->status;
                                }
                                $subscriber->source_register = Subscriber::SOURCE_IMPORT;
                                $subscriber->save();
                            }

                            // Verify if this subscriber has this plan
                            $subscription = $this->hasActiveSubscription($subscriber->id, $this->plan_id);
                            if ($subscription) {
                                //Cancel subscription
                                if( $this->status == Subscriber::STATUS_CANCELED ) {
                                    $subscription->status = Subscription::STATUS_CANCELED;
                                    $subscription->status_updated_at = \Carbon\Carbon::now();
                                    $subscription->cancellation_reason = "Cancelamento realizado via importação de alunos";
                                    $subscription->save();
                                    continue;
                                }
                                else
                                {
                                    $cellsLog = [WriterEntityFactory::createCell("E-mail: {$cells[1]->getValue()} já possúi esse plano.")];
                                    $singleRowLog = WriterEntityFactory::createRow($cellsLog);
                                    $writerLog->addRow($singleRowLog);
                                    continue;
                                }
                            } else {
                                if( $this->status == Subscriber::STATUS_ACTIVE ) {
                                    Subscription::insert([
                                        'platform_id' => $this->platform_id,
                                        'plan_id' => $this->plan_id,
                                        'subscriber_id' => $subscriber->id,
                                        'created_at' => date('Y-m-d H:i:s'),
                                    ]);

                                    $this->emailService->sendMailNewRegisterSubscriber($subscriber);
                                }
                            }
                        } else {
                            $subscriber = new Subscriber();
                            $subscriber->name = $cells[0]->getValue();
                            $subscriber->email = str_replace(" ", "", $cells[1]->getValue());
                            $subscriber->cel_phone = isset($cells[2]) ? $cells[2]->getValue() : null;
                            $subscriber->created_at = Carbon::now()->format('Y-m-d H:i:s');
                            $subscriber->status = $this->status;
                            $subscriber->last_acess = null;
                            $subscriber->plan_id = $this->plan_id;
                            $subscriber->platform_id = $this->platform_id;
                            $subscriber->raw_password = keygen(12);
                            $subscriber->source_register = Subscriber::SOURCE_IMPORT;
                            $subscriber->save();

                            Subscription::insert([
                                'platform_id' => $this->platform_id,
                                'plan_id' => $this->plan_id,
                                'subscriber_id' => $subscriber->id,
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);

                            if (isset($cells[1])) {
                                $this->emailService->sendMailNewRegisterSubscriber($subscriber);
                            }
                        }
                    }
                    $x++;
                }
            }

            $reader->close();
            $writerLog->close();

            // Save the Log
            Storage::disk($this->diskStorage)->putFileAs($this->saveDirectory, $pathLog, $filenameLog);
            Downloads::where('id', $downloadLog->id)->update([
                'status' => 'completed',
                'filesize' => Storage::disk($this->diskStorage)->size("$this->saveDirectory/" . $filenameLog),
                'url' => Storage::disk($this->diskStorage)->url("$this->saveDirectory/" . $filenameLog),
            ]);

            unlink($this->filePath);
        } catch (Exception $exception) {
            $this->failedJob($downloadId, $exception);
        }
    }

    /* Verify if subscriber exists */
    private function subscriberExist($platformId, $subscriberEmail)
    {
        $subscriber = Subscriber::wherePlatformId($platformId)->whereEmail($subscriberEmail)->first();
        return $subscriber ?? false;
    }

    /* Verify if this plan exist for this subscriber */
    private function hasActiveSubscription($subscriberId, $planId)
    {
        $subscription = Subscription::where('subscriber_id', $subscriberId)->where('plan_id', $planId)->where('status', Subscription::STATUS_ACTIVE)->first();
        return $subscription ?? false;
    }

    /* If the job failed, save in database */
    private function failedJob($downloadId, Exception $exception)
    {
        if (!empty($downloadId)) {
            Downloads::where('id', $downloadId)->update(['status' => 'failed',]);
        }
        throw $exception;
    }

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    private function getFileToLocal($s3Path, $filename)
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get($s3Path);
        $content = (string)$res->getBody();
        Storage::disk('public_local')->put('uploads/' . $filename, $content);
    }
}
