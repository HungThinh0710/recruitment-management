<?php

namespace App\Http\Controllers;
use Illuminate\Support\Collection;
use App\Services\CandidateService;
use App\Candidate;
use Illuminate\Http\Request;
use App\Http\Requests\CandidateRequest;

/**
 * @group Candidate management
 */
class CandidateController extends Controller
{
    protected $candidateService;
    protected $candidateServices;
    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
        $this->candidateServices = new CandidateServices;
    }

    /**
     * Display a listing of the candidate.
     * @bodyParam keyword string keyword want to search.
     * @bodyParam property string Field in table you want to sort(fullname,email,phone,address,cv,status,created_at,updated_at). Example: fullname
     * @bodyParam orderby string The order sort (ASC/DESC). Example: asc
     */
    public function index(Request $request)
    {   
        try{
            if ($request->has("keyword","property","orderby")&& $request->keyword !=null&& $request->property !=null && $request->orderby !=null )
            {
                $data = $request->only("keyword","property","orderby");
                return response()->json(
                        Candidate::where('fullname', 'like', '%'.$data["keyword"].'%')
                                ->orWhere('email', 'like', '%'.$data["keyword"].'%')
                                ->orWhere('phone', 'like', '%'.$data["keyword"].'%')
                                ->orWhere('address', 'like', '%'.$data["keyword"].'%')
                                ->orWhere('technicalSkill', 'like', '%'.$data["keyword"].'%')
                                ->orderBy($data["property"], $data["orderby"])
                                ->with(["jobs","interviews"])
                                ->paginate(10)
                    );
            }     
            else if ($request->has("keyword")&& $request->keyword !=null)
            {
                $data = $request->keyword;
                return response()->json(
                        Candidate::where('fullname', 'like', '%'.$data.'%')
                                ->orWhere('email', 'like', '%'.$data.'%')
                                ->orWhere('phone', 'like', '%'.$data.'%')
                                ->orWhere('address', 'like', '%'.$data.'%')
                                ->orWhere('technicalSkill', 'like', '%'.$data.'%')
                                ->with(["jobs","interviews"])
                                ->paginate(10)
                    );
            }
            else if ($request->has("property","orderby")&& $request->property !=null && $request->orderby !=null )
            {
                $data = $request->only("property","orderby");
                return response()->json(
                    Candidate::orderBy($data["property"], $data["orderby"])
                                ->with(["jobs","interviews"])
                                ->paginate(10)
                );
            }
            else{
                return response()->json(Candidate::with(["jobs","interviews"])->paginate(10));
            }

        }
        catch(\Illuminate\Database\QueryException $queryEx){
            return response()->json(['message' => $data["property"]." field is not existed"],422);
        }
        catch(\InvalidArgumentException $ex){
            return response()->json(['message' => $data["orderby"]." field is invalid"],422);
        }
    }
        
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a candidate.
     *
     * @bodyParam fullname string required The full name of the candidate.
     * @bodyParam email string required The email of the candidate.
     * @bodyParam phone string required The phone of the candidate.
     * @bodyParam address string required The address of the candidate.
     * @bodyParam description string The description of the candidate.
     * @bodyParam technicalSkill string  The technicalSkill of the candidate. Example: NodeJs-2, PHP-1
     * @bodyParam CV file required The resume of the candidate.
     */
    public function store(CandidateRequest $request)
    {
        //check by email if candidate is existed
        $candidate = Candidate::where('email','=',$request["email"])->first();
        if ($candidate!=null)
        {
            //update old candidate
            //delete old CV and upload new CV
            unlink('upload/CV/'.$candidate->CV);
             $fileName = $this->candidateServices->handleUploadNewCV($request->file('CV'));
            if ($fileName == NULL){
                return response()->json(['message' => "Upload failed, file not exist"],422);
            }
            $candidate->update($request->except("CV","created_at","updated_at")
                            +["CV"=> $fileName]
                            +["status"=>1]);
            return response()->json(['message'=>'Updated a candidate successfully'],200);
        }
        //if candidate is not existed in database
        else
        {
            //upload CV
            $fileName = $this->candidateServices->handleUploadNewCV($request->file('CV'));
            if($fileName == NULL){
                return response()->json(['message' => "Upload failed, file not exist"],422);
            }
            Candidate::create($request->except("CV","created_at","updated_at")
                            +["CV"=> $fileName]
                            +["status"=>1]);
            return response()->json(['message'=>'Created a candidate successfully'],200);
        }       
    }

    
    /**
     * Show a candidate by ID
     */
    public function show($candidateID)
    {
        $candidate = Candidate::with(["interviews","jobs"])->findOrFail($candidateID);
        //solve technical skill data
        $technical_arr = explode(",",$candidate->technicalSkill);
        $technicalSkill =  new Collection();
        foreach ($technical_arr as $key => $technical) {
            $tech = explode("-",$technical);
            $technicalSkill ->push([
                "name"=>$tech[0],
                "year"=>$tech[1]
            ]);
        }
        $candidate->technicalSkill = $technicalSkill;
        //solve status data
        switch ($candidate->status) {
            case '1':
                $status = "Pending";
                break;
            case '2':
                $status = "Deny";
                break;
            case '3':
                $status = "Approve Application";
            break;
            case '4':
                $status = "Passed";
            break;
            case '5':
                $status = "Failed";
            break;
            default:
                break;
        }
        $candidate->status = $status;
        return response()->json($candidate);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function edit(Candidate $candidate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Candidate $candidate)
    {
        //
    }

    /**
     * Update status of candidate.
     *
     * @bodyParam candidateId numeric required The Id of candidate.
     * @bodyParam status string required The email of the candidate.
     */
    public function updateStatus(Request $request){
        $this->validate($request,[
            "candidateId" => "required|exists:candidates,id",
            "status" => "required|string"
        ]);

        $numberStatus = $this->candidateService->convertStringStatusToNumber($request->input("status"));
        if($numberStatus == NULL)
            return response()->json(["message" => "Invalid status!"],422);
        Candidate::findOrFail($request->input("candidateId"))->update(["status" => $numberStatus]);
        return response()->json(["message" => "Updated status of candidate successfully!"],200);
    }
    /**
     * Delete the candidate by Id.
     * @bodyParam candidateId array required The id/list id of candidate. Example: [1,2,3,4,5]
     */
    public function destroy(CandidateRequest $request)
    {
        $candidateIds = request("candidateId");
        $exists = Candidate::whereIn('id', $candidateIds)->pluck('id');
        $notExists = collect($candidateIds)->diff($exists);
        $idsNotFound = "";
        foreach ($notExists as $key => $value) {
            $idsNotFound .= $value.",";
        }
        if($notExists->isNotEmpty()){
            return response()->json([
                'message'=>'Not found id: '.substr($idsNotFound,0,strlen($idsNotFound)-1)],404);
        }
        Candidate::destroy($exists);
        return response()->json([
           'message'=>'Deleted the candidate successfully']);
    }

}
class CandidateServices
{
    public function handleUploadNewCV($file)
    {
        if (!is_null($file)) {
            $fileName = 'CV_'.now()->year.'_'.str_random(5).'-enclave_'.$file->getClientOriginalName();
            $file->move(public_path('upload/CV'),$fileName);
            return $fileName;
        }
        else{
            return NULL;
        }
    }
}
