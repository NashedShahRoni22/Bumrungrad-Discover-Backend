<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

// Route::controller(DoctorController::class)->middleware('auth:sanctum')->group(function () {
Route::controller(DoctorController::class)->group(function () {
    Route::post('test', 'index');

    // 1. specialty
    Route::get('get/specialty', 'get_specialty');
    Route::post('add/specialty', 'add_specialty');

    // 2. sub specialty
    Route::get('get/sub/specialty', 'get_sub_specialty');
    Route::post('add/sub/specialty', 'add_sub_specialty');
    Route::get('get/selected/sub/specialty/{id}', 'selected_sub_specialty');

    // 3. doctors
    Route::get('get/doctors', 'get_doctors');
    Route::post('add/doctor', 'add_doctor');
    Route::get('/search/doctor', 'search_doctor');
    Route::get('/search/doctor/{id}', 'find_doctor');

    // 4. package
    Route::get('/get/package/{id?}', 'get_packages');
    Route::post('/create/package', 'create_package');

    // 5. sub package
    Route::get('/get/sub/package/{id?}', 'get_sub_package');
    Route::post('/create/sub/package', 'create_sub_package');
    Route::get('/get/sub/packages/{id}', 'get_sub_packages');

    // 6. clinic and centers
    Route::get('/get/centers/{id?}', 'get_centers');
    Route::post('/add/center', 'add_center');
    Route::get('/delete/{center}/{id}', 'delete_record');

    // 7. air ticket
    Route::post('/add/air/ticket', 'air_ticket');
    Route::get('/get/air/ticket/{id?}', 'get_air_ticket');

    // 8. air pickup
    Route::post('/add/air/pickup', 'air_pickup');
    Route::get('/get/air/pickup/{id?}', 'get_air_pickup');

    // 9. air ambulance
    Route::post('/add/air/ambulance', 'air_ambulance');
    Route::get('/get/air/ambulance/{id?}', 'get_air_ambulance');

    // 10. order medicine
    Route::post('/add/order/medicine', 'order_medicine');
    Route::get('/get/order/medicine/{id?}', 'get_order_medicine');

    // 11. tele medicine
    Route::post('/add/tele/medicine', 'tele_medicines');
    Route::get('/get/tele/medicine/{id?}', 'get_tele_medicine');
    
    // 12. medical Report
    Route::post('/add/medical/report', 'medical_report');
    Route::get('/get/medical/report/{id?}', 'get_medical_report');
    
    // 13. medical Report
    Route::post('/add/doctor/appointment', 'doctor_appointment');
    Route::get('/get/doctor/appointments/{id?}', 'get_doctor_appointment');
    
    // 14. Question quary
    Route::post('/add/question', 'add_question');
    Route::get('/get/questions/{id?}', 'get_question');
    
    // 15. Health checkups
    Route::post('/add/health/check_up', 'add_health_checkup');
    Route::get('/get/health/check_up', 'get_health_checkup');
    Route::get('/get/health/check_up/{id}', 'get_health_checkup_by');
    
});
