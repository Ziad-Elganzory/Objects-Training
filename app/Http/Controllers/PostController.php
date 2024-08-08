<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Traits\ApiResponse;

/**
 * @OA\Info(title="My API", version="1.0"),
 *
 */

 /**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     title="Post",
 *     required={"title", "content"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the post"
 *     ),
 *     @OA\Property(
 *         property="content",
 *         type="string",
 *         description="Content of the post"
 *     )
 * )
 */

class PostController extends Controller
{
    use ApiResponse;

      /**
     * @OA\Get(
     *     path="/api/posts",
     *     operationId="index",
     *     tags={"Posts"},
     *     summary="Get list of posts",
     *     description="Returns list of posts",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found"
     *     )
     * )
     */

    // Get All Posts
    public function index()
    {
        try {
            $posts = Post::all();
            return $this->successResponse(PostResource::collection($posts),'Posts Retrived Successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve posts', 500,$e->getMessage());
        }
    }

   /**
     * @OA\Post(
     *     path="/api/posts",
     *     operationId="store",
     *     tags={"Posts"},
     *     summary="Create a new post",
     *     security={{"bearer": {}}},
     *     description="Creates a new post and returns the created post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="title",
     *                 type="string",
     *                 description="Title of the post"
     *             ),
     *             @OA\Property(
     *                 property="content",
     *                 type="string",
     *                 description="Content of the post"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */

    // Create Post
    public function store(StorePostRequest $request)
    {
        try {
            $post = Post::create($request->validated());

            return $this->successResponse(new PostResource($post),'Post Created Successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to create post', 500,$e->getMessage());
        }
    }

        /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     operationId="show",
     *     tags={"Posts"},
     *     summary="Get post by ID",
     *     description="Returns a single post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found"
     *     )
     * )
     */

    // Get One Post
    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);
            return $this->successResponse(new PostResource($post),'Post Retrived Successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Post not found', 404,$e->getMessage());
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve post', 500,$e->getMessage());
        }
    }

        /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     operationId="update",
     *     tags={"Posts"},
     *     summary="Update an existing post",
     *     security={{"bearer": {}}},
     *     description="Updates an existing post and returns the updated post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found"
     *     )
     * )
     */

    // Update Post
    public function update(UpdatePostRequest $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
            // var_dump($post);
            $post->update($request->validated());
            return $this->successResponse(new PostResource($post),'Post Updated Successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Post not found', 404,$e->getMessage());
        } catch (Exception $e) {
            return $this->errorResponse('Failed to update post', 500,$e->getMessage());
        }
    }

       /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     operationId="destroy",
     *     tags={"Posts"},
     *     summary="Delete a post",
     *     security={{"bearer": {}}},
     *     description="Deletes a post and returns no content",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found"
     *     )
     * )
     */

    // Delete Post
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();
            return $this->successResponse(PostResource::collection($post),'Post Deleted Successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Post not found', 404,$e->getMessage());
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete post', 500,$e->getMessage());
        }
    }
}
