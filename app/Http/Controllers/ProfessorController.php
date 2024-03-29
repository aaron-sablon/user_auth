<?php

namespace App\Http\Controllers;

use App\Professor;
use App\Section;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function __construct( Request $request )
    {
        $this->params = config('app.params');
        $this->params['is_logged'] = true;
        $this->params['forced_login'] = false;
        $this->params['token'] = $request->token;
    }
    

    //index page 

    public function index(){
    	$professors = Professor::paginate(10);

    	$this->params['professors'] = $professors;
    	//dd($professors);
    	 return view('professor.index', $this->params);
    }

    //show the professors tab
    public function show($id){
        $professors = Professor::find($id);
        $this->params=['professor' => $professor];

        // dd( $albums->user->id);
        return view('professor.show', $this->params);

    }

    //undo delete from databse
    public function restore(Request $request, $id){
        $professors = Professor::onlyTrashed()->find($id);
        $professors->restore();
        //no route yet
        return redirect()->route('professor.index')->with('Success','Professor was restored.');
    }

    //CRUDE
    public function create(){
        $sections = Section::all();
        $this->params['sections'] = $sections;
        return view('professor.create', $this->params);
    }

    public function store(Request $request){

        $rules=Professor::$rules;
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
        $professors= new Professor;
        //$professors->id =INPUT::get('id');
        $professors->fname =INPUT::get('fname');
        $professors->lname =INPUT::get('lname');
        $professors->contact =INPUT::get('contact');
        $professors->section_id =INPUT::get('section_id');
       

        
        $professors->save();
        $this->params['msg']='Room was created successfully.';

        return redirect()->route('professors.index')
                        ->with( $this->params);
    }

    public function update(Request $request, $id){
 		$rules=Professor::$rules;
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
        $professors = Professor::find($id);
        $professors->fname =INPUT::get('fname');
        $professors->lname =INPUT::get('lname');
        $professors->advisory=INPUT::get('advisory');
        $professors->contact=INPUT::get('contact');
        
        $professors->save();

        $this->params['msg']='Information updated successfully.';
        //no route yet
        return redirect()->route('professor.index')->with($this->params);

    }

    public function destroy($id){
    	$professors = Professor::find($id);
        $professors->delete();

        $this->params['msg']='Professor was removed successfully.';
        //no route yet
        return redirect()->route('professor.index')->with($this->params);

    }

    //end of CRUDE

}