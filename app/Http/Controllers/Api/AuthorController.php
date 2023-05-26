<?php

namespace App\Http\Controllers\Api;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Traits\ApiResponse;

class AuthorController extends Controller
{
    use ApiResponse;

    /**
     * Get source list
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $authors = Author::orderBy('name', 'asc')->get();
            return $this->responseSuccess(AuthorResource::collection($authors), 'Author list retrieved succesfully!');
        } catch (\Throwable $th) {
            return $this->responseError($th->getMessage());
        }
    }
}
