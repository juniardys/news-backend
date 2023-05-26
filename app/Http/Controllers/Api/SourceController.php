<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SourceResource;
use App\Models\Source;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SourceController extends Controller
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
            $sources = Source::orderBy('name', 'asc')->get();
            return $this->responseSuccess(SourceResource::collection($sources), 'Source list retrieved succesfully!');
        } catch (\Throwable $th) {
            return $this->responseError($th->getMessage());
        }
    }
}
