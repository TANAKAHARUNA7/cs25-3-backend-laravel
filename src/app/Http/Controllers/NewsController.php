<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * すべてのニュースを照会
     */
    public function index()
    {
        $news = News::all();

        return response()->json([
            'success' => true,
            'data'    => $news
        ]);
    }

    /**
     * ニュース作成
     */
    public function store(Request $request, ImageService $imageService)
    {
        // 1. バリデーションチェック
        $validate = $request->validate([
            'title'     => ['required', 'string'],
            'content'   => ['required', 'string'],
            'image'     => ['nullable', 'image', 'max:2048'],
        ]);

        // 2. テキストデータをDBに保存 (まずは画像なしでNewsを作成)
        $news = News::create([
            'title'   => $validate['title'],
            'content' => $validate['content'], 
        ]);
        
        // 3. 画像が送られてきた場合のみ処理
        if ($request->hasFile('image')) {
            
            // 画像を storage/app/public/news に保存
            // 戻り値として image_key と image(URL) が返る
            $img = $imageService->store($request->file('image'), 'news');

            // 保存した画像情報を News に更新
            $news->update([
                'image_key' => $img['image_key'], // 実体の保存パス
                'image'     => $img['image'],     // 表示用URL
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '投稿しました。'
        ], 201);
    }

    /**
     * 特定のニュースを照会
     */
    public function show(string $id)
    {
        //　news記事内容を持ってくる
        $news = News::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $news
        ]);

    }

    /**
     * ニュース修正
     */
    public function update(Request $request, string $id, ImageService $imageService)
    {
        //　記事情報摂取
        $news = News::findOrFail($id);

        //　バリデーションチェック
        $validate = $request->validate([
            'title'   => ['required', 'string'],
            'content' => ['required', 'string'], 
            'image'   => ['nullable', 'image', 'max:2048'],
        ]);

        //　テキスト更新
        $news->update($validate);

        // 画像が来たら差し替え
        if ($request->hasFile('image')) {

            // 既存画像があれば削除
            $imageService->delete($news->image_key);

            // 新画像保存
            $img = $imageService->store($request->file('image'), 'news');

            // 画像情報更新
            $news->update([
                'image_key' => $img['image_key'],
                'image'     => $img['image'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => '更新しました。',
        ], 201);

    }

    /**
     * ニュース削除
     * DB削除前に画像ファイルも削除する
     */
    public function destroy(string $id, ImageService $imageService)
    {
        //　該当する記事をもってくる
        $news = News::findOrFail($id);

        // 画像ファイル削除（存在する場合のみ）
        $imageService->delete($news->image_key);

        // ③ レコード削除
        $news->delete();

        return response()->json([
        'success' => true,
        'message' => '削除しました。',
    ]);

    }
}
