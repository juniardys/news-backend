<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\News;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\NewsListRequest;

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

            // If Authenticated
            $user = $request->user();
            if ($user) {
                $sourcePreferences = $user->sourcePreferences->pluck('id');
                $categoryPreferences = $user->categoryPreferences->pluck('id');

                if ($sourcePreferences->count() > 0) {
                    $query = $query->whereIn('category_id', $sourcePreferences);
                }

                if ($categoryPreferences->count() > 0) {
                    $query = $query->whereIn('source_id', $categoryPreferences);
                }
            }

            if ($search) {
                $query = $query->search($search);
            }

            if ($categories) {
                $categories = $categories ? array_map('trim', explode(',', $categories)) : [];
                $query = $query->whereIn('category_id', $categories);
            }

            if ($sources) {
                $sources = $sources ? array_map('trim', explode(',', $sources)) : [];
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
            return $this->responseSuccess(NewsResource::collection($news)->response()->getData(true), 'News list retrieved successfully!');
        } catch (\Throwable $th) {
            return $this->responseError($th->getMessage());
        }
    }
}
