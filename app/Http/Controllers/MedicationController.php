<?php

namespace App\Http\Controllers;

use App\Exceptions\DrugNotFoundException;
use App\Http\Requests\AddDrugRequest;
use App\Services\MedicationService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MedicationController extends Controller
{
    protected $medicationService;

    /**
     * Create a new MedicationController instance.
     *
     * @param MedicationService $medicationService
     */
    public function __construct(MedicationService $medicationService)
    {
        $this->medicationService = $medicationService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Handle a request to add a drug to a user's medication list.
     *
     * @param AddDrugRequest $request The validated request containing the RXCUI.
     * @return \Illuminate\Http\JsonResponse A success response or an error response.
     */
    public function addDrug(AddDrugRequest $request)
    {
        try {
            $this->medicationService->addDrug($request->user(), $request->rxcui);
            return apiResponse(200, 'Drug added successfully');
        } catch (DrugNotFoundException $drugNotFoundException) {
            return apiResponse(404, "Drug not found!");
        }catch (Exception $exception) {
            Log::error("Error", [$exception]);
            return apiResponse(500, "Internal server error");
        }
    }

    /**
     * Delete a drug from the authenticated user's medication list.
     *
     * @param int $medicationId The ID of the medication to delete.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDrug(int $medicationId)
    {
        try {
            $this->medicationService->deleteDrug(Auth::user(), $medicationId);
            return apiResponse(200, 'Drug removed successfully');
        } catch (ModelNotFoundException $modelNotFoundException) {
            return apiResponse(404, 'Medication not found');
        } catch (AuthorizationException $authorizationException) {
            return apiResponse(403, 'Unauthorized action');
        } catch (Exception $exception) {
            Log::error("Error", [$exception]);
            return apiResponse(500, 'Internal server error');
        }
    }

    /**
     * Retrieve all drugs from the authenticated user's medication list.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDrugs()
    {
        try {
            $medications = $this->medicationService->getUserDrugs(Auth::user());
            return apiResponse(200, 'User medications retrieved', ['medications' => $medications]);
        } catch (Exception $exception) {
            Log::error("Error", [$exception]);
            return apiResponse(500, "Internal server error");
        }
    }
}
