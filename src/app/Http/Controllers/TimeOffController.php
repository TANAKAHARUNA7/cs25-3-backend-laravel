<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TimeOff;
use Illuminate\Http\JsonResponse;
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
     * 特定の休日を照会
     */
    public function show(string $id):JsonResponse
    {
        // timeoff_idで該当する休日情報を持ってくる
        $timeOff = TimeOff::findOrFail($id);

        // レスポンスを返す
        return response()->json([
            'success' => true,
            'date'    => [
                'time_off' => $timeOff
            ]
        ]);
    }


    /**
     * 特定のDesigner休日を照会
     */
    public function designer(string $designer_id):JsonResponse
    {
        // designer_idで特定のdesignerの休日を照会
        $timeOff = TimeOff::where('designer_id', $designer_id)->get();

        return response()->json([
            'success' => true,
            'date'    => [
                'time_off' => $timeOff
            ]
        ]);
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
