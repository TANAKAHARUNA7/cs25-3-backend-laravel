<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * すべてのサービス一覧を返す
     */
    public function index(): JsonResponse
    {
        $services = Service::all();

        return response()->json($services);
    }

    /**
     * 新しいサービスを作成する
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'price'        => ['required', 'numeric', 'min:0'],
            'duration_min' => ['required', 'integer', 'min:1'],
        ]);

        $service = Service::create($validated);

        return response()->json($service, 201);
    }

    /**
     * 指定したサービスを返す
     */
    public function show(string $id): JsonResponse
    {
        $service = Service::findOrFail($id);

        return response()->json($service);
    }

    /**
     * 指定したサービスを更新する
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'service_name' => ['sometimes', 'string', 'max:255'],
            'price'        => ['sometimes', 'numeric', 'min:0'],
            'duration_min' => ['sometimes', 'integer', 'min:1'],
        ]);

        $service->update($validated);

        return response()->json($service);
    }

    /**
     * 指定したサービスを削除する
     */
    public function destroy(string $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(null, 204);
    }
}
