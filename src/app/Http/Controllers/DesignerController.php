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
        return DB::transaction(function () use ($request, $imageService, $validated, $userId) {
            
            //　画像保存
            $img = $imageService->store($request->file('image'), 'designer');

            //　DBに保存する値をまとめる
            $data = array_merge($validated, [
                'user_id'   => $userId,
                'image_key' => $img['image_key'],
                'image'     => $img['image'], 
            ]);

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
    public function update(Request $request, string $id)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'image'       => ['required', 'image', 'max:2048'],
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
                
                $img = $imageService->store($request->file('image'), 'designer');

                $data['image_key'] = $img['image_key'];
                $data['immage']    = $img['image'];
            }

            // DBに保存できないので消す（nullableでも来た場合UploadedFileになる）
            unset($data['image']);

            $designer->update()->json([
                'success' => true,
                'message' => '更新に成功しました。'
            ], 200);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
