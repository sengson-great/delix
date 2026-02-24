<?php


namespace App\Repositories\Admin;

use App\Enums\StatusEnum;
use App\Models\ThirdParty;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiReturnFormatTrait;
use App\Repositories\Interfaces\Admin\ThirdPartyInterface;

class ThirdPartyRepository implements ThirdPartyInterface
{
    use ApiReturnFormatTrait,RepoResponseTrait;

    public function paginate()
    {
        return ThirdParty::orderByDesc('id')->paginate(\Config::get('parcel.paginate'));
    }

    public function get($id)
    {
        return ThirdParty::find($id);
    }

public function store($request)
{
    dump('========== REPOSITORY DEBUG START ==========');
    dump('Request data received in repository:', [
        'name' => $request->name,
        'address' => $request->address,
        'phone_number' => $request->phone_number,
    ]);

    DB::beginTransaction();
    try {
        dump('Step 1: Creating new ThirdParty model');
        $third_party = new ThirdParty();
        
        dump('Step 2: Model fillable fields:', $third_party->getFillable());
        
        dump('Step 3: Setting attributes');
        $third_party->name = $request->name;
        $third_party->address = $request->address;
        $third_party->phone_number = $request->phone_number;
        
        // Generate slug
        $third_party->slug = \Str::slug($request->name);
        dump('Step 4: Generated slug: ' . $third_party->slug);
        
        // Set default values
        $third_party->type = 'third_party';
        $third_party->provider = 'custom';
        $third_party->status = \App\Enums\StatusEnum::ACTIVE;
        dump('Step 5: Set default values');
        
        dump('Step 6: Model attributes before save:', $third_party->getAttributes());
        
        // Check if address and phone_number columns exist
        $columns = \Schema::getColumnListing('third_parties');
        dump('Step 7: Table columns:', $columns);
        
        dump('Step 8: Attempting to save...');
        $third_party->save();
        
        dump('Step 9: Saved successfully! ID: ' . $third_party->id);
        
        DB::commit();
        dump('Step 10: Transaction committed');
        dump('========== REPOSITORY DEBUG END ==========');
        
        return true;
        
    } catch (\Exception $e){
        DB::rollback();
        dump('========== ERROR IN REPOSITORY ==========');
        dump('Error message: ' . $e->getMessage());
        dump('Error file: ' . $e->getFile());
        dump('Error line: ' . $e->getLine());
        dump('Error trace: ' . $e->getTraceAsString());
        dump('==========================================');
        return false;
    }
}

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $third_party                = $this->get($request->id);
            $third_party->name          = $request->name;
            $third_party->address       = $request->address;
            $third_party->phone_number  = $request->phone_number;
            $third_party->save();

            DB::commit();
            return true;
        } catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }


    public function changeStatus($request)
    {
        try {
            DB::beginTransaction();
            $row                = $this->get($request->id);
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status    = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status    = StatusEnum::ACTIVE;
            }
            $row->save();
            DB::commit();
            return $this->responseWithSuccess('updated successfully', []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $third_party = $this->get($id);

            $third_party->delete();

            DB::commit();

            return true;
        }   catch (\Exception $e){
            DB::rollback();
            return false;
        }
    }
}
