<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(Route::getCurrentRoute()->uri() !== 'profile') {
            $this->middleware('guest');
        }

    }

    public static function showRegistrationForm()
    {
        $arFields = [
            [
                'field_label' => 'First Name',
                'field_slug' => 'first_name',
                'field_type' => 'text'
            ],
            [
                'field_label' => 'Last Name',
                'field_slug' => 'last_name',
                'field_type' => 'text'
            ],
            [
                'field_label' => 'Email',
                'field_slug' => 'email',
                'field_type' => 'email'
            ],

            [
                'field_label' => 'Category',
                'field_slug' => 'type',
                'field_type' => 'radio',
                'values' => [
                    'personal' => 'Personal',
                    'company' => 'Company'
                ]
            ],
            [
                'field_label' => 'Company Name',
                'field_slug' => 'company_name',
                'field_type' => 'text',
                'company_only' => true
            ],
            [
                'field_label' => 'Company Position',
                'field_slug' => 'company_position',
                'field_type' => 'text',
                'company_only' => true
            ],
            [
                'field_label' => 'Company CIF',
                'field_slug' => 'cif',
                'field_type' => 'text',
                'company_only' => true
            ],
            [
                'field_label' => 'Password',
                'field_slug' => 'password',
                'field_type' => 'password'
            ],
            [
                'field_label' => 'Confirm Password',
                'field_slug' => 'password_confirmation',
                'field_type' => 'password'
            ],

        ];
        return view('auth.register', ['arFields' => $arFields]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $isEdit = auth()->check();
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'type' => [$isEdit ? 'nullable' : 'required', Rule::in(['personal', 'company'])]
        ];
        if(($isEdit && auth()->user()->type === 'company') || (!$isEdit && $data['type'] === 'company')){
            $rules = array_merge($rules, [
                    'company_name' => ['required', 'string', 'max:255'],
                    'company_position' => ['required', 'string', 'max:255'],
                    'cif' => ['required', 'string', 'max:255', 'unique:users'],
                ]
            );
        }
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $isEdit = auth()->check();
        $data = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'company_name' => $data['company_name'],
            'company_position' => $data['company_position'],
            'cif' => $data['cif'],
            'type' => $data['type'],
            'password' => Hash::make($data['password']),
        ];
        if($isEdit){
            unset($data['type']);
        }
        return $isEdit ? User::where('id', auth()->user()->id)->update($data) : User::create($data);
    }
}
