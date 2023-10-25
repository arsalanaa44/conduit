<?php

namespace App\Http\Controllers\API;
use App\Models\Tag;
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
        $query = Article::query();

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        // Filter by author's username
        if ($request->has('author')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('username', $request->author);
            });
        }


        $query->orderBy('created_at', 'desc');

        $limit = $request->input('limit', 3);
        $query->take($limit);

        $offset = $request->input('offset', 0);
        $query->skip($offset);

        $articles = $query->get();
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

        $validatedData = $request->validate([
            'article.title' => 'required|string|max:255',
            'article.description' => 'required|string',
            'article.body' => 'required|string',
            'article.tagList' => 'nullable|array',
            'article.tagList.*' => 'string|max:255', // validates each item in the array
        ]);

        // Create the article

        $article = new Article($request->get('article'));
        $article->user_id = auth()->id();
        $article->slug = Str::slug($article->title);
        $article->save();
        // Process the tags
        $tags = $validatedData['article']['tagList'] ?? [];

        foreach ($tags as $tagName) {
            // Use the firstOrCreate method to get the tag or create it if it doesn't exist
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }

        // Attach tags to the article
        $article->tags()->sync($tagIds);

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
