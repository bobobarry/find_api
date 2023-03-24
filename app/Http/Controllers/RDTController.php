<?php

namespace App\Http\Controllers;

use App\Http\Requests\RDTRequest;
use App\Models\RdtSreening;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RDTController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RdtSreening::all();
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RDTRequest $request)
    {
        
        $validatedData = $request->validated();

        $validatedData['rdt_image'] = $this->uploadPhoto($request);
        if($validatedData['rdt_image'] !== null) {
            RdtSreening::where('patient_id', $request['patient_id'])
            ->where('rdt_type', $request['rdt_type'])
            ->where('symptome_id', $request['symptome_id'])
            ->update([
                'rdt_image' => 'test.jpg',
                'rdt_result' => $validatedData['rdt_result'],
                'created_by' => Auth::user()->id,
                'rdt_result_at' => Carbon::now()
            ]);
        }
        return $this->successResponse($validatedData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RdtSreening  $rdtSreening
     * @return \Illuminate\Http\Response
     */
    public function show(RdtSreening $rdtSreening)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RdtSreening  $rdtSreening
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RdtSreening $rdtSreening)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RdtSreening  $rdtSreening
     * @return \Illuminate\Http\Response
     */
    public function destroy(RdtSreening $rdtSreening)
    {
        //
    }

    public function uploadPhoto(Request $request) {
        if ($request->hasFile('rdt_image')) {
            $photo = $request->file('rdt_image');
            $photoName = $request['rdt_type'].".".$request['patient_id'].".".$photo->extension();

            $imagePath = public_path(). '/rdt';
            $photo->move($imagePath, $photoName);

            return $photoName;
        }
}

    
}
