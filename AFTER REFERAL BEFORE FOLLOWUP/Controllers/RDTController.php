<?php

namespace App\Http\Controllers;

use App\Http\Requests\RDTRequest;
use App\Models\NewDiagnosted;
use App\Models\RdtSreening;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\TraitMakeReferals;

class RDTController extends Controller
{
    use TraitMakeReferals;
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
            $success = RdtSreening::where('patient_id', $request['patient_id'])
            ->where('rdt_type', $request['rdt_type'])
            ->where('symptome_id', $request['symptome_id'])
            ->update([
                'rdt_image' => $validatedData['rdt_image'],
                'rdt_result' => $validatedData['rdt_result'],
                'created_by' => Auth::user()->id,
                'rdt_result_at' => Carbon::now() 
            ]);
            
            if($success !== null) {
                $new = new NewDiagnosted();
                    if($request['do_you_have_the_disease'] == 'No' and $validatedData['rdt_result'] == 'positif' ) {
                        $this->isDisease($request['patient_id'], $request['rdt_type']);
                        $new->patient_id = $request['patient_id'];
                        $new->diagnosted = $request['rdt_type'];
                        $new->do_you_have_the_disease = $request['do_you_have_the_disease'];
                        $new->status = 'first';
                        $new->is_active = 1;
                        $new->save();
                    } else if($request['do_you_have_the_disease'] == 'Yes') {
                        $this->isDisease($request['patient_id'], $request['rdt_type']);
                        $new->patient_id = $request['patient_id'];
                        $new->diagnosted = $request['rdt_type'];
                        $new->do_you_have_the_disease = $request['do_you_have_the_disease'];
                        $new->status = 'know';
                        $new->is_active = 1;
                        $new->save();
                    }
                    $rdt = RdtSreening::where('patient_id', $request['patient_id'])
                            ->where('rdt_type', $request['rdt_type'])
                            ->where('symptome_id', $request['symptome_id'])->first();
                    if($validatedData['rdt_result'] == 'positif') {
                        if($new !== null) {
                            $this->createReferals($rdt->id, $rdt->patient_id, $request['rdt_type']);
                        }
                    } else {
                        $this->canceledReferal($rdt->id, $rdt->patient_id, $request['rdt_type']);
                    }

            }
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

    public function addRdtResultText(Request $request) {
        $success = RdtSreening::where('patient_id', $request['patient_id'])
            ->where('rdt_type', $request['rdt_type'])
            ->where('symptome_id', $request['symptome_id'])
            ->update([
                'rdt_result' => $request['rdt_result'],
                'created_by' => Auth::user()->id,
                'rdt_result_at' => Carbon::now() 
            ]);

    }

    public function getLastAdd(Request $request) {
        $result = RdtSreening::where('patient_id', $request['patient_id'])
            ->where('rdt_type', $request['rdt_type'])
            ->where('symptome_id', $request['symptome_id'])
            ->where('is_active', 1)
            ->first();
            
        return $this->successResponse($result);
    }

    public function rdtSheets() {
        return RdtSreening::where('is_active', true)->get();
    }

    
}
