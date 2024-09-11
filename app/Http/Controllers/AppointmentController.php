<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Service;
use MongoDB\BSON\ObjectId;
use App\Models\Appointment;
use Illuminate\Http\Request;
// use MongoDB\Laravel\Eloquent\Casts\ObjectId;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentCollection;
use Symfony\Component\HttpFoundation\Response;
use MongoDB\BSON\UTCDateTime;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new AppointmentCollection(Appointment::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $services = Service::whereIn('_id', $request->services)->get();
        
        $appointment = Appointment::create([
            'service_id' => $services->toArray(),
            'date' => Carbon::parse($request['date']),
            'time' => $request['time'],
            'totalAmount' => $request['totalAmount'],
            'user_id' => new ObjectId(Auth::user()->id)
        ]);

        return response([
            'message' => 'Appointment scheduled correctly',
            'appointment' => $appointment
        ], Response::HTTP_CREATED);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $appointment = Appointment::findOrFail($id);

        $services = Service::whereIn('_id', $request->services)->get();

        $appointment->service_id = $services->toArray();
        $appointment->date = Carbon::parse($request->date);
        $appointment->time = $request->time;
        $appointment->totalAmount = $request->totalAmount;
        $appointment->user_id = new ObjectId(Auth::user()->id);

        $appointment->save();

        return response([
            'message' => 'Appointment updated successfully',
            'appointment' => new AppointmentResource($appointment)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Appointment::destroy($id);

        return response([
            'message' => 'Appointment canceled successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::findOrFail($id);
        
        return response([
            'appointment' => new AppointmentResource($appointment)
        ], Response::HTTP_OK);
    }

    public function getByDate(Request $request)
    {
        $dateString = $request->query('date');
        // dd($dateString);

        if(!$dateString) {
            return response([
                'message' => 'Date parameter is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $date = Carbon::createFromFormat('d/m/Y', $dateString);

        $startOfDay = new UTCDateTime($date->startOfDay()->timestamp * 1000);
        $endOfDay = new UTCDateTime($date->endOfDay()->timestamp * 1000);

        $appointments = Appointment::whereBetween('date', [$startOfDay, $endOfDay])
                                    ->select('time')->get();

        return response($appointments, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
}
