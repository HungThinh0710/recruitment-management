<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;
use App\Http\Requests\JobRequest;

/**
 * @group Job management
 *
 */
class JobController extends Controller
{
    /**
     * Display a listing of the job.
     * @bodyParam keyword string keyword want to search (search by name, description, position, address, salary, status, amount).
     * @bodyParam property string Field in table you want to sort(name, description, position,address, salary, experience, status,amount,publishOn,deadline). Example: name
     * @bodyParam orderby string The order sort (ASC/DESC). Example: asc
     */
    public function index(Request $request)
    {
        $orderby = $request->input('orderby')? $request->input('orderby'): 'desc';
        $jobs = Job::SearchByKeyWord($request->input('keyword'))
                        ->sort($request->input('property'),$orderby)
                        ->paginate(10);
        return response()->json($jobs);                               
    }

    public function create()
    {
        //
    }

    /**
     * Create the Job.
     *
     * @bodyParam name string required The name of job.
     * @bodyParam description string The description of job.
     * @bodyParam address string required Address of job.
     * @bodyParam position string required The position of job.
     * @bodyParam salary string required The salary of job.
     * @bodyParam status string required The status of job.
     * @bodyParam experience numeric required The experience of job. 1- 1 year;2- 2 years;3- 3 years;4: 4 years;5: 5 years; 6: more than 5 years. Example: 2
     * @bodyParam amount integer required The amount of job.
     * @bodyParam publishedOn datetime required The publishedOn date of job (Ex: 2019-07-10 00:00:00). Example: 2019-07-10 00:00:00
     * @bodyParam deadline datetime required The deadline of job (Ex: 2019-07-10 00:00:00). Example: 2019-07-10 00:00:00
     */

    public function store(JobRequest $request)
    {
        Job::create($request->except("created_at","updated_at"));
        return response()->json(['message'=>'Created a job successfully'],200);
    }

    /**
     * Show a job by ID for Recruitment-Webapp.
     *
     */
    public function showJobForCandidatesPage($idJob)
    {
        return response()->json(Job::findOrFail($idJob),200);
    }


    /**
     * Show a job by ID.
     *
     */
    public function show(Job $job, $idJob)
    {
        $job = Job::with(['articles','candidates'])->findOrFail($idJob);
        $job->experience = $job->convertExperiencetoString($job->experience);
        return response()->json($job,200);
    }

    public function edit(Job $job)
    {
        //
    }

    /**
     * Update the Job by ID.
     *
     * @bodyParam name string required The name of job.
     * @bodyParam description string The description of job.
     * @bodyParam address string required Address of job.
     * @bodyParam position string required The position of job.
     * @bodyParam salary string required The salary of job.
     * @bodyParam status string required The status of job.
     * @bodyParam experience numeric required The experience of job. 1- 1 year;2- 2 years;3- 3 years;4: 4 years;5: 5 years; 6: more than 5 years. Example: 2
     * @bodyParam amount string required The amount of job.
     * @bodyParam publishedOn datetime required The publishedOn date of job (Ex: 2019-07-10 00:00:00). Example: 2019-07-10 00:00:00
     * @bodyParam deadline datetime required The deadline of job (Ex: 2019-07-10 00:00:00). Example: 2019-07-10 00:00:00
     */
    public function update(JobRequest $request, Job $job, $idJob)
    {
        $job = Job::findOrFail($idJob)->update($request->except("updated_at","created_at"));
        return response()->json(['message'=>'Updated job successfully'],200);
    }

    /**
     * Remove a job/many jobs by ID.
     *
     * @bodyParam jobID array required The id/list id of job. Example: [1,2,3,4,5]
     */
    public function destroy(JobRequest $request)
    {
        $jobIds = $request->jobId;
        $exists = Job::whereIn('id', $jobIds)->pluck('id');
        $notExists = collect($jobIds)->diff($exists);

        //Get list id not found from array to var.
        $idsNotFound = "";
        foreach ($notExists as $key => $value) {
            $idsNotFound .= $value.",";
        }

        if($notExists->isNotEmpty()){
            return response()->json([
                'message'=>'Not found id: '.substr($idsNotFound,0,strlen($idsNotFound)-1)],404);
        }

        Job::whereIn('id', $jobIds)->delete();
        return response()->json([
           'message'=>'Deleted job successfully'],200);
    }
}
