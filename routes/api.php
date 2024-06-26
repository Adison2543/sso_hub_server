<?php

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api', 'scope:view-user')->get('/user', function (Request $request) {
    $courses_list = $request->user()->course ?? [];
    $course = App\Models\Course::where('id', end($courses_list))->first(['code', 'name']);
    $user_data = [
        "name" => $request->user()->name,
        "username" => $request->user()->email,
        "role" => $request->user()->role,
        "course_code" => $course->code,
        "course_name" => $course->name,
        "branch" => $request->user()->brn,
        "agency" => $request->user()->agn,
    ];
    return $user_data;
});

Route::middleware('auth:api', 'scope:i-prompt')->get('/i-prompt', function (Request $request) {
    if ($request->user()->can('i-prompt')) {
        return $request->user();
    } else {
        return ['message' => 'Permission denied'];
    }
});

Route::middleware('auth:api', 'scope:kst-plus')->get('/kst-plus', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/logmeout', function (Request $request) {
    $user = $request->user();
    // $accessToken = $user->token();
    $accessTokens = DB::table("oauth_access_tokens")->where("user_id", $user->id)->get();
    foreach ($accessTokens as $accessToken) {
        DB::table("oauth_refresh_tokens")->where("access_token_id", $accessToken->id)->delete();
    }

    $accessTokens = DB::table("oauth_access_tokens")->where("user_id", $user->id)->delete();
    // DB::table("oauth_refresh_tokens")->where("access_token_id", $accessToken->id)->delete();
    // $accessToken->delete();
    return response()->json([
        "message" => "Revoked"
    ]);
});

Route::middleware('auth.basic')->get('/courses', function () {
    $courses = Course::get(['code', 'name']);
    $res = [];
    foreach ($courses as $course) {
        $charsOnly = preg_replace('/\d+/', '', $course->code);
        $agn = App\Models\Agency::where('prefix', $charsOnly)->first();
        $res[] = [
            "code" => $course->code,
            "name" => $course->name,
            "agn" => $agn->name,
        ];
    }
    return $res;
});

