<?php

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

/*
 * Endpoints:
 * - Employee:
 *   - GET /employees
 *   - POST /employees
 *   - GET /employees/{id}
 *   - DELETE /employees/{id}
 */

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;


// Route::resource('employees', EmployeeController::class);
// Since there are additional routes added by defining a resource route, we need to define them manually
Route::prefix('employees')->group(function (): void {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('/{id}', [EmployeeController::class, 'show'])->whereNumber('id');
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->whereNumber('id');
});
