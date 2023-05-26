<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Get category list
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // If Authenticated
            if ($user && $user->categoryPreferences->count() > 0) {
                $categories = $user->categoryPreferences;
            } else {
                $categories = Category::orderBy('name', 'asc')->get();
            }
            return $this->responseSuccess(CategoryResource::collection($categories), 'Category list retrieved successfully!');
        } catch (\Throwable $th) {
            return $this->responseError($th->getMessage());
        }
    }
}
