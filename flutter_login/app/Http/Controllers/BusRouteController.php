<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use App\Models\CreateBusRouteModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BusRouteController extends Controller
{
    public function createbusroute(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // Validate the request data
            $validator = Validator::make($request->all(), [
                'N_BusRoute_From' => 'required',
                'N_BusRoute_To' => 'required',
                'N_BusRouteFare_Amount' => 'required',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                // Log the specific fields that caused the validation to fail in a single log entry
                $failedFields = implode(', ', $validator->errors()->keys());
                Log::info("Validation failed for fields: $failedFields");

                throw new ValidationException($validator);
            }

            // Create the child record
            $createBusRoute = CreateBusRouteModel::create([
                'N_BusRoute_From' => $request->N_BusRoute_From,
                'N_BusRoute_To' => $request->N_BusRoute_To,
                'N_BusRouteFare_Amount' => $request->N_BusRouteFare_Amount,
            ]);

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json(['Route Added successfully' => true, 'child' => $createBusRoute], 200);
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
