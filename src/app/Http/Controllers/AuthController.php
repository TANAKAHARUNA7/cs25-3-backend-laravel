<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account'   => ['required', 'string','max255'],
            'password'  => ['required', 'string', 'max255'],
            'user_name' => ['required', 'string', 'max255'],
            'role'      => Rule::in(['client', 'designer', 'manager']),
            'gender'    => Rule::in(['MEN','WOMEN', 'Non-binary']),
            'phone'     => ['string'],
            'birth'     => ['required', 'string'],
        ]);

        DB::table('users')->insert($validated);

        return response()->json([
            'success' => true,
            'message' => '会員登録完了しました'
        ]);
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
