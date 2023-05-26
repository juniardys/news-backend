<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\SavePreferencesRequest;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Get user data
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user()->load(['sourcePreferences', 'categoryPreferences', 'authorPreferences']);
            return $this->responseSuccess(new UserResource($user), 'User data retrieved successfully!');
        } catch (\Throwable $th) {
            return $this->responseError($th->getMessage());
        }
    }

    public function savePreferences(SavePreferencesRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find($request->user()->id);
            $sources = $request->input('sources') ? array_map('trim', explode(',', $request->input('sources'))) : [];
            $categories = $request->input('categories') ? array_map('trim', explode(',', $request->input('categories'))) : [];
            $authors = $request->input('authors') ? array_map('trim', explode(',', $request->input('authors'))) : [];

            $user->sourcePreferences()->sync($sources);
            $user->categoryPreferences()->sync($categories);
            $user->authorPreferences()->sync($authors);

            $user->load(['sourcePreferences', 'categoryPreferences', 'authorPreferences']);

            DB::commit();
            return $this->responseSuccess(new UserResource($user), 'Preferences saved successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseError('Error when trying to save preferences!');
        }
    }
}
