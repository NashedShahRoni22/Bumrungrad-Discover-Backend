<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use DB;

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


Route::controller(DoctorController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('personal/appointment/{id}', 'personal_appointment');
});
Route::controller(DoctorController::class)->group(function () {
    // 1. specialty
    Route::get('get/specialty', 'get_specialty');
    Route::post('add/specialty', 'add_specialty');

    // 2. sub specialty
    Route::get('get/sub/specialty', 'get_sub_specialty');
    Route::post('add/sub/specialty', 'add_sub_specialty');
    Route::get('get/selected/sub/specialty/{id}', 'selected_sub_specialty');

    // 3. doctors
    Route::get('get/doctors', 'get_doctors')->name('get.doctor');
    Route::post('add/doctor', 'add_doctor');
    Route::get('/search/doctor', 'search_doctor');
    Route::get('/search/doctor/{slug}/{id}', 'find_doctor');

    // 4. package
    Route::get('/get/package/{slug?}/{id?}', 'get_packages')->name('get.package');
    Route::post('/create/package', 'create_package');
    Route::post('/update/package/{id}', 'update_package');

    // 5. sub package
    Route::get('/get/sub/package/{slug?}/{id?}', 'get_sub_package')->name('get.sub_package');
    Route::post('/create/sub/package', 'create_sub_package');
    Route::post('/update/sub/package/{id}', 'update_sub_package');
    Route::get('/get/sub/packages/{slug}/{id}', 'get_sub_packages');
    Route::get('/search/package/{name}', 'search_package');

    // 6. clinic and centers
    Route::get('/get/centers/{slug?}/{id?}', 'get_centers')->name('get.center');
    Route::post('/add/center', 'add_center');
    Route::post('/update/center/{id}', 'update_center');
    Route::get('/delete/{center}/{id}', 'delete_record');
    Route::get('/search/center/{name}', 'search_center');

    // 7. air ticket
    Route::get('/get/air/ticket/{id?}', 'get_air_ticket')->name('get.air_ticket');
    Route::post('/add/air/ticket', 'air_ticket');

    // 8. air pickup
    Route::get('/get/air/pickup/{id?}', 'get_air_pickup')->name('get.air_pickup');
    Route::post('/add/air/pickup', 'air_pickup');

    // 9. air ambulance
    Route::get('/get/air/ambulance/{id?}', 'get_air_ambulance')->name('get.air_ambulance');
    Route::post('/add/air/ambulance', 'air_ambulance');

    // 10. order medicine
    Route::get('/get/order/medicine/{id?}', 'get_order_medicine')->name('get.order_medicine');
    Route::post('/add/order/medicine', 'order_medicine');

    // 11. tele medicine
    Route::get('/get/tele/medicine/{id?}', 'get_tele_medicine')->name('get.tele_medicine');
    Route::post('/add/tele/medicine', 'tele_medicines');
    
    // 12. medical Report
    Route::get('/get/medical/report/{id?}', 'get_medical_report')->name('get.medical_record');
    Route::post('/add/medical/report', 'medical_report');
    
    // 13. medical Report
    Route::get('/get/doctor/appointments/{id?}', 'get_doctor_appointment')->name('get.doctor_appointment');
    Route::post('/add/doctor/appointment', 'doctor_appointment');
    
    // 14. Question quary
    Route::post('/add/question', 'add_question');
    Route::get('/get/questions/{id?}', 'get_question')->name('get.query');
    
    // 15. Health checkups
    Route::get('/get/health/check_up', 'get_health_checkup')->name('get.health_checkup');
    Route::post('/add/health/check_up', 'add_health_checkup');
    Route::get('/get/health/check_up/{id}', 'get_health_checkup_by');
    
    // 16. package booking
    Route::get('get/package_booking/{id?}', 'get_package_booking')->name('get.health_package');
    Route::post('/add/package/booking', 'add_package_booking');
    
    // 17. users data
    Route::get('get/users/{id?}', 'get_users')->name('get.patient');
    Route::get('review/appointment/{id}', 'review_appointment');
    Route::get('appointment/success/{id}', 'appointment_success');
    
    // 18. visa processing
    Route::post('/add/visa/precessing', 'add_visa_processing')->name('get.visa');
    Route::get('/get/visa/precessing/{id?}', 'get_visa_processing');
    
    // 19. news
    Route::post('/add/news', 'add_news');
    Route::get('/get/news/{id?}', 'get_news');
    
    // 20. blog
    Route::post('/add/blogs', 'add_blog');
    Route::get('/get/blogs/{id?}', 'get_blog');
    
    // 21. count of all categories
    Route::get('get/category/length', 'category_length');
    
    // 21. must remove later
    Route::get('send_mail/', 'send_mail');
    Route::get('test', function(){
        foreach(DB::table('sub_specialties')->get() as $sub_specialty){
            $new = str_replace('&', 'and', $sub_specialty->specialty);
            echo $new.'<br>';
            DB::table('sub_specialties')->where('id', $sub_specialty->id)->update(['specialty'=>$new]);
            // echo $sub_specialty->sub_specialty.'<br>';
        }
        
    });
});
