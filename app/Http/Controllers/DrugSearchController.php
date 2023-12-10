<?php

namespace App\Http\Controllers;

use App\Services\RxNavService;
use Exception;
use Illuminate\Support\Facades\Log;

class DrugSearchController extends Controller
{

    public function __construct(protected RxNavService $rxNavService)
    {
        $this->rxNavService = $rxNavService;
    }


    /**
     * Search for drugs by name using the RxNav API.
     *
     * @param string $name The name of the drug to search for.
     * @return \Illuminate\Http\JsonResponse A standardized JSON response containing
     *                                       the search results or an error message.
     */
    public function search($name)
    {
        try {
            $results = $this->rxNavService->getDrugsByName($name);
            return apiResponse(200, "Search Results", ['search_result' => $results]);
        } catch (Exception $exception) {
            Log::error("Error", [$exception]);
            return apiResponse(500, "Internal server error");
        }
    }
}
