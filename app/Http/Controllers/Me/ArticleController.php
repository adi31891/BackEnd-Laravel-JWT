<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\Article\StoreRequest;
use App\Http\Requests\Me\Article\UpdateRequest;
use Str;
use ImageKit\ImageKit;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use App\Models\Article;

class ArticleController extends Controller
{

    public function index()
    {
        $userId = auth()->id();


        $articles = Article::with(['category', 'user:id,name,email,picture'])->select([
            'id',
            'user_id',
            'category_id',
            'title',
            'slug',
            'content_preview',
            
            'featured_image',
            'created_at',
            'updated_at',
        ])
            ->where('user_id', $userId)
            ->paginate();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message'=> 'Article fetched succesfuly',

            ],
            'data' => $articles,
            ]);
        
        
    }




    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] =Str::of($validated['title'])->slug('-') . '-' . time();
        $validated['content_preview'] = substr($validated['content'], 0, 218) . '...';

        $imageKit = new ImageKit (
            env('IMAGEKIT_PUBLIC_KEY'),
            env('IMAGEKIT_PRIVATE_KEY'),
            env('IMAGEKIT_URL_ENDPOINT'),


        );

        $image = base64_encode(file_get_contents($request->file('featured_image')));

        $uploadImage = $imageKit->uploadFile([
            'file' => $image,
            'fileName' => $validated['slug'],
            'folder' => '/article',
        ]);
        

        $validated['featured_image'] = $uploadImage->result->url;

        $userId = auth()->id();

        $createArticle = user::find($userId)->articles()->create($validated);

        if ($createArticle)
        {
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Article created successfully',
                ],
                'data' => [],

            ]);
        }

        return response()->json([
            'meta' => [
                'code' => 500,
                'status' => 'error',
                'message' => 'Failed to create article',
            ],
            'data' => [],

            
        ], 500);


    }

    public function show($id)
    {
        $article = Article::with(['category', 'user:id,name,picture'])->find($id);

        if ($article)
        {
            $userId = auth()->id();

            if($article->user_id === $userId)
            {
                return response()->json([
                    'meta' => [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Article fetched successfully.',
                    ],
                    'data' => $article,
                ], 200);
                

            }

            return response()->json([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => ' Unauthorized.',
    
                ],
                'data' => []
            ], 401);

        }

        return response()->json([
            'meta' => [
                'code' => 404,
                'status' => 'error',
                'message' => 'Article not found.',

            ],
            'data' => []
        ], 404);

    }

    public function update(UpdateRequest $request, $id)
    {
        $article = Article::find($id);

        if ($article)
        {
            $userId = auth()->id();

            if ($article->user_id === $userId)
            {
                $validated = $request->validated();

                $validated['slug'] = Str::of($validated['title'])->slug('-') . '-' . time();
                $validated['content_preview'] = substr($validated['content'], 0 ,218) . '...';

                if ($request->hasFile('featured_imaged'))
                {
                    $imageKit = new ImageKit(
                        env('IMAGEKIT_PUBLIC_KEY'),
                        env('IMAGEKIT_PRIVATE_KEY'),
                        env('IMAGEKIT_URL_ENDPOINT'),
                    );

                    $image = base64_encode(file_get_contents($request->file('featured_image')));

                    $uploadimage = $imageKit->uploadFile([
                        'file' => $image,
                        'filename' => $validated('slug'),
                        'folder' => '/article',
                    ]);

                    $validated['featured_image'] = $uploadimage->result->url;
                }

                $updateArticle = $article->update($validated);

                if ($updateArticle)
                {
                    return response()->json([
                        'meta' => [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Article updated successfully.',

                        ],
                        'data' => [

                        ],
                    ]);
                }

                return response()->json([
                    'meta' => [
                        'code' => 500,
                        'status' => 'error',
                        'message' => 'Error! Article failed to update.',
                    ],
                    'data' => [],
                ], 500);
            }

            return response()->json([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Unauthorized.',

                ],
                'data' => [

                ],
            ], 401);
        }

        return response()->json([
            'meta' => [
                'code' => 404,
                'status' => 'error',
                'message' => 'Article not found.',
            ],
            'data'=>[],
        ], 404);
    }

    public function destroy($id)
    {
        $article = Article::find($id);

        if ($article)
        {
            $userId = auth()->id();

            if ($article->user_id === $userId)
            {
                $deleteArticle = $article->delete();

                if ($deleteArticle)
                {
                    return response()->json([
                        'meta' => [
                            'code' => 200,
                            'status' => 'success',
                            'message' => 'Article deleted successfully.',
                        ],
                        'data' => [],   
                    ], 200);
                }

                return response()->json([
                    'meta' => [
                        'code' => 500,
                        'status' => 'error',
                        'message' => 'Error! Article failed to delete.',
                    ],
                    'data' => [],
                ], 500);
            }

            return response()->json([
                'meta' => [
                        'code' => 401,
                        'status' => 'error',
                        'message' => 'Unaurhorized.',
                    ],
                    'data' => [],
            ], 401);
        }

        return response()->json([
            'meta' => [
                'code' => 404,
                'status' => 'error',
                'message' => 'Article not found.',
            ],
            'data' => [],
        ], 404);
    }
}
