<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Reservation;
use App\Models\TimeOff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
                
                'status'         => $r->status,
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
                'status'         => $r->status,
                
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
        // 1. ログインユーザ情報取得
        $user = $request->user();

        // 2. 入力チェック
        $validated = $request->validate([
            'day'           => ['required', 'date'],
            'start_at'      => ['required', 'date_format:H:i:s'],
            'designer_id'   => ['required', 'integer'],
            'requirement'   => ['nullable', 'string'],
            'services'      => ['required', 'array', 'min:1'],
            // 配列のすべての要素のルール
            'services.*'  => ['required', 'integer', 'exists:services,id'],  
        ]);

        
        return DB::transaction((function () use ($validated, $user){

            // 3. サービスから合計時間を計算
            $serviceIds = $validated['services'];

            // サービス取得
            $services = Service::whereIn('id', $serviceIds)->get();

            // 合計施術時間
            $totalMinutes = (int) $services->sum('duration_min');

            // 4. 開始→終了時刻を自動計算
            // 開始時間
            $startDateTime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $validated['day'].' '.$validated['start_at']
            );

            
            $endDateTime = $startDateTime->copy()->addMinutes($totalMinutes);

            $startTime = $startDateTime->format('H:i:s');
            $endTime   = $endDateTime->format('H:i:s');
            

            // 5. 予約重複チェック（同じデザイナー・同日・時間帯が重なる）
            $reservationOverlap = Reservation::where('designer_id', $validated['designer_id'])
                ->whereDate('day', $validated['day'])
                ->whereNull('cancelled_at') // キャンセル済みは除外
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_at', '<', $endTime)
                      ->where('end_at',   '>', $startTime);
                })
                ->lockForUpdate() // 同時に予約が入ることを防止する
                ->exists();


            // 6. TimeOff(休日)との重複チェック（同じデザイナー・日付が重なる）
            $hasTimeOff = TimeOff::where('designer_id', $validated['designer_id'])
                ->whereDate('start_at', '<=', $validated['day'])
                ->whereDate('end_at', '>=', $validated['day'])
                ->exists();

            if ($reservationOverlap || $hasTimeOff) {
                return response()->json([
                    'success' => false,
                    'message' => 'その時間帯は予約不可能です',
                ], 409);
            }

            // reservationsに入れるデータだけにする（service_idは除外）
            $reservationData = collect($validated)->except('services')->all();
            
            // end_at保存
            $reservationData['start_at']  = $startTime;
            $reservationData['end_at']    = $endTime;
            $reservationData['client_id'] = $user->id;

            // Reservationテーブルに保存
            $reservation = Reservation::create($reservationData);
    

            // 価格を取得
            $prices = Service::whereIn('id', $serviceIds)->pluck('price', 'id');
            
            // sync用データ
            $syncData = [];
            foreach ($serviceIds as $sid) {
                $sid = (int) $sid;

                $syncData[$sid] = [
                    'unit_price' => (int) ($prices[$sid] ?? 0),
                ];
            }

            $reservation->services()->sync($syncData);

            return response()->json([
                'success' => true,
                'message' => '予約が完了しました',
            ], 201);

        }));
    }

    /**
     * Client　：予約キャンセル
     */
    public function cancelByClient(Request $request, string $id): JsonResponse
    {
        // 1. ログインユーザ情報摂取
        $user = $request->user();

        // 2. 対象予約取得
        $reservation = Reservation::where('id', $id)
            ->where('client_id',$user->id)
            ->firstOrFail();

        
        if ($reservation->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'すでにキャンセル済みです。'
            ], 409);
        }

        //　3. バリデーションチェック
        $valideted = $request->validate([
            'cancel_reason' => ['required', 'string'],
        ]);

        //　4. update
        $reservation->update([
            'cancel_reason' => $valideted['cancel_reason'],
            'cancelled_at'  => now(),
            'status'        => 'cancelled',
        ]);

        return response()->json([
            'success' => true,
            'message' => '予約をキャンセルしました。',
        ], 200);

    }

    /*
    * Designer：予約状態変更
    */
    public function statusChangeByDesigner(Request $request, string $id): JsonResponse
    {
        // 1. ログインユーザ情報摂取
        $user = $request->user();

        // 2. designerID摂取
        $designerId = $user->designer->id;

        // 2. 対象予約摂取
        $reservation = Reservation::where('id', $id)
            ->where('designer_id', $designerId)
            ->firstOrFail();

        // 3. バリデーションチェック
        $validate = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['peding', 'confirmed', 'checked_in', 'completed', 'no_show'])
            ],
        ]);

        // 4. update
        $reservation->update([
            'status' => $validate['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => '予約状況を変更しました。',
        ]);
    }



}
