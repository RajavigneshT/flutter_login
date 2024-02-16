<?php

namespace App\Http\Controllers\webcontroller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserReportsController extends Controller
{
    
    public function usershow(){
        $users=User::all();
        $users = DB::table('users')->paginate(10);
        return view('show-users', ['users' => $users]);
    }
}