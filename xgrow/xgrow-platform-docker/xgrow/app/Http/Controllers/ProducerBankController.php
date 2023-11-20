<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProducerBankRequest;
use App\Repositories\Contracts\ProducerRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ProducerBankController extends Controller
{
    private ProducerRepositoryInterface $producerRepository;

    public function __construct(ProducerRepositoryInterface $producerRepository)
    {
        $this->producerRepository = $producerRepository;
    }

    public function update(UpdateProducerBankRequest $request, $producerId)
    {
        $producer = $this->producerRepository->findById($producerId);
        if ($producer->platform_id != Auth::user()->platform_id) {
            throw new ModelNotFoundException('Producer not found on this platform');
        }

        $producer->fill($request->validated());
        $producer->save();

        $hash = '#nav-bank';
        return redirect()->to(url()->previous() . $hash)->with('Salvo com sucesso');
    }

}
