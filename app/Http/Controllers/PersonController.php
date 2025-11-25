<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use App\Services\Person\PersonCreateService;
use App\Services\Person\PersonDeleteService;
use App\Services\Person\PersonQueryService;
use App\Services\Person\PersonUpdateService;
use App\Services\Person\PersonDuplicatesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class PersonController extends Controller
{
    public function __construct(
        protected PersonQueryService  $queryService,
        protected PersonCreateService $createService,
        protected PersonUpdateService $updateService,
        protected PersonDeleteService $deleteService
    ){}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->queryService->search($request->get('filters', [])));
    }

    /**
     * @throws Throwable
     */
    public function store(PersonRequest $request): JsonResponse
    {
        $person = $this->createService->create($request->validated());
        return response()->json($person, 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $person = $this->queryService->search($request->get('filters', []), $id);
        return response()->json($person);
    }

    public function update(PersonRequest $request, $id): JsonResponse
    {
        $person = $this->updateService->update($id, $request->validated());
        return response()->json($person);
    }

    public function destroy($id): JsonResponse
    {
        $this->deleteService->delete($id);
        return response()->json(null, 204);
    }

    public function duplicadas(PersonDuplicatesService $cacheService): JsonResponse
    {
        $groups = $cacheService->getAllGroups();
        return response()->json($groups);
    }

    public function updateDuplicates(): JsonResponse
    {
        Artisan::call('person:duplicates');
        return response()->json(['message' => 'Processamento de duplicidades disparado.'], 202);
    }
}
