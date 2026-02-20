<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TimeOff;
use App\Models\Designer;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class TimeOffController extends Controller
{
    /**
     * 全Designerの休日照会
     */
    public function index(): JsonResponse
    {
        // DB接続しデータを摂取
        $timeOffs = TimeOff::all();
        
        // フロントに返す
        return response()->json($timeOffs);
    }

    /**
     * 特定のDesignerの休日を新規作成
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'designer_id' => ['required', 'integer', 'exists:designers,id'],
            'start_at'    => ['required', 'date'],
            'end_at'      => ['required', 'date']
        ]);

        $timeOff = TimeOff::create($validated);

        return response()->json([
            'success'  => true,
            'message'  => '登録成功しました。',
            'date'     => [
                'time_off' => $timeOff
            ] 
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
