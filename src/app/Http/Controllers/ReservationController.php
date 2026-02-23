<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Designer;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Client　：予約履歴照会
     */
    public function clientToIndex(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $reservation = Reservation::query()
            ->where('client_id', $userId)
            ->with([
                
                // 担当デザイナー名（designer -> user -> user_name）
                'designer.user:id,user_name',

                // メニュー名　＋　価格（services + pivot）
                'services:id,service_name'
            ])
            ->get();

        $data = $reservation->map(function ($r) {
            return [
                'reservation_id' => $r->id,
                'day'            => $r->day,
                'start_at'       => $r->start_at, 
                'end_at'         => $r->end_at,

                'designer_name'  => optional(optional($r->designer)->user)->user_name,
                
                'menues' => $r->services->map(function($s) {
                    return [
                        'service_id'   => $s->id,
                        'service_name' => $s->service_name,
                        'price'        => $s->pivot->unit_price,
                    ];
                }),

                'total_price' => $r->services->sum(fn($s) => (int) ($s->pivot->unit_price ?? 0)),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }


    /**
     * Designer：自分の顧客の予約をすべて照会
     */
    public function designerToIndex(Request $request): JsonResponse
    {
        $user     = $request->user();

        $designerId = $user->designer->id;

        $reservation = Reservation::query()
            ->where('designer_id', $designerId)
            ->with([
                
                // 担当顧客名
                'client:id,user_name',

                // メニュー名　＋　価格
                'services:id,service_name'
            ])
            ->get();

        $data = $reservation->map(function($r){
            return [
                'reservation_id' => $r->id,
                'client_name'    => $r->client->user_name,
                'day'            => $r->day,
                'start_at'       => $r->start_at,
                'end_at'         => $r->end_at,
                
                'menues'         => $r->services->map(function($s){
                    return [
                        'service_id'   => $s->id,
                        'service_name' => $s->service_name,
                        'price'        => $s->pivot->unit_price, 
                    ];
                }),

                'total_price'    => $r->services->sum(fn($s) => (int) $s->pivot->unit_price),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    /**
     * Client：予約作成
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'day'          => ['required', 'date'],
            'start_at'     => ['required', 'date_format:H:i:s'],
            'end_at'       => ['required', 'date_format:H:i:s'],
            'designer_id'  => ['required', 'integer'],
            'requirement'  => ['nullable', 'string'],
            'service_id'   => ['required', 'array', 'min:1'],
            // 配列のすべての要素のルール
            'service_id.*' => ['integer', 'exists:services,id'], 
        ]);

        $validated['client_id'] = $user->id;

        Reservation::create($validated);

        return response()->json([
            'success' => true,
            'message' => '予約が完了しました。'
        ], 201);
    }

    /**
     * 
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Client　：予約キャンセル
     * Designer：予約状態変更
     */
    public function update(Request $request, string $id)
    {
        //
    }

}
