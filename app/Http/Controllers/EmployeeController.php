<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataObjects\EmployeeData;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final class EmployeeController extends Controller
{
    private const DEFAULT_EMPLOYEES = [
        [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
        ],
        [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane.doe@example.com',
        ]
    ];

    public function __construct(private readonly EmployeeService $service)
    {
        foreach (self::DEFAULT_EMPLOYEES as $employee) {
            $this->service->createEmployee(EmployeeData::fromArray($employee));
        }
    }

    public function index(): AnonymousResourceCollection
    {
        $employees = $this->service->getAllEmployees();
        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $record = $this->service->createEmployee($request->toDataObject());
        return (new EmployeeResource($record))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): EmployeeResource
    {
        $employee = $this->service->getEmployeeById($id);

        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteEmployee($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
