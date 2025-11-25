<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use App\Services\Person\DuplicateFinderService;
use App\Services\Person\PersonCreateService;
use App\Services\Person\PersonDeleteService;
use App\Services\Person\PersonQueryService;
use App\Services\Person\PersonUpdateService;
use Illuminate\Http\Request;
use Throwable;

class PersonController extends Controller
{
    public function __construct(
        protected PersonQueryService  $queryService,
        protected PersonCreateService $createService,
        protected PersonUpdateService $updateService,
        protected PersonDeleteService $deleteService
    ){}

    public function index(Request $request)
    {
        return response()->json($this->queryService->search($request->get('filters', [])));
    }

    /**
     * @throws Throwable
     */
    public function store(PersonRequest $request)
    {
        $person = $this->createService->create($request->validated());
        return response()->json($person, 201);
    }

    public function show(Request $request, $id)
    {
        $person = $this->queryService->search($request->get('filters', []), $id);
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

    public function duplicadas(DuplicateFinderService $finder)
    {
        $groups = $finder->findGroups();
        return response()->json($groups);
    }
}
