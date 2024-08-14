    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\FirebaseNotificationController;
use App\Http\Controllers\PostController;
    use App\Http\Controllers\SanctumAuthController;
    use App\Http\Controllers\PushNotificationController;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "api" middleware group. Make something great!
    |
    */

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group([

        'middleware' => 'api',
        'prefix' => 'auth'

    ], function ($router) {

        Route::post('login', [AuthController::class,'login']);
        Route::post('register',[AuthController::class,'register']);
        Route::post('refresh', [AuthController::class,'refresh']);
        Route::get('me', [AuthController::class,'me']);
        Route::post('logout', [AuthController::class,'logout']);


    });

    Route::group([

        'middleware' => 'api',
        'prefix' => 'sanctum'

    ], function ($router) {

        Route::post('login', [SanctumAuthController::class,'login']);
        Route::post('register',[SanctumAuthController::class,'register']);
        Route::get('me', [SanctumAuthController::class,'me']);
        Route::post('logout', [SanctumAuthController::class,'logout']);
    });

    Route::group([

        'middleware' => 'api',
        'prefix' => 'firebase'

    ], function ($router) {

        Route::post('login', [FirebaseController::class,'login']);
        Route::post('register',[FirebaseController::class,'register']);
        Route::get('me', [FirebaseController::class,'me']);
        Route::middleware(['web'])->group(function() {
            Route::post('/auth/google', [FirebaseController::class, 'signInWithGoogle']);
            Route::post('/auth/phone', [FirebaseController::class, 'signInWithPhoneNumber']);
        });
    });

    //Post CRUD
    Route::middleware(['lang'])->controller(PostController::class)->group(function(){
        Route::get('/posts','index'); // Get All Posts
        Route::post('/posts','store')->middleware('lang'); //Creat Post
        Route::get('/posts/{id}','show'); // Get One Post
        Route::put('/posts/{id}','update'); // Update Post
        Route::delete('/posts/{id}','destroy'); // Delete Post
    });


    //notification

    Route::group(['prefix'=>'notification'],function(){
        Route::post('/send-topic-notification', [FirebaseNotificationController::class, 'sendToTopic']);
        Route::post('/send-device-notification', [FirebaseNotificationController::class, 'sentToSpecificDevice']);
    });
