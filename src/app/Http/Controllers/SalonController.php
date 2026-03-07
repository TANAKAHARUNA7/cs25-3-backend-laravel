<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Salon;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SalonController extends Controller
{
    /**
     * サロン情報表示
     */
    public function index():JsonResponse
    {
        $salon = Salon::all();

        return response()->json([
            'saccess' => true,
            'data'    => $salon
        ]);
    }

    /**
     * サロン情報update
     */
    public function update(Request $request, ImageService $imageService) :JsonResponse
    {
        $validated = $request->validate([
            'image'        => ['nullable', 'image', 'max:2048'],
            'introduction' => ['required', 'string'],
            'information'  => ['required', 'json'],
            'map'          => ['required', 'string'],
            'traffic'      => ['required', 'json'],
        ]);

        // DBに保存する値
        return DB::transaction(function () use ($request, $validated, $imageService) {

            $salon = Salon::firstOrFail();

            $data = [
                'introduction' => $validated['introduction'],
                'information'  => json_decode($validated['information'], true),
                'map'          => $validated['map'],
                'traffic'      => json_decode($validated['traffic'], true),
            ];

            if ($request->hasFile('image')) {

                // 既存画像削除
                $imageService->delete($salon->image_key);

                // 新画像を保存
                $img = $imageService->store($request->file('image'), 'salon');

                $data['image']     = $img['image'];
                $data['image_key'] = $img['image_key'];
            }

            $salon->update($data);

            return response()->json([
                'success' => true,
                'message' => '修正しました。'
            ], 201);

        });
    }

}
