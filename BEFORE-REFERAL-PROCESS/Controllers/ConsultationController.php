<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultationRequest;
use App\Models\Consulations;
use App\Models\PatientDemographic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $consulationData = [];
        foreach(Consulations::orderBy('id', 'DESC')->get() as $consultation) {
            $peopleEnrolled = PatientDemographic::where('date_of_registration',$consultation->date_of_consult)
                                ->where('type_of_consultation',$consultation->type_consult)
                                ->where('sector',$consultation->location_of_consult)
                                ->count('id');
            $consultation['people_enrolled'] = $peopleEnrolled;
            $consulationData[] = $consultation;
        }
        return [$consulationData,User::where('user_type', 'agent')->get()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConsultationRequest $request)
    {
        $consulation = Consulations::create($request->validated());
        if($consulation->id !== null ) {
            $consulation->created_by = Auth::user()->id;
            $consulation->save();
        }
        return $this->successResponse($consulation);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConsultationRequest $request, Consulations $consultation)
    {
        $consultation->update($request->validated());
        return $this->successResponse($consultation, "Consultation modifiés avec sucèss");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
