<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        // バリデーションチェック
        $validete = $request->validate([
            'title'     => ['required', 'string'],
            'content'   => ['required', 'string'],
            'image'     => ['nullabel', 'string'],
            'image_key' => ['nullabel', 'string'] 
        ]);

        //　Insert
        News::create($validete);

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
    public function update(Request $request, string $id)
    {
        //　記事情報摂取
        $news = News::findOrFail($id);

        //　バリデーションチェック
        $validate = $request->validate([
            'title'   => ['required', 'string'],
            'content' => ['required', 'string'], 
        ]);

        //　update
        $news->update($validate);

        return response()->json([
            'success' => true,
            'message' => '更新しました。',
        ], 201);

    }

    /**
     * ニュース削除
     */
    public function destroy(string $id)
    {
        //　該当する記事をもってくる
        $news = News::findOrFail($id);

        //　削除
        $news->delete($id);
    }
}
