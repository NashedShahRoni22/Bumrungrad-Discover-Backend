<?php

namespace App\Http\Controllers;

use App\Http\Requests\CenterRequest;
use App\Http\Requests\DoctorRequest;
use App\Http\Requests\PackageRequest;
use App\Http\Requests\SubPackageRequest;
use App\Models\Center;
use App\Models\News;
use App\Models\Blogs;
use App\Models\VisaProcessing;
use App\Models\User;
use App\Models\HealthCheckUp;
use App\Models\Doctor;
use App\Models\Package;
use App\Models\Specialty;
use App\Models\SubPackage;
use App\Models\SubSpecialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers;
use App\Models\AirAmbulance;
use App\Models\AirPickup;
use App\Models\AirTicket;
use App\Models\OrderMedicine;
use App\Models\TeleMedicine;
use App\Models\Medicalreport;
use App\Models\Doctorappoinment;
use App\Models\Question;
use App\Models\PackageBooking;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;


class DoctorController extends Controller
{
    /*--------------------------------------------------------------------------------------------------*/
    //                                       reuseable functions
    /*--------------------------------------------------------------------------------------------------*/
    // 1. modify doctor data
    public function makeJson($array)
    {
        foreach ($array as $key => $items) {
            $arr = [];
            for ($i = 0; $i < count($items); $i++) {
                $arr[$key . $i] = $items[$i];
            }
            if (count($arr) > 0) {
                $collection[$key] = json_encode($arr);
            } else {
                $collection[$key] = NULL;
            }
        }
        return $collection;
    }

    // 2. upload images to server
    public function upload_file($file, $path, $type)
    {
        $url = NULL;
        if ($file->hasFile($type)) {
            $doc = $file->$type;
            $extension = $file->file($type)->getClientOriginalExtension();
            $doc_name = $type . '-' . time() . Str::random(10) . '.' . $extension;
            $doc->move(public_path($path), $doc_name);
            $url = asset('public/' . $path . $doc_name);
        }
        return $url;
    }

    // 3. make array to json for doctor table
    public function sendJson($col, $string, $old)
    {
        $json_field = [];
        for ($i = 0; $i < 100; $i++) {
            $field = $col . '->' . $col . $i;
            if (Doctor::where($field, $string)->exists()) {
                array_push($json_field, $field);
            }
        }
        
        if (count($json_field) > 0) {
            foreach($json_field as $key => $field){
                if($key + 1 ==1){
                    $old = $old->where($field, $string);
                }else{
                    $old = $old->orWhere($field, $string);
                }
            }
        } else {
            $old = $old->where($col, $string);
        }
        return $old;
    }

    /*--------------------------------------------------------------------------------------------------*/
    //                                       api functions
    /*--------------------------------------------------------------------------------------------------*/
    // 1. add specialty
    public function add_specialty(Request $request)
    {
        $validation = Validator::make($request->all(), ['name' => 'required']);

        if ($validation->fails()) {
            return json_encode(array('validationError' => $validation->getMessageBag()->toArray()));
        } else {
            $check = Specialty::where('name', $request->name)->first();
            if ($check != '') {
                $data = ['status' => 404, 'msg' => $request->name . ' is available.'];
                return response()->json(['response' => $data]);
            }
            $add = Specialty::insert(['name' => $request->name]);
            if ($add) {
                $data = ['status' => 200, 'msg' => 'Specialty added.'];
            } else {
                $data = ['status' => 404, 'msg' => 'Something wrong.'];
            }
            return response()->json(['response' => $data]);
        }
    }

    // 2. add sub specialty
    public function add_sub_specialty(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'sub_specialty' => 'required', 'specialty' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 404, 'msg' => 'Validation error.', 'err' => $validation->errors()]);
        } else {
            $check = SubSpecialty::where('specialty', $request->specialty)->where('sub_specialty', $request->sub_specialty)->exists();
            if ($check) {
                $data = ['status' => 404, 'msg' => $request->sub_specialty . ' already exists.'];
                return response()->json(['response' => $data]);
            }
            $add = SubSpecialty::insert(['specialty' => $request->specialty, 'sub_specialty' => $request->sub_specialty]);
            if ($add) {
                $data = ['status' => 200, 'msg' => 'Sub specialty added.'];
            } else {
                $data = ['status' => 404, 'msg' => 'Something wrong.'];
            }
            return response()->json(['response' => $data]);
        }
    }

    // 3. get specialty
    public function get_specialty()
    {
        $specialty = Specialty::orderBy('name', 'ASC')->get();
        if ($specialty->count() > 0) {
            $data = ['data' => $specialty, 'status' => 200];
        } else {
            $data = ['status' => 404, 'msg' => 'Data not found.'];
        }
        return response()->json(['response' => $data]);
    }

    // 4. get sub specialty
    public function get_sub_specialty()
    {
        $sub = SubSpecialty::orderBy('sub_specialty', 'ASC')->get();
        if ($sub->count() > 0) {
            $data = ['data' => $sub, 'status' => 200];
        } else {
            $data = ['status' => 404, 'msg' => 'Data not found.'];
        }
        return response()->json(['response' => $data]);
    }

    // 5. get selected sub specialty
    public function selected_sub_specialty($specialty)
    {
        $selected = SubSpecialty::where('specialty', $specialty)->get();
        if ($selected->count() > 0) {
            $data = ['data' => $selected, 'status' => 200];
        } else {
            $data = ['status' => 404, 'msg' => 'Data not found.'];
        }
        return response()->json(['response' => $data]);
    }

    // 6. get all doctors
    public function get_doctors()
    {
        $doctors = Doctor::get();
        if ($doctors->count() > 0) {
            $doctors->each(function ($item) {
                $item->specialty = Specialty::where('name', $item->specialty)->value('name');
                
                $item->certificates = json_decode($item->certificates);
                $item->fellowships = json_decode($item->fellowships);
                $item->experiences = json_decode($item->experiences);
                $item->researches = json_decode($item->researches);
                $item->interests = json_decode($item->interests);
                $item->article = json_decode($item->article);
                $item->trainings = json_decode($item->trainings);
                $item->schools = json_decode($item->schools);
                
                $item->sub_specialty = $item->sub_specialty ? array_values((array) json_decode($item->sub_specialty)) : NULL;
                $item->lang = array_values((array) json_decode($item->lang));
                $item->day = array_values((array) json_decode($item->day));
                $item->arrival = array_values((array) json_decode($item->arrival));
                $item->leave = array_values((array) json_decode($item->leave));
                $item->location = array_values((array) json_decode($item->location));
                $item->shift = array_values((array) json_decode($item->shift));
            });
            
            $data = ['arr' => '$arr', 'data' => $doctors, 'status' => 200];
        } else {
            $data = ['status' => 404, 'msg' => 'Data not found.'];
        }
        return response()->json(['response' => $data]);
    }

    // 7. add new doctor
    public function add_doctor(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'lang' => 'required',
            'schools' => 'required',
            'gender' => 'required',
            'specialty' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 404, 'err' => $validation->errors()]);
        }

        $path = 'assets/images/doctors/';
        $image = $this->upload_file($request, $path, 'cover_photo');

        $schedule = json_decode($request->schedule);
        $day = $shift = $arrival = $leave = $location = [];
        for ($i = 0; $i < count($schedule); $i++) {
            for ($j = 0; $j < count($schedule[$i]); $j++) {
                if ($j == 0) {
                    array_push($day, $schedule[$i][$j]);
                } elseif ($j == 1) {
                    array_push($shift, $schedule[$i][$j]);
                } elseif ($j == 2) {
                    array_push($arrival, $schedule[$i][$j]);
                } elseif ($j == 3) {
                    array_push($leave, $schedule[$i][$j]);
                } elseif ($j == 4) {
                    array_push($location, $schedule[$i][$j]);
                }
            }
        }

        
        $modify['day'] = $day;
        $modify['shift'] = $shift;
        $modify['arrival'] = $arrival;
        $modify['leave'] = $leave;
        $modify['location'] = $location;
        $modify['lang'] = explode(',', $request->lang);
        
        if($request->sub_specialty){
            $modify['sub_specialty'] = explode(',', $request->sub_specialty);
        }

        $json =  $this->makeJson($modify);
        
        if (!array_key_exists('sub_specialty',$json)){
            $json['sub_specialty'] = NULL;
        }
        


        $doctor = new Doctor();
        $doctor->name = $request->name;
        $doctor->slug = Str::slug($request->name, '-');
        $doctor->cover_photo = $image;
        $doctor->specialty = $request->specialty;
        $doctor->sub_specialty = $json['sub_specialty'];
        $doctor->lang = $json['lang'];
        $doctor->gender = $request->gender;
        $doctor->schools = $request->schools;
        $doctor->certificates = $request->certificates;
        $doctor->fellowships = $request->fellowships;
        $doctor->interests = $request->interests;
        $doctor->experiences = $request->experiences;
        $doctor->researches = $request->researches;
        $doctor->article = $request->article;
        $doctor->trainings = $request->trainings;
        $doctor->day = $json['day'];
        $doctor->location = $json['location'];
        $doctor->arrival = $json['arrival'];
        $doctor->leave = $json['leave'];
        $doctor->shift = $json['shift'];
        $doctor->save();

        $data = ['status' => 200, 'msg' => 'Doctor added.'];
        return response()->json(['response' => $data]);
    }

    // 8. search doctor
    public function search_doctor(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'school' => 'missing',
            'certificates' => 'missing',
            'fellowships' => 'missing',
            'interests' => 'missing',
            'experiences' => 'missing',
            'researches' => 'missing',
            'article' => 'missing',
        ]);

        if ($validation->fails()) {
            return response()->json(['err' => 'Something went wrong.']);
        }

        $search = DB::table('doctors');
        foreach ($request->all() as $key => $query) {
            if ($query != '') {
                if ($key == 'sub_specialty') {
                    $search = $this->sendJson($key, $query, $search);
                } elseif ($key == 'lang') {
                    $search = $this->sendJson($key, $query, $search);
                } elseif ($key == 'day') {
                    $search = $this->sendJson($key, $query, $search);
                } elseif ($key == 'shift') {
                    $search = $this->sendJson($key, $query, $search);
                } elseif ($key == 'location') {
                    $search = $this->sendJson($key, $query, $search);
                } else {
                    if($key == 'name'){
                        $search = $search->where($key, 'LIKE', "%$query%");
                    }else{
                        $search = $search->where($key, $query);
                    }
                }
            }
        }
        
        

        $doctors = $search->get();
        $response = ['status' => 404, 'msg' => 'Data not found.', 'query' => $request->all()];
        if ($doctors->count() > 0) {
            $doctors->each(function ($item) {
                $item->specialty = Specialty::where('name', $item->specialty)->value('name');
                $item->lang = array_values((array) json_decode($item->lang));
                $item->sub_specialty = array_values((array) json_decode($item->sub_specialty));
                $item->schools = json_decode($item->schools);
                $item->certificates = json_decode($item->certificates);
                $item->fellowships = json_decode($item->fellowships);
                $item->experiences = json_decode($item->experiences);
                $item->researches = json_decode($item->researches);
                $item->interests = json_decode($item->interests);
                $item->article = json_decode($item->article);
                $item->trainings = json_decode($item->trainings);
                $item->day = array_values((array) json_decode($item->day));
                $item->arrival = array_values((array) json_decode($item->arrival));
                $item->leave = array_values((array) json_decode($item->leave));
                $item->location = array_values((array) json_decode($item->location));
                $item->shift = array_values((array) json_decode($item->shift));
            });
        }else{
            $doctor = 0;
        }
        $response = ['status' => 200, 'data' => $doctors, 'query' => $request->all()];
        return response()->json($response);
    }

    // 9. search single doctor
    public function find_doctor($slug, $id)
    {
        $res = ['status' => 404, 'msg' => 'Data not found.'];
        if ($id > 0) {
            $doctor = Doctor::where('id', $id)->first();

            if ($doctor != '') {
                $doctor->specialty = Specialty::where('name', $doctor->specialty)->value('name');
                $doctor->lang = array_values((array) json_decode($doctor->lang));
                $doctor->sub_specialty = array_values((array) json_decode($doctor->sub_specialty));
                $doctor->schools = json_decode($doctor->schools);
                $doctor->certificates = json_decode($doctor->certificates);
                $doctor->fellowships = json_decode($doctor->fellowships);
                $doctor->experiences = json_decode($doctor->experiences);
                $doctor->researches = json_decode($doctor->researches);
                $doctor->interests = json_decode($doctor->interests);
                $doctor->article = json_decode($doctor->article);
                $doctor->trainings = json_decode($doctor->trainings);
                $doctor->day = array_values((array) json_decode($doctor->day));
                $doctor->arrival = array_values((array) json_decode($doctor->arrival));
                $doctor->leave = array_values((array) json_decode($doctor->leave));
                $doctor->location = array_values((array) json_decode($doctor->location));
                $doctor->shift = array_values((array) json_decode($doctor->shift));
                $res = ['status' => 200, 'data' => $doctor];
            }
        }
        return response()->json(['response' => $res]);
    }

    // 10. create new parent package
    public function create_package(PackageRequest $request)
    {
        $path = 'assets/images/packages/';
        $image = $this->upload_file($request, $path, 'cover_photo');

        // insert new package
        $data = [
            'title' => $request->title,
            'cover_photo' => $image,
            'description' => $request->description,
        ];
        $add = Package::insert($data);
        if ($add) {
            return response()->json(['msg' => 'Package added.']);
        }
    }
    
    public function update_package(Request $request, $id)
    {
        if($request->cover_photo){
            $path = 'assets/images/packages/';
            $image = $this->upload_file($request, $path, 'cover_photo');
            $data['cover_photo'] = $image;
        }

        // update package
        $data['title'] = $request->title;
        $data['description'] = $request->description;
        
        $update = Package::where('id', $id)->update($data);
        if ($update) {
            return response()->json(['msg' => 'Package updated.']);
        }
    }

    // 11. get all parent packages
    public function get_packages($slug = '', $id='')
    {
        if ($id != '') {
            $package = Package::where('id', $id)->first(); 
            if ($package) {
                $data = ['status' => 200, 'data' => $package];
            }
        } else {
            $package = Package::get();
            if ($package->count() > 0) {
                $package->each(function ($item) {
                    $item->slug = Str::slug($item->title);
                });
                $data = ['status' => 200, 'data' => $package];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }

    // 12. create sub packages
    public function create_sub_package(Request $request)
    {
        $path = 'assets/images/sub_packages/';
        $image = $this->upload_file($request, $path, 'cover_photo');
        
        // insert new package
        $data = $request->all();
        $data['cover_photo'] = $image;
        
        
        // return response()->json(['test' => $data]);
        SubPackage::insert($data);
        return response()->json(['status' => 200, 'msg' => "Child package added."]);
    
        
    }
    public function update_sub_package(Request $request, $id)
    {
        if($request->cover_photo){
            $path = 'assets/images/sub_packages/';
            $image = $this->upload_file($request, $path, 'cover_photo');
            $data['cover_photo'] = $image;
        }
        
        // insert new package
        $data = $request->all();
        unset($data['cover_photo']);
        
        
        SubPackage::where('id', $id)->update($data);
        return response()->json(['status' => 200, 'msg' => "Child package updated."]);
    }

    // 13. get sub packages
    public function get_sub_package($slug = '', $id = '')
    {
        if ($id != '') {
            $sub_package = SubPackage::where('id', $id)->first();
            if ($sub_package) {
                $sub_package->conditions = json_decode($sub_package->conditions);
                $sub_package->inclusions = json_decode($sub_package->inclusions);
                $sub_package->exclusions = json_decode($sub_package->exclusions);
                $data = ['status' => 200, 'data' => $sub_package];
            }
        } else {
            $sub_package = SubPackage::get();

            if ($sub_package->count() > 0) {
                $sub_package->each(function ($item) {
                    $item->slug = Str::slug($item->title);
                    $item->conditions = json_decode($item->conditions);
                    $item->inclusions = json_decode($item->inclusions);
                    $item->exclusions = json_decode($item->exclusions);
                });
                $data = ['status' => 200, 'data' => $sub_package];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }

    // 14. sub package by id
    public function sub_package_details($id)
    {
        $sub_package = SubPackage::where('id', $id)->first();
        if ($sub_package != '') {
            $data = ['status' => '200', 'data' => $sub_package];
        } else {
            $data = ['status' => '404', 'msg' => 'Data not found.'];
        }

        return response()->json($data);
    }

    // 15. add clinic
    public function add_center(Request $request)
    {
        $path = 'assets/images/centers/';
        $image = $this->upload_file($request, $path, 'cover_photo');

        $data['name'] = $request->name;
        $data['cover_photo'] = $image;
        $data['location'] = $request->location;
        $data['description'] = $request->description;
        $data['informations'] = $request->informations;
        $data['conditions'] = $request->conditions;
        $data['treatments'] = $request->treatments;

        Center::insert($data);
        return response()->json(['status' => 200, 'msg' => 'Clinic added.']);
    }
    
    public function update_center(Request $request, $id)
    {
        if($request->cover_photo){
            $path = 'assets/images/centers/';
            $image = $this->upload_file($request, $path, 'cover_photo');
            $data['cover_photo'] = $image;
        }

        $data['name'] = $request->name;
        $data['location'] = $request->location;
        $data['description'] = $request->description;
        $data['informations'] = $request->informations;
        $data['conditions'] = $request->conditions;
        $data['treatments'] = $request->treatments;

        Center::where('id', $id)->update($data);
        return response()->json(['status' => 200, 'msg' => 'Clinic updated.']);
    }

    // 16. get clinics
    public function get_centers($slug = '', $id = '')
    {
        if($id == ''){
            $centers = Center::get();
            $res = ['status' => 404, 'msg' => 'Data not found.'];
            if ($centers->count() > 0) {
                $centers->each(function ($item){
                    $item->slug = Str::slug($item->name);
                    $item->informations = json_decode($item->informations);
                    $item->conditions = json_decode($item->conditions);
                    $item->treatments = json_decode($item->treatments);
                });
                $res = ['data' => $centers, 'status' => 200];
            }
        }else{
            $res = ['status' => 200, 'msg' => 'Data not found'];
            if($id > 0){
                $center = Center::where('id', $id)->first();
                if ($center != '') {
                    
                    $center->informations = json_decode($center->informations);
                    $center->conditions = json_decode($center->conditions);
                    $center->treatments = json_decode($center->treatments);
                    
                    $res = ['data' => $center, 'status' => 200];
                }
            }
        }

        return response()->json(['response' => $res]);
    }

    // 17. air ticket
    public function air_ticket(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'booking_date' => 'required',
        ]);
        if ($validation->fails()) {
            $data = ['status' => 404, 'mgs' => 'Validation error.', $validation->errors()];
            return response()->json($data);
        }

        $path = public_path('assets/docs/air_ticket/');
        $doc = $this->upload_file($request, $path, 'doc');

        $data['booking_date'] = $request->booking_date;
        $data['country'] = $request->country;
        $data['doc'] = $doc;
        $data['destination'] = $request->destination;
        $data['return_date'] = $request->return_date;

        AirTicket::insert($data);

        $res = ['status' => 200, 'msg' => 'Ticket created.'];
        return response()->json($res);
    }

    // 18. get all air tickets
    public function get_air_ticket($id = '')
    {
        if ($id != '') {
            $air_tickets = AirTicket::where('id', $id)->first();
            if ($air_tickets) {
                $data = ['status' => 200, 'data' => $air_tickets];
            }
        } else {
            $air_tickets = AirTicket::get();
            if ($air_tickets->count() > 0) {
                $data = ['status' => 200, 'data' => $air_tickets];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }

    // 19. air pickup
    public function air_pickup(Request $request)
    {
        $path = 'assets/docs/air_pickup/';
        $appointment = $this->upload_file($request, $path, 'appointment');
        $air_ticket = $this->upload_file($request, $path, 'air_ticket');

        $data['appointment'] = $appointment;
        $data['air_ticket'] = $air_ticket;
        $data['passenger'] = $request->passenger;

        AirPickup::insert($data);

        $res = ['status' => 200, 'msg' => 'Airpickup created.'];
        return response()->json($res);
    }

    // 20. get all air tickets
    public function get_air_pickup($id = '')
    {
        if ($id != '') {
            $air_pickup = AirPickup::where('id', $id)->first();
            if ($air_pickup) {
                $data = ['status' => 200, 'data' => $air_pickup];
            }
        } else {
            $air_pickup = AirPickup::get();
            if ($air_pickup->count() > 0) {
                $data = ['status' => 200, 'data' => $air_pickup];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }

    // 21. air ambulance
    public function air_ambulance(Request $request)
    {
        $path = 'assets/docs/air_ambulance/';
        $passport_copy = $this->upload_file($request, $path, 'passport_copy');

        $data['entry_date'] = $request->entry_date;
        $data['passport_copy'] = $passport_copy;
        $data['summary'] = $request->summary;
        $data['description'] = $request->description;

        AirAmbulance::insert($data);

        $res = ['status' => 200, 'msg' => 'Air ambulance created.'];
        return response()->json($res);
    }

    // 22. get all air ambulance
    public function get_air_ambulance($id = '')
    {
        if ($id != '') {
            $air_ambulance = AirAmbulance::where('id', $id)->first();
            if ($air_ambulance) {
                $data = ['status' => 200, 'data' => $air_ambulance];
            }
        } else {
            $air_ambulance = AirAmbulance::get();
            if ($air_ambulance->count() > 0) {
                $data = ['status' => 200, 'data' => $air_ambulance];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }

    // 23. order medicine
    public function order_medicine(Request $request)
    {
        $data = $request->all();
        $medicines = json_decode($request->medicines);
        $medicine = $quantity = [];
        for ($i = 0; $i < count($medicines); $i++) {
            for ($j = 0; $j < count($medicines[$i]); $j++) {

                if ($j % 2 == 0) {
                    array_push($medicine, $medicines[$i][$j]);
                } else {
                    array_push($quantity, $medicines[$i][$j]);
                }
            }
        }

        $modify = [
            'medicines' => $medicine,
            'quantity' => $quantity,
        ];

        $json =  $this->makeJson($modify);

        $path = 'assets/docs/order_medicine/';
        $prescription = $this->upload_file($request, $path, 'prescription');

        $data['medicines'] = $json['medicines'];
        $data['prescription'] = $prescription;
        $data['quantity'] = $json['quantity'];
        
        

        OrderMedicine::insert($data);

        $res = ['status' => 200, 'msg' => 'Order medicine created.'];
        return response()->json($res);
    }

    // 24. get all tele medicines
    public function get_order_medicine($id = '')
    {
        if ($id != '') {
            $order_medicine = OrderMedicine::where('id', $id)->first();
            if ($order_medicine) {
                $order_medicine->medicines = array_values((array) json_decode($order_medicine->medicines));
                $order_medicine->quantity = array_values((array) json_decode($order_medicine->quantity));
                
                // $order_medicine->medicines = json_decode($order_medicine->medicines);
                // $order_medicine->quantity = json_decode($order_medicine->quantity);
                $data = ['status' => 200, 'data' => $order_medicine];
            }
        } else {
            $order_medicine = OrderMedicine::get();
            if ($order_medicine->count() > 0) {
                $order_medicine->each(function ($item) {
                    
                     $item->medicines = array_values((array) json_decode($item->medicines));
                     $item->quantity = array_values((array) json_decode($item->quantity));
                    // $item->medicines = json_decode($item->medicines);
                    // $item->quantity = json_decode($item->quantity);
                });
                $data = ['status' => 200, 'data' => $order_medicine];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }

    // 25. tele medicines
    public function tele_medicines(Request $request)
    {
        $path = 'assets/docs/tele_medicine/';
        $investigationDocument = $this->upload_file($request, $path, 'investigationDocument');

        $data = $request->all();
        $data['investigationDocument'] = $investigationDocument;

        TeleMedicine::insert($data);
        $res = ['status' => 200, 'msg' => 'Tele medicine created.'];
        return response()->json($res);
    }

    // 26. get all tele medicines
    public function get_tele_medicine($id = '')
    {
        if ($id != '') {
            $tele_medicine = TeleMedicine::where('id', $id)->first();
            if ($tele_medicine) {
                $data = ['status' => 200, 'data' => $tele_medicine];
            }
        } else {
            $tele_medicine = TeleMedicine::get();
            if ($tele_medicine->count() > 0) {
                $data = ['status' => 200, 'data' => $tele_medicine];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }
    
    // 27. post medical report
    public function medical_report(Request $request)
    {
        $path = 'assets/docs/medicalreport/';
        $investigationDocument = $this->upload_file($request, $path, 'passport');

        $data = $request->all();
        $data['passport'] = $investigationDocument;

        Medicalreport::insert($data);
        $res = ['status' => 200, 'msg' => 'Medical report created.'];
        return response()->json($res);
    }
    
    // 28. get all medical report
    public function get_medical_report($id = '')
    {
        if ($id != '') {
            $medicalreport = Medicalreport::where('id', $id)->first();
            if ($medicalreport) {
                $data = ['status' => 200, 'data' => $medicalreport];
            }
        } else {
            $medicalreport = Medicalreport::get();
            if ($medicalreport->count() > 0) {
                $data = ['status' => 200, 'data' => $medicalreport];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }
    
    // 29. Doctor appointment add
    public function doctor_appointment(Request $request)
    {
        $path = 'assets/docs/doctorappointment/';
        $investigationDocument = $this->upload_file($request, $path, 'passport');
        $investigationDocument1 = $this->upload_file($request, $path, 'medicalReport1');
        $investigationDocument2 = $this->upload_file($request, $path, 'medicalReport2');
        $investigationDocument3 = $this->upload_file($request, $path, 'medicalReport3');

        $data = $request->all();
        $data['passport'] = $investigationDocument;
        $data['medicalReport1'] = $investigationDocument1;
        $data['medicalReport2'] = $investigationDocument2;
        $data['medicalReport3'] = $investigationDocument3;

        Doctorappoinment::insert($data);
        
        $this->send_mail($data);
        
        $res = ['status' => 200, 'msg' => 'Doctor appoinment created.'];
        return response()->json($res);
    }
    
    // 29. get Doctor appointment
    public function get_doctor_appointment($id = '')
    {
        if ($id != '') {
            $doctorappoinment = Doctorappoinment::where('id', $id)->first();
            if ($doctorappoinment) {
                $data = ['status' => 200, 'data' => $doctorappoinment];
            }
        } else {
            $doctorappoinment = Doctorappoinment::get();
            if ($doctorappoinment->count() > 0) {
                $data = ['status' => 200, 'data' => $doctorappoinment];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }
    
    // 30. delete center
    public function delete_center($id)
    {
        if($id != '' && $id > 0){
            $delete = Center::where('id', $id)->delete();
            if($delete){
                $data = ['status' => 200, 'msg' => 'Center deleted'];
                return response()->json($data);
            }
        }
    }
    
    // 31. Question quary add
    public function add_question(Request $request)
    {
        $data = $request->all();
        Question::insert($data);
        $res = ['status' => 200, 'msg' => 'Question created.'];
        return response()->json($res);
    }
    
    // 32. get Question quary
    public function get_question($id='')
    {
        if ($id != '') {
            $question = Question::where('id', $id)->first(); 
            
            if ($question) {
                $data = ['status' => 200, 'data' => $question];
            }
        } else {
            $question = Question::get();
            if ($question->count() > 0) {
                $data = ['status' => 200, 'data' => $question];
            }
        }
        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }
    
    // 33. get child package by parent
    public function get_sub_packages ($slug, $id)
    {
        $data = ['status' => 404, 'msg' => 'Data not found'];
        if($id != '' && $id > 0){
            $sub_package = SubPackage::where('parent_id', $id)->get();
            if($sub_package->count() > 0){
                $sub_package->each(function ($item){
                    $item->slug = Str::slug($item->title);
                    $item->conditions = array_values((array) json_decode($item->conditions));
                    $item->exclusions = array_values((array) json_decode($item->exclusions));
                    $item->inclusions = array_values((array) json_decode($item->inclusions));
                });
                $data = ['status' => 200, 'data' => $sub_package];
            }
        }
        return response()->json($data);
    }
    
    // 34. delete record
    public function delete_record($param, $id)
    {
        $data = ['status' => 200, 'msg' => 'Data not found.'];
        if($param != '' && ($id != '' && $id > 0)){
            $query = DB::table($param)->where('id', $id);
            
            if($query->exists()){
                $delete = $query->delete();
                if($delete){
                    if($param == 'packages'){
                        DB::table('sub_packages')->where('parent_id', $id)->delete();
                    }
                    $data = ['status' => 200, 'msg' => 'Record deleted'];
                }
            }
            return response()->json($data);
        }
    }
    
    // 35. healty check up
    public function add_health_checkup(Request $request)
    {
        $data = $request->all();
        HealthCheckUp::insert($data);
        $res = ['status' => 200, 'msg' => 'Health checkup created.'];
        return response()->json($res);
    }
    
    // 36. get healty check up
    public function get_health_checkup()
    {
        $res = ['status' => 404, 'msg' => 'Data not found.'];
        
        $checkup = HealthCheckUp::get();
        if($checkup->count() > 0){
            $res = ['status' => 200, 'data' => $checkup];
        }
        return response()->json($res);
    }
    
    // 37. get healty check up by id
    public function get_health_checkup_by($id)
    {
        $res = ['status' => 404, 'msg' => 'Data not found.'];
        
        $checkup = HealthCheckUp::where('id', $id)->first();
        if($checkup->count() > 0){
            $res = ['status' => 200, 'data' => $checkup];
        }
        return response()->json($res);
    }
    
    // 38. get users
    public function get_users($id = '')
    {
        if ($id != '') {
            $users = User::where('id', $id)->first();
            if ($users) {
                $data = ['status' => 200, 'data' => $users];
            }
        } else {
            $users = User::get();
            if ($users->count() > 0) {
                $data = ['status' => 200, 'data' => $users];
            }
        }

        if (isset($data)) {
            return response()->json($data);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found.']);
        }
    }
    
    // 39. review appointment
    public function review_appointment($id)
    {
        $data = ['status' => 404, 'msg' => 'Data not found.'];
        if($id != '' && $id > 0){
            $query = Doctorappoinment::where('id', $id)->where('status', 0);
            if($query->exists()){
                $query->update(['status' => 1]);
                $data = ['status' => 200, 'msg' => 'Appointment reviewed.'];
            }
        }
        return response()->json($data);
    }
    
    // 40. add package booking
    public function add_package_booking(Request $request)
    {
        $data = $request->all();
        PackageBooking::insert($data);
        $res = ['status' => 200, 'msg' => 'Package booking created.'];
        return response()->json($res);
    }
    
    // 41. get package booking
    public function get_package_booking($id='')
    {
        $data = ['status' => 200, 'msg' => 'Data not found.'];
        
        if($id != '' && $id > 0){
            $package_booking = PackageBooking::where('id', $id)->first();
            if($package_booking){
                $data = ['status' => 200, 'data' => $package_booking];
            }
        }else{
            $package_booking = PackageBooking::get();
            if($package_booking->count() > 0){
                $data = ['status' => 200, 'data' => $package_booking];
            }
        }
        return response()->json($data);
    }
    
    // 42. personal appointment
    public function personal_appointment($id)
    {
        if($id != '' && $id >0){
            $appointment = Doctorappoinment::where('user_id', $id)->get();
            
            $data = ['status' => 404, 'msg' => 'Data not found.'];
            if($appointment->count() > 0){
                $data = ['status' => 200, 'data' => $appointment];
            }
            return response()->json($data);
        }
    }
    
    // 43. visa processing
    public function add_visa_processing(Request $request)
    {
        $path = 'assets/docs/visa_processing/';
        $passport = $this->upload_file($request, $path, 'passport');
        $medicalReport1 = $this->upload_file($request, $path, 'medicalReport1');
        $medicalReport2 = $this->upload_file($request, $path, 'medicalReport2');
        $invitationLetter = $this->upload_file($request, $path, 'invitationLetter');
        
        
        $data = $request->all();
        $data['passport'] = $passport;
        $data['medicalReport1'] = $medicalReport1;
        $data['medicalReport2'] = $medicalReport2;
        $data['invitationLetter'] = $invitationLetter;

        VisaProcessing::insert($data);
        $res = ['status' => 200, 'msg' => 'Visa processing added.'];
        return response()->json($res);
    }
    
    // 44. get visa processing
    public function get_visa_processing($id ='')
    {
        $data = ['status' => 404, 'msg' => 'Data not found.'];
        if($id != '' && $id >0){
            $visa = VisaProcessing::where('id', $id)->first();
            if($visa != ''){
                $data = ['status' => 200, 'data' => $visa];
            }
        }else{
            $visa = VisaProcessing::get();
            if($visa->count() > 0){
                $data = ['status' => 200, 'data' => $visa];
            }
        }
        return response()->json($data);
    }
    
    // 45. mark appointment as success
    public function appointment_success($id)
    {
        $data = ['status' => 404, 'msg' => 'Data not found.'];
        if($id != '' && $id > 0){
            $query = Doctorappoinment::where('id', $id)->where('status', 1);
            if($query->exists()){
                $query->update(['status' => 2]);
                $data = ['status' => 200, 'msg' => 'Appointment reviewed success.'];
            }
        }
        return response()->json($data);
    }
    
    // 46. news 
    public function add_news(Request $request)
    {
        $path = 'assets/images/news/';
        $image = $this->upload_file($request, $path, 'newsImage');
        
        $data = $request->all();
        $data['newsImage'] = $image;
        
        News::insert($data);
        $res = ['status' => 200, 'msg' => 'News added.'];
        return response()->json($res);
    }
    
    // 47. get news
    public function get_news($id ='')
    {
        $data = ['status' => 404, 'msg' => 'Data not found.'];
        if($id != '' && $id >0){
            $news = News::where('id', $id)->first();
            if($news != ''){
                $data = ['status' => 200, 'data' => $news];
            }
        }else{
            $news = News::get();
            if($news->count() > 0){
                $data = ['status' => 200, 'data' => $news];
            }
        }
        return response()->json($data);
    }
    
    // 48. news 
    public function add_blog(Request $request)
    {
        $path = 'assets/images/blog/';
        $image = $this->upload_file($request, $path, 'blogImage');
        
        $data = $request->all();
        $data['blogImage'] = $image;
        
        Blogs::insert($data);
        $res = ['status' => 200, 'msg' => 'Blogs added.'];
        return response()->json($res);
    }
    
    // 49. get news
    public function get_blog($id ='')
    {
        $data = ['status' => 404, 'msg' => 'Data not found.'];
        if($id != '' && $id >0){
            $blog = Blogs::where('id', $id)->first();
            if($blog != ''){
                $data = ['status' => 200, 'data' => $blog];
            }
        }else{
            $blog = Blogs::get();
            if($blog->count() > 0){
                $data = ['status' => 200, 'data' => $blog];
            }
        }
        return response()->json($data);
    }
    
    // all category length
    public function category_length()
    {
        $categories = [
            'doctor' => $this->getCategoryData(Doctor::class, 'doctor', '/home/doctors-list'),
            'doctor_appointment' => $this->getCategoryData(Doctorappoinment::class, 'doctor_appointment', '/home/add-appointMent'),
            'health_package' => $this->getCategoryData(PackageBooking::class, 'health_package', '/home/health-Package'),
            'patient' => $this->getCategoryData(User::class, 'patient', '/home/users'),
            'package' => $this->getCategoryData(Package::class, 'package', '/home/get-packages'),
            'center' => $this->getCategoryData(Center::class, 'center', '/home/centers-list'),
            'air_ticket' => $this->getCategoryData(AirTicket::class, 'air_ticket', '/home/airTicket'),
            'air_pickup' => $this->getCategoryData(AirPickup::class, 'air_pickup', '/home/airPickup'),
            'air_ambulance' => $this->getCategoryData(AirAmbulance::class, 'air_ambulance', '/home/airAmbulance'),
            'order_medicine' => $this->getCategoryData(OrderMedicine::class, 'order_medicine', '/home/medicineOrder'),
            'tele_medicine' => $this->getCategoryData(TeleMedicine::class, 'tele_medicine', '/home/teleMedicine'),
            'medical_record' => $this->getCategoryData(MedicalReport::class, 'medical_record', '/home/medicalRecord'),
            'health_checkup' => $this->getCategoryData(HealthCheckup::class, 'health_checkup', '/home/check-up'),
            'visa' => $this->getCategoryData(VisaProcessing::class, 'visa', '/home/visa_processing'),
            'client_query' => $this->getCategoryData(Question::class, 'query', '/home/seeQuery'),
        ];

        return response()->json(['status' => 200, 'data' => $categories]);
    }
    
    private function getCategoryData($modelClass, $routeKey, $link="")
    {
        return [
            'name' => ucfirst(str_replace('_', ' ', $routeKey)),
            'length' => $modelClass::count(),
            'link' => $link,
        ];
    }
    
    // 50. search package by name
    public function search_package($name)
    {
        $data = ['status' => 404, 'msg' => 'Data not found.'];
        if($name != ''){
            $result = Package::where('title', 'LIKE', "$name%")->get();
            if($result->count() > 0){
                $data = ['status' => 200, 'data' => $result];
            }
        }
        return response()->json($data);
    }
    
    // 51. search center by name
    public function search_center($name)
    {
        $data = ['status' => 404, 'msg' => 'Data not found.'];
        if($name != ''){
            $result = Center::where('name', 'LIKE', "$name%")->get();
            if($result->count() > 0){
                $data = ['status' => 200, 'data' => $result];
            }
        }
        return response()->json($data);
    }
    
    // send email
    public function send_mail($mail_data)
    {
        $this->set_config('MAIL_MAILER', 'smtp');
        $this->set_config('MAIL_ENCRYPTION', 'tls');
        $this->set_config('MAIL_HOST', 'natstechbd.com');
        $this->set_config('MAIL_PORT', 465);
        $this->set_config('MAIL_USERNAME', 'admin@bumrungraddiscover.com');
        $this->set_config('MAIL_PASSWORD', 'Bumrungrad_Discover@__Admin');
        Mail::to($mail_data['PataientEmail'])->send(new SendMail($mail_data));
        Mail::to('rodiscoverbangladesh@gmail.com')->send(new SendMail($mail_data));
        return 'mail sent';
    }
    
    function set_config($key = '', $value = '')
    {
        $config = json_decode(file_get_contents(base_path('config/config.json')), true);
        $config[$key] = $value;
        file_put_contents(base_path('config/config.json'), json_encode($config));
    }
}
