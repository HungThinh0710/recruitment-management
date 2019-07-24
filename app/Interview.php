<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{

    protected $table = 'interviews';
    public $fillable = ["name", "timeStart", "address", "interviewerId"];
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function candidates()
    {
        return $this->belongsToMany("App\Candidate","candidate_interview", "interviewId", "candidateId");
    }
}
