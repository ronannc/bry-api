<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Services\Company\CompanyCreateService;
use App\Services\Company\CompanyDeleteService;
use App\Services\Company\CompanyQueryService;
use App\Services\Company\CompanyUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyQueryService $queryService,
        protected CompanyCreateService $createService,
        protected CompanyUpdateService $updateService,
        protected CompanyDeleteService $deleteService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $paginated = $request->get('paginated', true);
        $paginated = filter_var($paginated, FILTER_VALIDATE_BOOLEAN);
        return response()->json($this->queryService->search($request->get('filters', []), $paginated));
    }

    public function store(CompanyRequest $request): JsonResponse
    {
        $company = $this->createService->create($request->validated());
        return response()->json($company, 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $company = $this->queryService->search($request->get('filters', []), false, $id);
        return response()->json($company);
    }

    public function update(CompanyRequest $request, $id): JsonResponse
    {
        $company = $this->updateService->update($id, $request->validated());
        return response()->json($company);
    }

    public function destroy($id): JsonResponse
    {
        $this->deleteService->delete($id);
        return response()->json(null, 204);
    }
}
