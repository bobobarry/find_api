<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PatientDemographicRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;
use App\Models\PatientDemographic;
use App\Models\RdtSreening;
use App\Models\Referals;
use App\Models\User;
use App\Models\VitalParameter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\TraitMakeReferals;

class PatientDemographicController extends Controller
{
    use TraitMakeReferals;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return PatientDemographic::query()
                                        ->when($request->has('dateRange'), function ($q) use ($request)  {
                                            $q->where('date_of_registration','>=', Carbon::parse($request->dateRange['from'])->format('Y-m-d'))
                                                ->where('date_of_registration','<=', Carbon::parse($request->dateRange['to'])->format('Y-m-d'));
                                        })
        
                                    ->with(['rdts','vitals', 'covids','malarias','bloodPressures', 'glucoses','malnutritions'])
                                    ->orderBy('id', 'DESC')
                                    ->get();
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
            $patient->created_by = Auth::user()->id;
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
        $text = 'https://find.laclinico.com/detail-patient/' . $patientID;
  
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

    public function stats() {
        $counterData = [];
        // Mens
        $mens = PatientDemographic::where('gender', 'Male')
            ->whereDate('date_of_birth', '<', \Carbon\Carbon::now()->subYears(18))
            ->count();
        $counterData['mens'] = $mens;
        // who know do_you_know_date_of_birth
        $dykdate_of_birth = PatientDemographic::where('do_you_know_date_of_birth', 'Yes')
            ->count();
        $counterData['dykdate_of_birth'] = $dykdate_of_birth;

        // who don't know do_you_know_date_of_birth
        $dydkdate_of_birth = PatientDemographic::where('do_you_know_date_of_birth', 'No')
            ->count();
        $counterData['dydkdate_of_birth'] = $dydkdate_of_birth;
        // WOMEN
        $women = PatientDemographic::where('gender', 'Female')
            ->whereDate('date_of_birth', '<', \Carbon\Carbon::now()->subYears(18))
            ->count();
        $counterData['women'] = $women;
        $total_pregnant = PatientDemographic::where('pregnant', 'Yes')
            ->count();
            $counterData['pregnant'] = $total_pregnant;
        // CHILDRENS
        $total_child = PatientDemographic::whereDate('date_of_birth', '>', \Carbon\Carbon::now()->subYears(18))
            ->count();
        $counterData['total_child'] = $total_child;
        $total_child_boys = PatientDemographic::where('gender', 'Male')
            ->whereDate('date_of_birth', '>', \Carbon\Carbon::now()->subYears(18))
            ->count();
        $counterData['total_child_boys'] = $total_child_boys;
        $total_child_girls = PatientDemographic::where('gender', 'Female')
            ->whereDate('date_of_birth', '>', \Carbon\Carbon::now()->subYears(18))
            ->count();
        $counterData['total_child_girls'] = $total_child_girls;
        // CHILDRENS

        $counterData['totalPatient'] =  $counterData['mens'] + $counterData['women'] + $counterData['total_child'] ;

        // RDT
        $rdtTypes = ['covid', 'malaria'];
        $rdtStatus = ['positif','negatif','indeterminate','invalid', NULL];
        for($i=0; $i < count($rdtTypes); $i++) {
            for($j=0; $j < count($rdtStatus); $j++) {
                $counterData[$rdtTypes[$i]][$rdtStatus[$j] !== NULL ? $rdtStatus[$j] : "didnot"] = $this->statsCP($rdtTypes[$i],$rdtStatus[$j]);
            }
            $counterData["total_" . $rdtTypes[$i]] = $this->statsCP($rdtTypes[$i]);
        }
        // RDT

        //VITALS
        $vitals = ['bloodPressure','malnutrition','glucose'];
        for($i=0; $i < count($vitals); $i++) {
            $counterData[$vitals[$i]] = $this->statsVital($vitals[$i]);
        }

        // GL Details and BPDetails
        $counterData['glDetails'] = $this->statsVitalDetails('glucose');
        $counterData['bpDetails'] = $this->statsVitalDetails('bloodPressure');

        $counterData['patientCovid'] = RdtSreening::with([
                                                'patient:id,name,phone_number,patient_uid',
                                                'patient.temperature:id,patient_id,vital_type,vital_flag,temperature',
                                                'patient.oxygen:id,patient_id,vital_type,vital_flag,oxygen_saturation',
                                                'symptom',
                                                ])
                                    ->where('rdt_type', 'covid')
                                    ->where('is_active', true)
                                    ->where('rdt_result', 'positif')
                                    ->select('id','patient_id','symptome_id','rdt_result','rdt_type')
                                    ->get();
        
        return $this->successResponse($counterData, Null);
    }

    public function statsCP($type, $status = '') {
        $totals = RdtSreening::where('rdt_type', $type)
        ->where('is_active', true)
        ->when($status !== '', function ($query) use ($status) {
            $query->where('rdt_result', $status);
        })
        ->count();
        return $totals;
    }

    public function statsVital($type) {
        $totals = VitalParameter::where('vital_type', $type)
        // ->where('vital_flag','>=', VitalParameter::FLAG_VER_BAD)
        ->where('is_active', true)
        ->count();
        return $totals;
    }

    public function statsVitalDetails($type) {
         $flag = [VitalParameter::FLAG_NORMAL,VitalParameter::FLAG_MID_BAD,VitalParameter::FLAG_VER_BAD]; 
        $counterFlag = [];
        for($i=0; $i< count($flag);$i++) {
            $counterFlag[] = VitalParameter::where('vital_type', $type)
                ->where('vital_flag', $flag[$i])
                ->where('is_active', true)
                ->orderBy('vital_flag', 'ASC')
                ->count();
            
        }
        return $counterFlag;

    }


    public function getStatisticToday() {
        $today = date('Y-m-d');
        $posts = User::select('id','name','email')
                            ->with([
                                'symptoms' => function($query) {
                                            $query->select('created_by','created_at')
                                            ->orderBy('created_at', 'DESC')
                                            ->first();
                                },
                                'rdts' => function($query) {
                                    $query->select('created_by','created_at')
                                            ->orderBy('created_at', 'DESC')
                                            ->first();
                                }
                            
                        ])
                            ->withCount(['rdts' => function($query) {
                                                    $query->where('created_at', date('Y-m-d'));
                            }])
                            // ->has('rdts', '>', 0)
                            ->withCount(['symptoms as patients' => function($query) {
                                $query->where('created_at', date('Y-m-d'));
                            }])
                            // ->has('symptoms', '>', 0)
                            // ->whereIn('email', ['tambapaulsossouadouno@gmail.com','dialloamie162@gmail.com','cecilebalamou15@gmail.com'])
                        ->get();

                        return $this->successResponse($posts, null);
    }

    public function accounts(Request $request) {
        $posts = User::select('id','name','email')
                            ->with([
                                'symptoms' => function($query) {
                                            $query->select('created_by','created_at')
                                            ->orderBy('created_at', 'DESC')
                                            ->first();
                                },
                                'rdts' => function($query) {
                                    $query->select('created_by','created_at')
                                            ->orderBy('created_at', 'DESC')
                                            ->first();
                                }
                            
                        ])
                            ->withCount(['rdts' => function($query) use ($request) {
                                $query->when($request->has('dateRange'), function ($q) use ($request)  {
                                    $q->where('created_at','>=', Carbon::parse($request->dateRange['from'])->format('Y-m-d 00:00:00'))
                                        ->where('created_at','<=', Carbon::parse($request->dateRange['to'])->format('Y-m-d 23:59:59'));
                                });
                            }])
                            // ->has('rdts', '>', 0)
                            ->withCount(['symptoms as patients' => function($query) use ($request) {
                                $query->when($request->has('dateRange'), function ($q) use ($request)  {
                                    $q->where('created_at','>=', Carbon::parse($request->dateRange['from'])->format('Y-m-d 00:00:00'))
                                        ->where('created_at','<=', Carbon::parse($request->dateRange['to'])->format('Y-m-d 23:59:59'));
                                });
                            }])
                            // ->has('symptoms', '>', 0)
                            ->when($request->has('user'), function ($query) use ($request) {
                                $query->where('id', $request['user']['id']);
                            })
                        ->whereIn('email', ['tambapaulsossouadouno@gmail.com','dialloamie162@gmail.com','cecilebalamou15@gmail.com']) 
                        ->get();

                        return $this->successResponse($posts, null);$posts = User::select('id','name','email')
                        ->with([
                            'symptoms' => function($query) {
                                        $query->select('created_by','created_at')
                                        ->orderBy('created_at', 'DESC')
                                        ->first();
                            },
                            'rdts' => function($query) {
                                $query->select('created_by','created_at')
                                        ->orderBy('created_at', 'DESC')
                                        ->first();
                            }
                        
                    ])
                        ->withCount(['rdts' => function($query) use ($request) {
                            $query->when($request->has('dateRange'), function ($q) use ($request)  {
                                $q->where('created_at','>=', Carbon::parse($request->dateRange['from'])->format('Y-m-d 00:00:00'))
                                    ->where('created_at','<=', Carbon::parse($request->dateRange['to'])->format('Y-m-d 23:59:59'));
                            });
                        }])
                        // ->has('rdts', '>', 0)
                        ->withCount(['symptoms as patients' => function($query) use ($request) {
                            $query->when($request->has('dateRange'), function ($q) use ($request)  {
                                $q->where('created_at','>=', Carbon::parse($request->dateRange['from'])->format('Y-m-d 00:00:00'))
                                    ->where('created_at','<=', Carbon::parse($request->dateRange['to'])->format('Y-m-d 23:59:59'));
                            });
                        }])
                        // ->has('symptoms', '>', 0)
                        ->when($request->has('user'), function ($query) use ($request) {
                            $query->where('id', $request['user']['id']);
                        })
                    //->whereIn('email', ['tambapaulsossouadouno@gmail.com','dialloamie162@gmail.com','cecilebalamou15@gmail.com']) 
                    ->get();

                    return $this->successResponse($posts, null);
    }

    public function mustBeReferal(PatientDemographic $patient) {
        return Referals::where('patient_id', $patient->id)->count();
    }

    public function referalByPatient(PatientDemographic $patient) {
        return $this->getPatientReferals($patient->id);
    }

    public function referalByPatientOK(PatientDemographic $patient) {
        return $this->getPatientReferalsOK($patient->id);
    }

    public function sheets()
    {
        $result =  PatientDemographic::orderBy('id', 'DESC')
                                    ->get();

        return ($result);
    }

}