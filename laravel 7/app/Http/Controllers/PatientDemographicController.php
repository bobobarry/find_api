<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientDemographicRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;
use App\Models\PatientDemographic;
use Illuminate\Http\Request;

class PatientDemographicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PatientDemographic::with(['rdts','vitals', 'covids','malarias','bloodPressures', 'glucoses','malnutritions'])->orderBy('id', 'DESC')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PatientDemographicRequest $request)
    {
        $patient = PatientDemographic::create($request->validated());
        if($patient->id !== null) {
            $patient->patient_uid  = $this->generatePatientUniqueId($patient->id);
            $patient->qrCode  = $this->qrCode($patient->id);
            $patient->save();
        }
        return $this->successResponse($patient , 'Patient créée avec succès');
        
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientDemographic  $patientDemographic
     * @return \Illuminate\Http\Response
     */
    public function show(PatientDemographic $patient)
    {
        return $this->successResponse($patient, null);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientDemographic  $patientDemographic
     * @return \Illuminate\Http\Response
     */
    public function update(PatientDemographicRequest $request, PatientDemographic $patient)
    {
        $patient->update($request->validated());
        return $this->successResponse($patient, "Données demograhiques modifiées avec sucèss");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientDemographic  $patientDemographic
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientDemographic $patient)
    {
        $patient->delete();
        return $this->successResponse(null, "Patient supprimé avec succès");
    }

    public function uploadPhoto(Request $request) {
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = $request['patient_id'].".".$photo->extension();

            $image = Image::make($request->file('photo'));
    
            $image->resize( 91, 121); // Redimensionnement de l'image à 120 x 80 px
            $image->save(public_path()."/files/".$photoName); // Enregistrement de l'image

            // $imagePath = public_path(). '/files';
            // $photo->move($imagePath, $photoName);

            PatientDemographic::where('id',$request['patient_id'])->update(array('photo' => $photoName));

            return $photoName;
        }
    }

    public function print(Request $request) {
        $patients = PatientDemographic::select("*")
                    ->where('card_printed', 0)
                    ->when($request->has('town'), function ($query) use ($request) {
                        $query->where('town', $request->town);
                    })
                    ->when($request->has('quartier'), function ($query) use ($request) {
                        $query->where('quartier', $request->quartier);
                    })
                    ->when($request->has('sector'), function ($query) use ($request) {
                        $query->where('sector', $request->sector);
                    })
                    ->limit(9)
                    ->get();
   
        return $this->successResponse($patients, null);
    }
    
    public function qrCode($patientID) {
        $path = public_path('qrCode/'.time().'.png');
        $text = 'http://find.laclinico.com/detail-patient/' . $patientID;
  
        $image = QrCode::format('png')
        ->size(500)
        ->errorCorrection('H')
        ->generate($text, $path);

        return time().'.png';
    }

    public function generatePatientUniqueId($patientID){
        $patient = PatientDemographic::find($patientID);
		$patient_unique_id= substr($patient->first_name, 0, 1).substr($patient->last_name, 0, 1).
			($patient->date_of_birth === "Invalid date" ? '00' : date('y' , strtotime($patient->date_of_birth))). substr(md5(rand(10,8000000)),0 ,5).
			(($patient->town) == "" ? 'xxx' : substr($patient->town, 0, 3));

			$patient_unique_id_exist = PatientDemographic::where('patient_uid', strtoupper($patient_unique_id))->get();
		if($patient_unique_id_exist === NULL){
			$this->generatePatientUniqueId($patientID);
		}else{
			return  strtoupper($patient_unique_id);
		}

	}

}