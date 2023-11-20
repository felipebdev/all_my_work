<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidOperationException;
use App\Http\Requests\StoreProducerRequest;
use App\Repositories\Banks\Banks;
use App\Repositories\Contracts\ProducerRepositoryInterface;
use App\Services\Auth\ClientStatus;
use App\Services\Objects\ProducerReportFilter;
use App\Services\Producer\ProducerService;
use App\Services\Reports\ProducerReportService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProducerController extends Controller
{
    private ProducerReportService $producerReportService;

    public function __construct(ProducerReportService $producerReportService)
    {
        $this->producerReportService = $producerReportService;
    }

    public function index(Request $request)
    {
        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        return view('producers.index', [
            'isOwner' => $status->isOwner,
            'clientApproved' => $status->clientApproved,
            'recipientStatusMessage' => $status->recipientStatusMessage,
        ]);
    }

    public function getAll(Request $request)
    {
        $filters = new ProducerReportFilter($request->all());
        $query = $this->producerReportService->getProducersReport(Auth::user()->platform_id, $filters);
        return datatables()->eloquent($query)->make();
    }

    public function exportReports(Request $request)
    {
        $typeFile = $request->typeFile ?? 'xlsx';
        $filters = new ProducerReportFilter($request->all());

        $this->producerReportService->exportReport(Auth::user()->platform_id, Auth::user(), $typeFile, $filters);
    }

    public function create(Request $request)
    {
        $terms = file_get_contents('https://loripsum.net/api/10/medium/plaintext');
        return view('producers.create')->with('terms', $terms);
    }

    public function store(StoreProducerRequest $request, ProducerService $service)
    {
        try {
            $producer = $service->storeProducer(Auth::user(), $request->producer_name, $request->producer_email);
        } catch (InvalidOperationException $exception) {
            return redirect()->back()->withInput()->withErrors(['error' => $exception->getErrors()]);
        }

        return redirect()->route('producers.edit', ['producerId' => $producer->id]);
    }

    public function edit(Request $request, ProducerRepositoryInterface $producerRepository, $producerId)
    {
        $producer = $producerRepository->findById($producerId);

        $platformUser = $producerRepository->getPlatformUserByProducerId($producer->id);

        $bankList = Banks::getBankList();

        return view('producers.edit')
            ->with('user', $platformUser)
            ->with('producer', $producer)
            ->with('bankList', $bankList);
    }

    public function destroy(Request $request, ProducerRepositoryInterface $producerRepository, $producerId)
    {
        $producer = $producerRepository->findById($producerId);
        if ($producer->platform_id != Auth::user()->platform_id) {
            throw new ModelNotFoundException('Producer not found on this platform');
        }

        try {
            $result = $producer->delete();

            if (!$result) {
                return response()->json([
                    'error' => true,
                    'message' => 'Falha ao remover produtor',
                ], 400);
            }
        } catch (QueryException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Falha ao remover produtor, existem produtos vinculados',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Coprodutor removido com sucesso.',
        ]);
    }

}
