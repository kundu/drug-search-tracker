<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Constants\RxNavApiConstants;
use Exception;
use Illuminate\Support\Facades\Cache;

class RxNavService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Get drugs by name from the RxNav API with caching.
     *
     * @param string $name The name of the drug to search for.
     * @return array The response from the RxNav API as an associative array.
     * @throws Exception If an HTTP error occurs.
     */
    public function getDrugsByName(string $name): array
    {
        $cacheDuration = config('rxnav.cache_duration') * 60;

        return Cache::remember("rxnav_drugs_{$name}", $cacheDuration, function () use ($name) {
            try {
                $response = $this->client->get(config('rxnav.base_url') .RxNavApiConstants::GET_DRUGS_ENDPOINT . urlencode($name));
                $data = json_decode($response->getBody()->getContents(), true);
                return $this->processDrugData($data);
            } catch (Exception $exception) {
                throw $exception;
            }
        });
    }

    /**
     * Process the drug data from the RxNav API response.
     *
     * @param array $data The response data from the RxNav API.
     * @return array Processed drug data.
     */
    protected function processDrugData(array $data): array
    {
        $results = [];

        if (isset($data['drugGroup']['conceptGroup'])) {
            foreach ($data['drugGroup']['conceptGroup'] as $group) {
                if ($group['tty'] === 'SBD' && isset($group['conceptProperties'])) {
                    foreach (array_slice($group['conceptProperties'], 0, 5) as $concept) {
                        $additionalData = $this->getAdditionalDrugData($concept['rxcui']);
                        $additionalData = $this->extractAdditionalData($additionalData);
                        $results[] = [
                            'rxcui' => $concept['rxcui'],
                            'name' => $concept['name'],
                            'ingredientAndStrength' => $additionalData['ingredientAndStrength'] ?? [],
                            'doseFormGroupName' => $additionalData['doseFormGroupName'] ?? []
                        ];
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Get additional drug data from the RxNav API's historystatus endpoint.
     *
     * @param string $rxcui The RxNorm Concept Unique Identifier.
     * @return array The additional drug data.
     * @throws Exception If an HTTP error occurs.
     */
    public function getAdditionalDrugData(string $rxcui): array
    {
        try {
            $endpoint = sprintf(RxNavApiConstants::HISTORY_STATUS_ENDPOINT, $rxcui);
            $response = $this->client->get(config('rxnav.base_url') . $endpoint);
            $data = json_decode($response->getBody()->getContents(), true);

            return $data;
        } catch (Exception $exception) {
            throw $exception;
        }
    }


    /**
     * Extract additional data from the RxNav API response.
     *
     * @param array $data The response data from the RxNav API.
     * @return array Extracted additional data.
     */
    protected function extractAdditionalData(array $data): array
    {
        $additionalData = [];
        if (isset($data['rxcuiStatusHistory']['definitionalFeatures'])) {
            $features = $data['rxcuiStatusHistory']['definitionalFeatures'];

            $additionalData['ingredientAndStrength'] = array_map(function ($item) {
                return [
                    'baseName' => $item['baseName'],
                    'strength' => $item['numeratorValue'] . ' ' . $item['numeratorUnit']
                ];
            }, $features['ingredientAndStrength'] ?? []);

            $additionalData['doseFormGroupName'] = array_map(function ($item) {
                return $item['doseFormGroupName'];
            }, $features['doseFormGroupConcept'] ?? []);
        }

        return $additionalData;
    }




}
