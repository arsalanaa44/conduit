<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\MultiArticleResource;

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
        // Get articles of users the authenticated user follows
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
        $article->update($request->get('article'));

        // Update slug if title changed
        if ($request->has('article.title')) {
            $article->slug = Str::slug($request->get('article.title'));
            $article->save();
        }

        return new ArticleResource($article);
    }

    public function destroy($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $article->delete();

        return response()->json(['message' => 'Article deleted']);
    }

}
