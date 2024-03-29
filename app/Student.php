<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
	use SoftDeletes;

    protected $dates = ['deleted_at'];

    //Students cannot access the app
    //therefore no need for authentication

    public static $rules = array(
        'lrn'           =>'required|min:2|max:50',
    	'fname'		    =>'required|min:2|max:50',
        'lname'		    =>'required|min:2|max:50',
        'gradelevel'    =>'required|min:1|max:20',
        'spec_id'       =>'required|min:1|max:3',
        'section_id'    =>'required|min:1|max:3'
    );


    //This is for relationships in database
    public function section(){
        return $this->belongsTo('App\Section', 'section_id', 'id');
    }

    public function specialization(){
        return $this->belongsTo('App\Specialization', 'spec_id', 'id');
    }
}