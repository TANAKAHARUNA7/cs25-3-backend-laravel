<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use App\Models\Designer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DesignerController extends Controller
{
    /**
     * すべてのデザイナー情報を表示する
     */
    public function index():JsonResponse
    {
        $designer = Designer::all();

        return response()->json([
            'success' => true,
            'data'    => $designer
        ]);
    }

    /**
     * designer情報作成
     */
    public function store(Request $request, ImageService $imageService):JsonResponse
    {
        // ログインユーザの情報摂取
        $userId = $request->user()->id;

        // バリデーションチェック
        $validated = $request->validate([
            'image'       => ['required', 'image', 'max:2048'],
            'experience'  => ['required', 'integer'],
            'good_at'     => ['required', 'string'],
            'personality' => ['required', 'string'],
            'message'     => ['required', 'string'],
        ]);

        // DBに保存する値
        return DB::transaction(function () use ($request, $imageService, $validated, $userId):JsonResponse {

            //　画像保存
            $img = $imageService->store($request->file('image'), 'designer');

            //　DBに保存する値をまとめる
            $data =[
                'user_id'     => $userId,
                'experience'  => $validated['experience'],
                'good_at'     => $validated['good_at'],
                'personality' => $validated['personality'],
                'message'     => $validated['message'],
                'image_key'   => $img['image_key'],
                'image'       => $img['image'],
            ];

            Designer::create($data);

            return response()->json([
                'success' => true,
                'message' => '登録が完了しました。'
            ], 201);

        });
    }

    /**
     * 特定のdesigner情報照会
     */
    public function show(string $id):JsonResponse
    {
        $designer = Designer::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $designer
        ]);
    }

    /**
     * 自分のプロフィールのみ編集可能
     */
    public function update(Request $request, ImageService $imageService): JsonResponse
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'image'       => ['nullable', 'image', 'max:2048'],
            'experience'  => ['required', 'integer'],
            'good_at'     => ['required', 'string'],
            'personality' => ['required', 'string'],
            'message'     => ['required', 'string'],
        ]);

        return DB::transaction(function () use ($request, $imageService, $validated, $userId) {

            // 本人のDesignerレコードを取得（無ければ404）
            $designer = Designer::where('user_id', $userId)->firstOrFail();

            $data = $validated;

            // 画像が送られてきたときだけ保存してDB更新
            if ($request->hasFile('image')) {

                $imageService->delete($designer->image_key);

                $img = $imageService->store($request->file('image'), 'designer');

                $data['image_key'] = $img['image_key'];
                $data['image']    = $img['image'];
            } else {
                // DBに保存できないので消す（nullableでも来た場合UploadedFileになる）
                unset($data['image']);
            }

            $designer->update($data);

            return response()->json([
                'success' => true,
                'message' => '更新に成功しました。'
            ], 200);
        });
    }

    /**
     * managerのみdesignerのプロフィールを削除可能
     */
    public function destroy(string $id, ImageService $imageService): JsonResponse
    {
        // 該当するdesignerの情報を持ってくる
        $designer = Designer::findOrFail($id);

        // 画像ファイル削除
        $imageService->delete($designer->image_key);

        // レコード削除
        Designer::destroy($id);

        return response()->json([
            'success'=> true
        ]);
    }
}
