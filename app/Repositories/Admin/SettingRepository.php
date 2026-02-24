<?php

namespace App\Repositories\Admin;

use App\Models\Charge;
use App\Models\CodCharge;
use App\Models\PackageAndCharge;
use App\Models\Setting;
use App\Models\Merchant;
use App\Repositories\Interfaces\Admin\SettingInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingRepository implements SettingInterface
{
    public function store($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->except('_token') as $key => $value):
                $setting = Setting::where('title', $key)->first();
                if ($setting == "" || $setting == null):
                    $setting = new Setting();
                    $setting->title = $key;
                    $setting->value = $value;
                else:
                    $setting->value = $value;
                endif;
                $setting->save();
            endforeach;

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Setting store error: ' . $e->getMessage());
            return false;
        }
    }

    public function arrayStore($request)
    {
        DB::beginTransaction();
        try {
            $array = [];
            $title = '';

            if (isset($request->packaging_types)):
                $title = 'package_types_and_charge';
                foreach ($request->packaging_types as $key => $type):
                    if (!blank($type) && !blank($request->charges[$key])):
                        $array += [$type => $request->charges[$key]];
                    endif;
                endforeach;
            endif;

            $setting = Setting::where('title', $title)->first();

            if ($title != ''):
                if ($setting == "" || $setting == null):
                    $setting = new Setting();
                    $setting->title = $title;
                    $setting->array_value = $array;
                else:
                    $setting->array_value = $array;
                endif;

                $setting->save();
            endif;

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Setting arrayStore error: ' . $e->getMessage());
            return false;
        }
    }

    public function packingCharge()
    {
        try {
            $package_and_charges = PackageAndCharge::all();
            return $package_and_charges;
        } catch (\Exception $e) {
            Log::error('Packing charge error: ' . $e->getMessage());
            return false;
        }
    }

    public function deletePackagingCharge($id)
    {
        Log::info('Delete packaging charge called with ID: ' . $id);
        
        DB::beginTransaction();
        try {
            $record = PackageAndCharge::find($id);
            
            if (!$record) {
                Log::warning('Record not found for deletion: ' . $id);
                DB::rollback();
                return false;
            }
            
            Log::info('Found record to delete:', $record->toArray());
            
            $deleted = $record->delete();
            
            DB::commit();
            Log::info('Delete result: ' . ($deleted ? 'success' : 'failed'));
            
            return $deleted;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Delete exception: ' . $e->getMessage());
            return false;
        }
    }

    public function packingChargeAdd()
    {
        DB::beginTransaction();
        try {
            $package_charge = new PackageAndCharge();
            $package_charge->save();
            DB::commit();
            return $package_charge->id;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Packing charge add error: ' . $e->getMessage());
            return false;
        }
    }

public function packagingChargeUpdate($request)
{
    \Log::info('========== REPOSITORY: PACKAGING CHARGE UPDATE STARTED ==========');
    \Log::info('Request data received:', [
        'all' => $request->all(),
        'has_packaging_types' => isset($request->packaging_types),
        'has_ids' => isset($request->ids),
        'has_charges' => isset($request->charges)
    ]);
    
    DB::beginTransaction();
    try {
        // Check if the required data exists
        if (!isset($request->packaging_types) || !isset($request->ids) || !isset($request->charges)) {
            \Log::error('Missing required data in request');
            DB::rollback();
            return false;
        }
        
        $packaging_types = $request->packaging_types;
        $ids = $request->ids;
        $charges = $request->charges;
        
        \Log::info('Processing data:', [
            'packaging_types_count' => count($packaging_types),
            'packaging_types' => $packaging_types,
            'ids_count' => count($ids),
            'ids' => $ids,
            'charges_count' => count($charges),
            'charges' => $charges
        ]);
        
        $recordsProcessed = 0;
        $recordsCreated = 0;
        $recordsUpdated = 0;
        
        foreach ($packaging_types as $key => $type):
            $id = $ids[$key] ?? null;
            $charge = $charges[$key] ?? null;
            
            \Log::info('========== Processing Item ' . $key . ' ==========');
            \Log::info('Item data:', [
                'key' => $key,
                'type' => $type,
                'id' => $id,
                'charge' => $charge
            ]);
            
            // Skip if type is empty
            if (empty($type)) {
                \Log::info('Type is empty, skipping item');
                continue;
            }
            
            // Skip if charge is empty
            if (empty($charge) && $charge !== 0) {
                \Log::info('Charge is empty, skipping item');
                continue;
            }
            
            $recordsProcessed++;
            
            // Check for existing record with same package_name
            \Log::info('Checking for existing record with package_name: ' . $type);
            $query = PackageAndCharge::where('package_name', $type);
            
            if (!empty($id)) {
                \Log::info('Excluding ID: ' . $id);
                $query->where('id', '!=', $id);
            }
            
            $existingRecord = $query->first();
            
            \Log::info('Existing record check:', [
                'exists' => !blank($existingRecord),
                'existing_id' => $existingRecord ? $existingRecord->id : null,
                'existing_data' => $existingRecord ? $existingRecord->toArray() : null
            ]);
            
            // If no duplicate found, create or update
            if (blank($existingRecord)):
                \Log::info('No duplicate found - proceeding with save');
                
                // Find existing or create new
                $table = null;
                if (!empty($id)) {
                    \Log::info('Looking for existing record with ID: ' . $id);
                    $table = PackageAndCharge::find($id);
                    if ($table) {
                        \Log::info('Found existing record:', $table->toArray());
                        $recordsUpdated++;
                    } else {
                        \Log::info('No record found with ID: ' . $id);
                    }
                }
                
                if (blank($table)):
                    \Log::info('Creating NEW record');
                    $table = new PackageAndCharge();
                    $recordsCreated++;
                else:
                    \Log::info('UPDATING existing record ID: ' . $table->id);
                endif;
                
                // Log before setting values
                \Log::info('Setting values:', [
                    'package_name' => $type,
                    'same_day' => $charge,
                    'weight' => $table->weight ?? 0,
                    'next_day' => $table->next_day ?? 0,
                    'sub_city' => $table->sub_city ?? 0,
                    'outside_city' => $table->outside_city ?? 0
                ]);
                
                // Set the values
                $table->package_name = $type;
                $table->same_day = $charge;
                
                // Set default values for new records
                if (blank($id)) {
                    \Log::info('Setting default values for new record');
                    $table->weight = 0;
                    $table->next_day = 0;
                    $table->sub_city = 0;
                    $table->outside_city = 0;
                    $table->status = 1;
                }
                
                // Log before save
                \Log::info('Attempting to save record:', $table->toArray());
                
                try {
                    $saved = $table->save();
                    \Log::info('Save result: ' . ($saved ? 'SUCCESS' : 'FAILED'));
                    
                    if ($saved) {
                        \Log::info('Record saved successfully with ID: ' . $table->id);
                        // Verify it was saved by fetching it again
                        $verify = PackageAndCharge::find($table->id);
                        \Log::info('Verification - Record in database:', $verify ? $verify->toArray() : 'NOT FOUND');
                    } else {
                        \Log::error('Save method returned false');
                    }
                } catch (\Exception $saveException) {
                    \Log::error('Exception during save: ' . $saveException->getMessage());
                    throw $saveException;
                }
                
            elseif (!empty($id)):
                \Log::info('Duplicate found, deleting record ID: ' . $id);
                $deleted = $this->deletePackagingCharge($id);
                \Log::info('Delete result: ' . ($deleted ? 'success' : 'failed'));
            endif;
            
            \Log::info('========== Finished Processing Item ' . $key . ' ==========');
        endforeach;
        
        \Log::info('Summary:', [
            'records_processed' => $recordsProcessed,
            'records_created' => $recordsCreated,
            'records_updated' => $recordsUpdated
        ]);
        
        DB::commit();
        \Log::info('========== REPOSITORY: TRANSACTION COMMITTED ==========');
        \Log::info('========== REPOSITORY: PACKAGING CHARGE UPDATE SUCCESSFUL ==========');
        
        // Final verification - count records in table
        $totalRecords = PackageAndCharge::count();
        \Log::info('Total records in package_and_charges table after update: ' . $totalRecords);
        
        return true;
        
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('========== REPOSITORY EXCEPTION ==========');
        \Log::error('Message: ' . $e->getMessage());
        \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
        \Log::error('Trace: ' . $e->getTraceAsString());
        return false;
    }
}

    public function chargeUpdate($request)
    {
        DB::beginTransaction();
        try {
            $charge = '';
            foreach ($request->weights as $key => $weight) {
                if (!$request->cod_ids[$key]) {
                    $charge = new Charge;
                } else {
                    $charge = Charge::find($request->cod_ids[$key]);
                }

                $charge->weight = $weight;
                $charge->same_day = $request->same_day[$key];
                //  $charge->next_day = $request->next_day[$key];
                $charge->sub_city = $request->sub_city[$key];
                $charge->sub_urban_area = $request->sub_urban_area[$key];
                $charge->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Charge update error: ' . $e->getMessage());
            return false;
        }
    }
}