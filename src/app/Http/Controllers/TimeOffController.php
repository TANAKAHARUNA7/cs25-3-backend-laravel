<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TimeOff;
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
        $result = TimeOff::all();
        
        // フロントに返す
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
