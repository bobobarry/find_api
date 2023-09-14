<?php

namespace App\Http\Controllers;

use App\Http\Requests\MBRequest;
use App\Models\MedicalBackground;
use Faker\Provider\Medical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalBackgroundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MedicalBackground::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MBRequest $request)
    {
        $medical = MedicalBackground::create($request->validated());
        if($medical->id !== null) {
            MedicalBackground::is_active($medical->patient_id);
            $medical->is_active = 1;
            $medical->created_by = Auth::user()->id;
            $medical->save();
        }
        return $this->successResponse($medical, "Enregistré avec succès");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MedicalBackground  $medicalBackground
     * @return \Illuminate\Http\Response
     */
    public function show(MedicalBackground $medical)
    {
        return $this->successResponse($medical, null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MedicalBackground  $medicalBackground
     * @return \Illuminate\Http\Response
     */
    public function update(MBRequest $request, MedicalBackground $medical)
    {
        $medical->update($request->validated());
        return $this->successResponse($medical, "Modifié avec succés");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MedicalBackground  $medicalBackground
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicalBackground $medicalBackground)
    {
        //
    }


    public function bgmSheet() {
        return MedicalBackground::where('is_active', true)->get();
    }
}
