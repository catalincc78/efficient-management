<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public static function viewProfile()
    {
        if (auth()->user()->type === 'company') {
            $user = User::where('id', auth()->user()->id)->select('first_name', 'last_name', 'email', 'cif', 'company_name', 'company_position', 'type')->first();
        } else {
            $user = User::where('id', auth()->user()->id)->select('first_name', 'last_name', 'email', 'type')->first();
        }

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

        if ($user->type === 'company') {
            $arFields = array_merge($arFields, [
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
                ]
            ]);
        }
        return view('profile', ['user' => $user, 'arFields' => $arFields]);
    }

    public static function editProfile(){

    }
}
