<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Nette\InvalidArgumentException;
use Exception;

class UserReportController extends Controller
{
    public function usershow(Request $request)
    {
        try {
            //Below code used to get user's table value with ACL value mandatory in url path
            $aclvalue = $request->query('ACLValue');
            if (!$aclvalue) {
                return response()->json(['error' => 'ACL parameter is required'], 400);
            }
            $users = User::where('ACL', $aclvalue)->get();
            return response()->json(['users' => $users]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (\Exception $e) {
            return response()->json(['error' => 'An Unexpected error occurred'], 500);
        }
    }
}
