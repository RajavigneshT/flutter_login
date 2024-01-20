<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\CreateChildModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
                'SchoolName' => $request->SchoolName,
                'BusRoute' => $request->BusRoute,
                'ParentName' => $request->ParentName,
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
            // Handle SQL exceptions
            Log::error("SQL Exception: " . $e->getMessage());

            return response()->json(['message' => 'Error occurred. SQL Exception.'], 500);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Handle other exceptions
            Log::error("Unexpected Exception: " . $e->getMessage());

            return response()->json(['message' => 'Error occurred. Transaction rolled back.'], 500);
        }
    }
}
