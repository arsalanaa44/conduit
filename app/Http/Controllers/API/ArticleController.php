<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\MultiArticleResource;
use Illuminate\Support\Facades\Auth;
class ArticleController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api', ['except'=>['show', 'index']]);
    }
    public function index(Request $request)
    {
        $articles = Article::latest()->paginate(2); // Default pagination
        return new MultiArticleResource($articles);
    }

    public function feed(Request $request)
    {
        //{{APIURL}}/articles/feed?limit=3&offset=3
            $followedUsers = Auth::user()->following()->pluck('users.id');

            $articles = Article::whereIn('user_id', $followedUsers)
                ->orderBy('created_at', 'desc')
                ->limit($request->input('limit', 10))  // Default
                ->offset($request->input('offset', 0))  // Default
                ->get();

            return new MultiArticleResource($articles);
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        return new ArticleResource($article);
    }

    public function store(Request $request)
    {
        $article = new Article($request->get('article'));
        $article->user_id = auth()->id();
        $article->slug = Str::slug($article->title);
        $article->save();

        return new ArticleResource($article);
    }

    public function update(Request $request, $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        $data = Arr::only($request->input('article', []), ['title', 'description', 'body']);
        $article->update($data);

        if ($article->user_id !== Auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (isset($data['title'])) {
            $article->slug = Str::slug($data['title']);
            $article->save();
        }

        return new ArticleResource($article);
    }

    public function destroy($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        if ($article->user_id !== Auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $article->delete();

        return response()->json(['message' => 'Article deleted']);
    }

}
