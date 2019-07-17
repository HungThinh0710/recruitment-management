<?php

namespace App\Http\Controllers;
use Illuminate\Support\Collection;

use App\Candidate;
use Illuminate\Http\Request;

/**
 * @group Candidate management
 */
class CandidateController extends Controller
{
    /**
     * Display a listing of the candidate.
     * @bodyParam keyword string keyword want to search.
     * @bodyParam property string Field in table you want to sort(fullname,email,phone,address,cv,status,created_at,updated_at). Example: fullname
     * @bodyParam orderby string The order sort (ASC/DESC). Example: asc
     */
    public function index(Request $request)
    {        
        if ($request->has("keyword","property","orderby")&& ($request->keyword !=null || $request->property!=null))
        {
            $data = $request->only("keyword","property","orderby");
            if ($data["keyword"]!=null)
            {
                return response()->json(
                    Candidate::where('fullname', 'like', '%'.$data["keyword"].'%')
                            ->orWhere('email', 'like', '%'.$data["keyword"].'%')
                            ->orWhere('phone', 'like', '%'.$data["keyword"].'%')
                            ->orWhere('address', 'like', '%'.$data["keyword"].'%')
                            ->orWhere('technicalSkill', 'like', '%'.$data["keyword"].'%')
                            ->with(["jobs","interviews"])
                            ->paginate(10)
                );
            }
            if ($data["property"]!=null)
            {
                return response()->json(
                    Candidate::orderBy($data["property"], $data["orderby"])
                            ->with(["jobs","interviews"])
                            ->paginate(10)
                );
            }

            return response()->json($data);
        }
        else 
            return response()->json(Candidate::with(["jobs","interviews"])->paginate(10));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show a candidate by ID
     */
    public function show($candidateID)
    {
        $candidate = Candidate::with(["interviews","jobs"])->findOrFail($candidateID);
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Candidate $candidate)
    {
        //
    }
}
