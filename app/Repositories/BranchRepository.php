<?php

namespace App\Repositories;
use App\Models\User;
use App\Models\Branch;
use App\Enums\StatusEnum;
use App\Traits\RepoResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiReturnFormatTrait;
use App\Repositories\Interfaces\BranchInterface;

class BranchRepository implements BranchInterface
{

    use RepoResponseTrait, ApiReturnFormatTrait;

    private $model;

    public function __construct(Branch $model)
    {
        $this->model = $model;
    }

    public function paginate()
    {
        return Branch::orderByDesc('id')->paginate(\Config::get('parcel.paginate'));
    }

    public function allUsers()
    {
        return User::get();
    }

    public function get($id)
    {
        return Branch::find($id);
    }

// In app/Repositories/BranchRepository.php
public function store($request)
{
    dump('BranchRepository store method started');
    
    DB::beginTransaction();
    try {
        dump('Creating new branch object');
        $branch = new Branch();
        $branch->user_id = $request->manager;
        $branch->name = $request->name;
        $branch->address = $request->address;
        $branch->phone_number = $request->phone_number;
        
        dump('Branch data:', [
            'user_id' => $branch->user_id,
            'name' => $branch->name,
            'address' => $branch->address,
            'phone_number' => $branch->phone_number
        ]);
        
        $branch->save();
        dump('Branch saved with ID: ' . $branch->id);
        
        // Check if user relationship works
        dump('Checking user relationship');
        if ($branch->user) {
            $user = $branch->user;
            dump('User found:', $user->id, $user->email);
            
            $user->branch_id = $branch->id;
            $user->save();
            dump('User updated with branch_id: ' . $user->branch_id);
        } else {
            dump('No user found with user_id: ' . $branch->user_id);
        }
        
        DB::commit();
        dump('Transaction committed successfully');
        return true;
        
    } catch (\Exception $e) {
        DB::rollback();
        dump('Exception in repository:', $e->getMessage());
        return false;
    }
}

    public function update($request)
    {

        DB::beginTransaction();
        try {
            $branch = $this->get($request->id);
            $branch->user_id = $request->manager;
            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->phone_number = $request->phone_number;
            $branch->save();

            $user = $branch->user;
            $user->branch_id = $branch->id;
            $user->save();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {

            $branch = $this->get($id);

            $branch->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }


    public function statusChange($request)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->findOrFail($request->id);
            if ($row->status == StatusEnum::ACTIVE) {
                $row->status = StatusEnum::INACTIVE;
            } elseif ($row->status == StatusEnum::INACTIVE) {
                $row->status = StatusEnum::ACTIVE;
            }
            $row->save();
            DB::commit();
            return $this->responseWithSuccess(__('updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError($th->getMessage(), []);
        }
    }

    public function changeDefault($request)
    {
        try {
            $branch = Branch::find($request['branch_id']);
            $old_default = Branch::where('default', 1)->first();
            if (!blank($old_default)):
                $old_default->default = 0;
                $old_default->save();
            endif;
            $branch->default = 1;
            $branch->save();
            return $this->responseWithSuccess('updated successfully', []);
        } catch (\Throwable $e) {
            return $this->responseWithError($e->getMessage(), []);
        }
    }
}
