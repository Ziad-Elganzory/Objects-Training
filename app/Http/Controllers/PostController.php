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
use Kreait\Firebase\Contract\Database;
use PhpParser\Node\Expr\Cast\String_;

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
    protected $database;
    protected $tableName;


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

     public function __construct(Database $database)
     {
         $this->database = $database;
         $this->tableName = 'posts';
     }

    // Get All Posts
    public function index()
    {
        try {

            $postRef = $this->database->getReference($this->tableName)->getValue();
            $posts = Post::all();
            return $this->successResponse(['Sql'=>PostResource::collection($posts),'FireBase'=>$postRef],__('messages.posts.success.posts_retrived'));
        } catch (Exception $e) {
            return $this->errorResponse(__('messages.posts.fail.posts_retrived'), 500,$e->getMessage());
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
            $postData = $request->validated();
            $post = Post::create($postData);
            $postRef = $this->database->getReference($this->tableName)->push($postData)->getValue();
            return $this->successResponse(['Sql'=>new PostResource($post),'FireBase'=>$postRef],__('messages.posts.success.post_created'));
        } catch (Exception $e) {
            return $this->errorResponse(__('messages.posts.fail.post_created'), 500,$e->getMessage());
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
     *         @OA\Schema(type="")
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
            if(gettype($id) == 'string'){
                $editData = $this->database->getReference($this->tableName)->getChild($id)->getValue();
                return $this->successResponse(['FireBase'=>$editData],__('messages.posts.success.post_retrived'));
            } elseif(gettype($id)=='integer') {
                $post = Post::findOrFail($id);
                return $this->successResponse(['Sql'=>new PostResource($post)],__('messages.posts.success.post_retrived'));
            }
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Post not found', 404,$e->getMessage());
        } catch (Exception $e) {
            return $this->errorResponse(__('messages.posts.fail.post_retrived'), 500,$e->getMessage());
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
     *         @OA\Schema(type="string")
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
    public function update(UpdatePostRequest $request,String $id)
    {
        try {
            $postData = $request->validated();
            // $post = Post::findOrFail($id);
            // $post->update($postData);
            $this->database->getReference($this->tableName.'/'.$id)->update($postData);
            return $this->successResponse(/*new PostResource($post)*/$postData,__('messages.posts.success.post_update'));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Post not found', 404,$e->getMessage());
        } catch (Exception $e) {
            return $this->errorResponse(__('messages.posts.fail.post_update'), 500,$e->getMessage());
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
     *         @OA\Schema(type="")
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
            $post = $this->database->getReference($this->tableName.'/'.$id);
            $postValue = $post->getValue();
            $post->remove();
            // $post = Post::findOrFail($id);
            // $post->delete();
            return $this->successResponse(/*PostResource::collection($post)*/["FireBase"=>$postValue],__('messages.posts.success.post_delete'));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Post not found', 404,$e->getMessage());
        } catch (Exception $e) {
            return $this->errorResponse(__('messages.posts.fail.post_delete'), 500,$e->getMessage());
        }
    }
}
