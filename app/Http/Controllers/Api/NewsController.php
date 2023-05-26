<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\News;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsListRequest;
use App\Http\Resources\NewsResource;

class NewsController extends Controller
{
    use ApiResponse;

    /**
     * Get source list
     *
     * @param NewsListRequest $request
     * @return void
     */
    public function index(NewsListRequest $request)
    {
        try {
            $query = new News;
            $search = $request->get('search');
            $categories = $request->get('categories');
            $sources = $request->get('sources');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            if ($search) {
                $query = $query->search($search);
            }

            if ($categories) {
                $categories = array_map('trim', explode(',', $categories));
                $query = $query->whereIn('category_id', $categories);
            }

            if ($sources) {
                $sources = array_map('trim', explode(',', $sources));
                $query = $query->whereIn('source_id', $sources);
            }

            if ($start_date && $end_date) {
                $start = Carbon::parse($start_date)->startOfDay();
                $end = Carbon::parse($end_date)->endOfDay();
                $query = $query->whereBetween('published_at', [$start, $end]);
            }

            $news = $query->with(['source', 'category', 'author'])
                ->orderBy('published_at', 'desc')
                ->paginate($request->limit ?: 10);
            return $this->responseSuccess(NewsResource::collection($news)->response()->getData(true), 'News list retrieved succesfully!');
        } catch (\Throwable $th) {
            return $this->responseError($th->getMessage());
        }
    }
}
