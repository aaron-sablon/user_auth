<?php

namespace App\Http\Controllers;

use App\Section;
use App\Professor;
use App\Specialization;
use App\Student;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct( Request $request )
    {
        $this->params = config('app.params');
        $this->params['is_logged'] = true;
        $this->params['forced_login'] = false;
        $this->params['token'] = $request->token;
    }

    public function index(){
        $sections = Section::paginate(5);
        $this->params['sections'] = $sections;
        //dd($sections);
         return view('section.index', $this->params);
    }

    public function show($id){
        $section = Section::find($id);
       
        $this->params['sections'] = $section;

        //dd( $section);
        return view('section.show', $this->params);
    }

    public function restore(Request $request, $id){
        $section = Section::onlyTrashed()->find($id);
        $section->restore();
        return redirect()->route('section.index')->with('Success','Information restored.');
    }    


    public function create(){
        $specializations = Specialization::all();
        $this->params['specializations'] = $specializations;
        return view('section.create', $this->params);
    }

    public function store(Request $request){

        $rules=Section::$rules;
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
        $section= new Section;
        $section->grade=INPUT::get('section_grade');
        $section->name =INPUT::get('name');
        $section->save();
        $this->params['msg']='Section was created successfully.';

        return redirect()->route('sections.index')
                        ->with( $this->params);
    }

    public function update(Request $request, $id){
    	$rules=Section::$rules;
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
    $section = Section::find($id);
    $section->name =INPUT::get('name');
    $section->save();
    $this->params['msg']='Information updated successfully.';
    return redirect()->route('section.index')->with($this->params);
    }

    public function destroy($id){
        $section = Section::find($id);
        $section->delete();
        $this->params['msg']='Section was removed successfully.';
        return redirect()->route('section.index')->with($this->params);
    }
}