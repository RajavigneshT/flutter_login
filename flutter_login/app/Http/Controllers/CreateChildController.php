<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\CreateChildModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class CreateChildController extends Controller
{
    public function createChild(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'ChildName' => 'required',
                'SchoolName' => 'required',
                'BusRoute' => 'required',
                'ParentName' => 'required',
                'Address' => 'required',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                // Log the specific fields that caused the validation to fail in a single log entry
                $failedFields = implode(', ', $validator->errors()->keys());
                Log::info("Validation failed for fields: $failedFields");

                throw new ValidationException($validator);
            }

            // Create the child record
            $createChild = CreateChildModel::create([
                'user_id' => $request->user_id,
                'ChildName' => $request->ChildName,
                'Gender' => $request->Gender,
                'SchoolName' => $request->SchoolName,
                'BusRoute' => $request->BusRoute,
                'ParentName' => $request->ParentName,
                'ContactNo' => $request->ContactNo,
                'Address' => $request->Address,
            ]);

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json(['Child Added successfully' => true, 'child' => $createChild], 200);
        } catch (ValidationException $e) {
            // Handle validation errors and log the specific fields that caused the failure
            $errors = $e->errors();

            return response()->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['message' => 'Error: The ChildName Must Be unique. '], 422);
            } else {
                Log::error("SQL Exception: " . $e->getMessage());
                return response()->json(['message' => 'Error occurred. SQL Exception.'], 500);
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Handle other exceptions
            Log::error("Unexpected Exception: " . $e->getMessage());

            return response()->json(['message' => 'Error occurred. Transaction rolled back.'], 500);
        }
    }

    public function updatechild(Request $request,$id)
    {
        try {
            DB::beginTransaction();

            $validator =validator::make($request->all(),[
                //'user_id'=>'required|exists:users,id',
                'ChildName' => 'required',
                'Gender'=>'required',
                'SchoolName' => 'required',
                'BusRoute' => 'required',
                'ParentName' => 'required',
                'Address' => 'required',
            ]);
            if($validator->fails()){
                $failedFields = implode(', ', $validator->errors()->keys());
                Log::info("Validation failed for fields: $failedFields");
                throw new ValidationException($validator);
            }
            //Log::info($validator);
            $createChild=CreateChildModel::find($id);

            if(!$createChild){
                return response()->json(['message'=>'User Not Found'],400);
            }

            $attributes = [
                'ChildName', 'Gender', 'SchoolName', 'BusRoute', 'ParentName', 'Address'
            ];

            $updateRequired = false;

            foreach ($attributes as $attribute) {
                Log::info($attribute);

            if ($request->filled($attribute) && $request->input($attribute) != $createChild->$attribute) {
            
                $updateRequired = true;
                break;
            }
        }
             if($updateRequired){
            $createChild->ChildName=$request->input('ChildName');
            $createChild->Gender=$request->input('Gender');
            $createChild->SchoolName=$request->input('SchoolName');
            $createChild->BusRoute=$request->input('BusRoute');
            $createChild->ParentName=$request->input('ParentName');
            $createChild->Address=$request->input('Address');
            $createChild->save();
             }else{
                return response()->json(['message'=>'You did not updated anything !'],401);
             }
            DB::commit();

            Log::info($createChild);
            return response()->json(['Child updated  successfully' => true, 'child' => $createChild], 200);

        }catch (ValidationException $e) {
            // Handle validation errors and log the specific fields that caused the failure
            $errors = $e->errors();

            return response()->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['message' => 'Error: The ChildName Must Be unique. '], 422);
            } else {
                Log::error("SQL Exception: " . $e->getMessage());
                return response()->json(['message' => 'Error occurred. SQL Exception.'], 500);
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Handle other exceptions
            Log::error("Unexpected Exception: " . $e->getMessage());

            return response()->json(['message' => 'Error occurred. Transaction rolled back.'], 500);
        }
    }

        public function deletechild(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            
            $createChild=CreateChildModel::find($id);
            if(!$createChild)
            {
                return response()->json(['message'=>'User Not Found'],515);
            }else
            {
                $createChild->delete();
            }
            DB::commit();
            return response()->json(['message'=>'Child Deleted successfully']);

        }catch (ValidationException $e) {
            // Handle validation errors and log the specific fields that caused the failure
            $errors = $e->errors();

            return response()->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return response()->json(['message' => 'Error: The ChildName Must Be unique. '], 422);
            } else {
                Log::error("SQL Exception: " . $e->getMessage());
                return response()->json(['message' => 'Error occurred. SQL Exception.'], 500);
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Handle other exceptions
            Log::error("Unexpected Exception: " . $e->getMessage());

            return response()->json(['message' => 'Error occurred. Transaction rolled back.'], 500);
        }
    }
        
}
