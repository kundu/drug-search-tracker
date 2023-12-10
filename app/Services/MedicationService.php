<?php

namespace App\Services;

use App\Exceptions\DrugNotFoundException;
use App\Models\User;
use App\Models\Medication;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MedicationService
{
    /**
     * Add a new drug to the user's medication list.
     *
     * @param User $user The user to whom the drug will be added.
     * @param string $rxcui The RXCUI identifier of the drug.
     * @return void
     * @throws DrugNotFoundException If the drug with the specified RXCUI is not found.
     */
    public function addDrug(User $user, string $rxcui) : void
    {
        $drugData = (new RxNavService())->getAdditionalDrugData($rxcui);
        $drugData = $drugData['rxcuiStatusHistory'];

        if ($drugData['metaData']['status'] != 'Active')
            throw new DrugNotFoundException("Drug(Rxcui) not found");

        // Extracting 'ingredientAndStrength' and 'doseFormGroupName'
        $ingredientAndStrength = array_map(function ($item) {
            return [
                'baseName' => $item['baseName'],
                'strength' => $item['numeratorValue'] . ' ' . $item['numeratorUnit']
            ];
        }, $drugData['definitionalFeatures']['ingredientAndStrength'] ?? []);

        $doseFormGroupName = array_map(function ($item) {
            return $item['doseFormGroupName'];
        }, $drugData['definitionalFeatures']['doseFormGroupConcept'] ?? []);

        // Create or update the Medication instance
        $medication = Medication::updateOrCreate(
            ['rxcui' => $rxcui],
            [
                'drug_name' => $drugData['attributes']['name'],
                'base_names' => $ingredientAndStrength,
                'dosage_forms' => $doseFormGroupName
            ]
        );

        // Associate the medication with the user
        $user->medications()->syncWithoutDetaching([$medication->id]);
    }


    /**
     * Delete a medication from the user's list.
     *
     * @param User $user The authenticated user.
     * @param int $medicationId The ID of the medication to delete.
     * @throws ModelNotFoundException If the medication is not found.
     * @throws AuthorizationException If the medication does not belong to the user.
     * @return void
     */
    public function deleteDrug(User $user, int $medicationId) : void
    {
        $medication = Medication::find($medicationId);

        if (!$medication)
            throw new ModelNotFoundException('Medication not found.');

        if (!$user->medications()->find($medicationId))
            throw new AuthorizationException('This action is unauthorized.');

        $user->medications()->detach($medicationId);
    }

    /**
     * Retrieve all drugs from the user's medication list.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserDrugs(User $user) : Collection
    {
        return $user->medications;
    }
}
