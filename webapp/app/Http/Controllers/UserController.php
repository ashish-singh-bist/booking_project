<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use JsValidator;
use App\User;

class UserController extends Controller
{   
    /*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles user related task in admin panel (create, edit, update and delete user).
    |
    */
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    //validation rules
    protected $validationRules = array(
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData()
    {
        $users = User::get();

        
        return Datatables::of($users)
            ->addColumn('action', function ($users) {
                $html =  '<a href="' . route('users.show', $users->id) . '" class="btn btn-xs btn-success" title="Show record"><i class="fa fa-eye"></i> Show</a>
                <a href="' . route('users.edit', $users->id) . '" class="btn btn-xs btn-warning" title="Edit record"><i class="fa fa-edit"></i> Edit</a>';

                $currentLoginUserId = auth()->user()->id;
                if($currentLoginUserId != $users->id){
                    $html .= ' <button type="button" title="Delete record" class="btn btn-xs btn-danger btn-delete" data-placement="left" data-remote="' . route('users.destroy', $users->id) . '"><span class="fa fa-trash-o" aria-hidden="true"></span> Delete</button>';
                }
                else{
                    $html .= ' <button type="button" disabled title="Delete record" class="btn btn-xs btn-danger btn-delete" data-placement="left" data-remote="' . route('users.destroy', $users->id) . '"><span class="fa fa-trash-o" aria-hidden="true"></span> Delete</button>';
                }
                return $html;
            })
            ->rawColumns([ 'action' ])
            ->make(true);
    }


    public function create()
    {
        $validator = JsValidator::make($this->validationRules, array(), array(), "form.create_user");

        return view('users.create', compact('validator'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate user data
        $this->validate($request, $this->validationRules, array());

        //create new user
        User::create($request->all());

        flash('User created successfully!')->success()->important();
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrfail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrfail($id);
        
        //Js validation for front-end
        $validationRules = array_except($this->validationRules, ['password', 'password_confirmation']);
        $validationRules['email'] = $validationRules['email'] . "," . $id;
        $validator = JsValidator::make($validationRules, array(), array(), "form.edit_user");

        return view('users.edit', compact('user', 'validator'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $input = $request->all(); 
        if($request->password == ''){
            $validationRules = array_except($this->validationRules, ['password', 'password_confirmation']);
            unset($input['password']);
        }
        else{
            $validationRules = $this->validationRules;
        }
        $validationRules['email'] = $validationRules['email'] . "," . $id;
        $validator = $this->validate($request, $validationRules, array());        

        // update user
        $user->fill($input);
        $user->save();

        flash('User updated successfully!')->success()->important();
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // check current login user
        $currentLoginUserId = auth()->user()->id;
        if($currentLoginUserId == $id){
            return response()->json([
                'status' =>false,
                'message' => "User can't delete itself !"
            ]);            
        }

        //delete user from db
        User::destroy($id);
        return response()->json([
            'status' =>true,
            'message' => 'User deleted successfully!'
        ]);
    }
}
