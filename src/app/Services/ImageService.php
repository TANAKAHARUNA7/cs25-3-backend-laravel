<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /*
     * 画像を保存して [image_key, image] を返す
     * image_key: 保存パス (例: news/abc.jpg)
     * image: 公開URL (例: /storage/news/abc.jpg)
     */
    public function store(UploadedFile $file, string $dir): array 
    {
        $key = $file->store($dir, 'public');
        
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        
        $url = $disk->url($key);

        return [
            'image_key' => $key,
            'image'     => $url,
        ];
    }

    /*
     * 既存画像があれば削除
     */
    public function delete(?string $imageKey): void
    {
        if (!$imageKey) {
            return;
        }

        if (Storage::disk('public')->exists($imageKey)) {
            Storage::disk('public')->delete($imageKey);
        }
    }
}