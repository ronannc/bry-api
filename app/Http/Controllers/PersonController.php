<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use App\Services\Person\PersonCreateService;
use App\Services\Person\PersonDeleteService;
use App\Services\Person\PersonQueryService;
use App\Services\Person\PersonUpdateService;

class PersonController extends Controller
{
    public function __construct(
        protected PersonQueryService $queryService,
        protected PersonCreateService $createService,
        protected PersonUpdateService $updateService,
        protected PersonDeleteService $deleteService
    ) {}

    public function index()
    {
        return response()->json($this->queryService->search());
    }

    public function store(PersonRequest $request)
    {
        $person = $this->createService->create($request->validated());
        return response()->json($person, 201);
    }

    public function show($id)
    {
        $person = $this->queryService->search($id);
        return response()->json($person);
    }

    public function update(PersonRequest $request, $id)
    {
        $person = $this->updateService->update($id, $request->validated());
        return response()->json($person);
    }

    public function destroy($id)
    {
        $this->deleteService->delete($id);
        return response()->json(null, 204);
    }
}
