<?php

namespace App\Http\Controllers;

use App\Schedule;
use App\Subject;
use App\Time;
use App\Room;
use App\Professor;
use App\Section;
use App\Specialization;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
     public function __construct( Request $request )
    {
        $this->params = config('app.params');
        $this->params['is_logged'] = true;
        $this->params['forced_login'] = false;
        $this->params['token'] = $request->token;
    }
    public function index(){
        $sections = Section::paginate(10);
        $this->params['sections'] = $sections;
        // $schedules = Schedule::paginate(10);
        // $this->params['schedules'] = $schedules;
         return view('schedule.index', $this->params);
    }

    public function show($id) {

        // $subjects = Schedule::find($id)->subject;
        // $times = Schedule::find($id)->time;
        // $professors = Schedule::find($id)->professor;
        // $sections = Schedule::find($id)->section;
        // $rooms = Schedule::find($id)->room;

        // $subjects = Subject::with('schedule')->find($id);
        // $times = Time::with('schedule')->find($id);
        // $professors = Professor::with('schedule')->find($id);
        // $sections = Section::with('schedule')->find($id);
        // $rooms = Room::with('schedule')->find($id);

        // $schedules = Schedule::find($id);

        $subjects = Subject::all();
        $times = Time::all();
        $professors = Professor::all();
        $sections = Section::find($id);
        $rooms = Room::all();
        $schedules = Schedule::all();
        $var2 = Schedule::with('section')->find($id);
        $var1 = Schedule::with('subject','time','room','professor', 'section')->where('section_id', 1 )->get();
        $varname = Section::with('schedule')->find($id);

        $this->params=[
            'subjects' => $subjects,
            'slots' => $times,
            'rooms' => $rooms,
            'professors' => $professors,
            'sections' => $sections,
            'schedules' => $schedules,
            'var1'  => $var1,
            'varname'   => $varname
        ];
        dd($var2);
        return view('schedule.show', $this->params);
    }

    //undo delete from databse
    public function restore(Request $request, $id){
        $schedules = Schedule::onlyTrashed()->find($id);
        $schedules->restore();
        return redirect()->route('schedule.index')->with('Success','Information restored.');
    }    

    //CRUDE
     public function create(){
        $sections = Section::all();
        $specializations = Specialization::all();
        $slots = Time::all();
        $rooms = Room::all();
        $professors = Professor::all();
        $subjects = Subject::all();
        $this->params['sections'] = $sections;
        $this->params['specializations'] = $specializations;
        $this->params['slots'] = $slots;
        $this->params['rooms'] = $rooms;
        $this->params['professors'] = $professors;
        $this->params['subjects'] = $subjects;
        return view('schedule.create', $this->params);
    }
    //NEED FOR CREATE
    //EDIT
    public function store(Request $request){

        $rules=Schedule::$rules;
        $validator = Validator::make(
            Input::all(),
            $rules
        );

        // If validator fails.
        if ( $validator->fails() ) {
            
            $error_messages = $validator->messages()->getMessages();
            $this->params['error'] = true;
            $this->params['msg'] = 'Form validation error. Please fix.';
            $this->params['form_errors'] = $error_messages;

            return redirect()->back()->with($this->params);
        }
        $schedules= new Schedule;
        $schedules->subject_id =INPUT::get('subject');
        $schedules->time_id =INPUT::get('slot');
        $schedules->room_id =INPUT::get('room');
        $schedules->prof_id =INPUT::get('professor');
        $schedules->section_id =INPUT::get('section');
        
        $schedules->save();
        $this->params['msg']='Schedule was created successfully.';

        return redirect()->route('schedules.index')
                        ->with( $this->params);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::all();
        $schedules = Schedule::with('time')->find($id);
        $this->params=[
            'users'=>$users,
            'album'=>$album
        ];
        
        return view('schedule.edit', $this->params);
    }

/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id){
        $rules=Schedule::$rules;
        $validator = Validator::make(
            Input::all(),
            $rules
        );

        // If validator fails.
        if ( $validator->fails() ) {
            
            $error_messages = $validator->messages()->getMessages();
            $this->params['error'] = true;
            $this->params['msg'] = 'Form validation error. Please fix.';
            $this->params['form_errors'] = $error_messages;

            return redirect()->back()->with($this->params);
        }
        $schedules = Schedule::find($id);
        $schedules->fname =INPUT::get('fname');
        $schedules->lname =INPUT::get('lname');
        $schedules->advisory=INPUT::get('advisory');
        $schedules->contact=INPUT::get('contact');
        
        $schedules->save();

        $this->params['msg']='Schedule updated successfully.';
        return redirect()->route('schedule.index')->with($this->params);

    }

    public function destroy($id){
        $schedules = Schedule::find($id);
        $schedules->delete();

        $this->params['msg']='Schedule was removed successfully.';
        //no route yet
        return redirect()->route('schedule.index')->with($this->params);

    }
    //end of CRUDE
}