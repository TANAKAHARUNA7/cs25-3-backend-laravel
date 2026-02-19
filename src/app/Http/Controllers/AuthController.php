<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        // バリデーションチェック
        $validated = $request->validate([
            'account'   => ['required', 'string','max:255', 'unique:users,account'],
            'password'  => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:255'],
            'role'      => ['required', Rule::in(['client', 'designer', 'manager'])],
            'gender'    => ['required', Rule::in(['MEN','WOMEN', 'Non-binary'])],
            'phone'     => ['nullable', 'string', 'max:30'],
            'birth'     => ['required', 'date'],
        ]);

        // パスワードハッシュ処理
        $validated['password'] = Hash::make($validated['password']);

        // UserModelを使ってDBへデータをINSERT 
        User::create($validated);

        return response()->json([
            'success' => true,
            'message' => '会員登録完了しました'
        ], 201);
    }



    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account'  => ['required', 'string'],
            'password' => ['required', 'string'], 
        ]);

        $user = User::where('account', $validated['account'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)){
            return response()->json([
                'success' => false,
                'message' => '認証に失敗しました。',
            ], 401);
        }

        // トークン発行
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'      => $user->id,
                'account' => $user->account,
                'role'    => $user->role ?? null, 
            ],
        ]);
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'ログアウトしました',
        ]);
    }

}
