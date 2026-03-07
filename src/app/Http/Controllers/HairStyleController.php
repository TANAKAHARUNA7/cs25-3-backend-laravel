<?php

namespace App\Http\Controllers;

use App\Models\HairStyle;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HairStyleController extends Controller
{
    /**
     * すべてのヘアスタイル情報を照会
     */
    public function index() :JsonResponse
    {
        $hairStyle = HairStyle::all();

        return response()->json([
            'success' => true,
            'data'    => $hairStyle
        ]);
    }

    /**
     * ヘアスタイル情報作成
     */
    public function store(Request $request, ImageService $imageService) :JsonResponse
    {
        // バリデーション確認
        $validated = $request->validate([
            'title'      => ['required','string',],
            'image'      => ['required','image','max:2048'],
            'description'=> ['required','string',],
        ]);

        return DB::transaction(function () use ($request, $validated, $imageService):JsonResponse {

            // 画像保存
            $img = $imageService->store($request->file('image'), 'hairstyle');

            $data = array_merge($validated,[
                'title'     => $validated['title'],
                'description'=> $validated['description'],
                'image_key' => $img['image_key'],
                'image'     => $img['image'],
            ]);

            HairStyle::create($data);

            return response()->json([
                'success' => true,
                'message' => '登録が完了しました。'
            ], 201);
        });
    }

    /**
     * 特定のヘアスタイル情報を照会
     */
    public function show(string $id)
    {
        $hairStyle = HairStyle::findOrFail($id);

        return response()->json([
            'success'=> true,
            'data'   => $hairStyle
        ]);
    }

    /**
     * 特定のヘアスタイル情報を修正
     */
    public function update(Request $request, string $id, ImageService $imageService): JsonResponse
    {
        $hairStyle = HairStyle::findOrFail($id);

        $validated = $request->validate([
            'title'=> ['required','string',],
            'image'=> ['required','image','max:2048'],
            'description'=> ['required','string',],
        ]);

        return DB::transaction(function () use ($request, $hairStyle, $validated,$imageService){

            $data = $validated;

            // もし画像が送られてきた時だけ保存してDB更新
            if ($request->hasFile('image')) {

                $imageService->delete($hairStyle->image_key);

                $img = $imageService->store($request->file('image'), 'designer');

                $data['image_key'] = $img['image_key'];
                $data['image']     = $img['image'];
                
            } else {
                unset($data['image']);
            }

            $hairStyle->update($data);

            return response()->json([
                'success' => true,
                'message' => '修正成功しました。'
            ], 201);
        });

    }

    /**
     *
     * 特定のヘアスタイル情報を削除する
     */
    public function destroy(string $id, ImageService $imageService):JsonResponse
    {
        // 該当するhairStyleの情報を持ってくる
        $hairStyle = HairStyle::findOrFail($id);

        // 画像ファイル削除
        $imageService->delete($hairStyle->image_key);

        // レコード削除
        HairStyle::destroy($id);

        return response()->json([
            'success'=> true
        ]);
    }
}
