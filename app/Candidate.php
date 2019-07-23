<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'candidates';
    protected $fillable= ["fullname","email","address","phone","technicalSkill","description","CV","status"];
    public function jobs()
    {
        return $this->belongsToMany('App\Job', 'job_candidate', 'candidateId', 'jobId');
    }
    public function interviews()
    {
        return $this->belongsToMany('App\Interview', 'candidate_interview', 'candidateId', 'interviewId');
    }
    public function scopeSearchByKeyWord($query, $keyword)
    {   
        if ($keyword) 
            return $query->where('fullname', 'like', '%'.$keyword.'%')
                                ->orWhere('email', 'like', '%'.$keyword.'%')
                                ->orWhere('phone', 'like', '%'.$keyword.'%')
                                ->orWhere('address', 'like', '%'.$keyword.'%')
                                ->orWhere('technicalSkill', 'like', '%'.$keyword.'%');    
    }
    public function scopeSort($query, $field, $orderBy)
    {   
        if ($field) 
            return $query->orderBy($field, $orderBy);    
    }
}
