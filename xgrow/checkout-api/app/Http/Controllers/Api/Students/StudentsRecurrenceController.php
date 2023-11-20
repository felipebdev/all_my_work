<?php

namespace App\Http\Controllers\Api\Students;

use App\Exceptions\Finances\ActionFailedException;
use App\Exceptions\Finances\PaymentChange\PaymentChangeInvalidException;
use App\Exceptions\Finances\PaymentChange\PaymentChangeNotAllowedException;
use App\Exceptions\Students\OperationNotAllowedException;
use App\Exceptions\Students\RecurrenceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionByRecurrenceRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\Students\StudentsRecurrenceRepository;
use App\Utils\XorIntObfuscator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StudentsRecurrenceController extends Controller
{

    use CustomResponseTrait;

    private StudentsRecurrenceRepository $studentsRecurrenceRepository;

    public function __construct(StudentsRecurrenceRepository $studentsRecurrenceRepository)
    {
        $this->studentsRecurrenceRepository = $studentsRecurrenceRepository;
    }

    public function getStudentsRecurrenceById($recurrence_id)
    {
        try {
            $recurrence_id = XorIntObfuscator::reveal($recurrence_id);
            $result = $this->studentsRecurrenceRepository->getStudentsRecurrenceById($recurrence_id);

            return $this->successJsonResponse($result);

        } catch (ModelNotFoundException $e) {
            return $this->customAbort('Recorrencia não encontrada', Response::HTTP_NOT_FOUND);
        } catch (OperationNotAllowedException $e) {
            return $this->customAbort('Recorrencia não permite pagamento manual.', Response::HTTP_CONFLICT);
        }
    }

    public function generateTransactionByRecurrence($recurrence_id, TransactionByRecurrenceRequest $request)
    {
        try {
            $recurrenceId = XorIntObfuscator::reveal($recurrence_id);

            Log::debug('generateTransactionByRecurrence started', [
                'request' => $request->all(),
                'obfuscated_recurrence_id' => $recurrence_id,
                'recurrence_id' => $recurrenceId,
            ]);

            $result = $this->studentsRecurrenceRepository->generateTransactionByRecurrence(
                $recurrenceId,
                $request->all()
            );

            return $this->successJsonResponse($result);
        } catch (RecurrenceNotFoundException $exception) {
            Log::debug('generateTransactionByRecurrence RecurrenceNotFoundException');
            $this->customAbort('Recorrencia não encontrada', Response::HTTP_NOT_FOUND);
        } catch (PaymentChangeInvalidException $exception) {
            Log::debug('generateTransactionByRecurrence PaymentChangeInvalidException');
            $this->customAbort('Não é possível gerar pagamento para essa recorrencia!', Response::HTTP_NOT_FOUND);
        } catch (PaymentChangeNotAllowedException $exception) {
            Log::debug('generateTransactionByRecurrence PaymentChangeNotAllowedException');
            $this->customAbort('Recorrencia não encontrada', Response::HTTP_NOT_FOUND);
        } catch (ActionFailedException $exception) {
            Log::debug('generateTransactionByRecurrence ActionFailedException');
            $this->customAbort('Falha no processo!', Response::HTTP_NOT_FOUND);
        }
    }

}
