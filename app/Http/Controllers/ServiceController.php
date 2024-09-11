<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Http\Resources\ServiceCollection;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new ServiceCollection(Service::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request)
    {
        $data = $request->validated();
        
        $service = Service::create([
            'name' => $data['name'],
            'price' => $data['price']
        ]);

        return response([
            'message' => 'Service created',
            'service' => $service
        ], Response::HTTP_CREATED);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, string $id)
    {
        $data = $request->validated();

        $service = Service::findOrFail($id);

        $service->update([
            'name' => $data['name'],
            'price' => $data['price']
        ]);

        return response([
            'message' => 'Service updated',
            'service' => new ServiceResource($service)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Service::destroy($id);

        return response([
            'message' => 'Service deleted'
        ], Response::HTTP_OK);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::findOrFail($id);

        return response([
            'service' => new ServiceResource($service)
        ], Response::HTTP_OK);
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
