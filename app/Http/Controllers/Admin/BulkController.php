<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bulk\DeliveryAssignRequest;
use App\Http\Requests\Admin\BulkPickupAssign;
use App\Models\DeliveryMan;
use App\Models\Branch;
use App\Models\Merchant;
use App\Models\Parcel;
use App\Repositories\BulkRepository;
use Brian2694\Toastr\Facades\Toastr;
use App\Repositories\Interfaces\BulkInterface;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Log;

class BulkController extends Controller
{
    protected $assign;

    public function __construct(BulkInterface $assign){
        $this->assign       = $assign;
    }
    public function create()
    {
        return view('admin.bulk.create');
    }

public function save(DeliveryAssignRequest $request)
{
    if (isDemoMode()) {
        Toastr::error(__('this_function_is_disabled_in_demo_server'));
        return back();
    }

    try {
        // Log the start of the operation
        Log::info('========== BULK SAVE START ==========');
        Log::info('Request data:', [
            'delivery_man' => $request->delivery_man,
            'parcels' => $request->parcels,
            'notify_customer' => $request->notify_customer,
            'all' => $request->all()
        ]);

        // Check if delivery man exists
        $deliveryMan = DeliveryMan::with('user')->find($request->delivery_man);
        if (!$deliveryMan) {
            Log::error('Delivery man not found with ID: ' . $request->delivery_man);
            return back()->with('danger', __('delivery_man_not_found'));
        }

        $success = $this->assign->bulkAssign($request);

        if ($success) {
            Log::info('Bulk assignment succeeded');
            
            // Prepare data for print view
            $parcels = [];
            $delivery_man = $deliveryMan->user->first_name . ' ' . $deliveryMan->user->last_name;

            foreach ($request->parcels as $parcel_id) {
                $parcel = $this->assign->get($parcel_id);
                if ($parcel) {
                    $parcels[] = $parcel;
                    Log::info('Loaded parcel for print:', [
                        'id' => $parcel->id,
                        'delivery_man_id' => $parcel->delivery_man_id
                    ]);
                } else {
                    Log::warning('Parcel not found for print: ' . $parcel_id);
                }
            }

            Log::info('Returning print view with ' . count($parcels) . ' parcels');
            return view('admin.bulk.print', compact('parcels', 'delivery_man'));
        }

        Log::error('Bulk assignment returned false');
        return back()->with('danger', __('something_went_wrong_please_try_again'));

    } catch (\Exception $e) {
        Log::error('========== EXCEPTION IN BULK SAVE ==========');
        Log::error('Message: ' . $e->getMessage());
        Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
        Log::error('Trace: ' . $e->getTraceAsString());
        
        return back()->with('danger', __('something_went_wrong_please_try_again'));
    }
}

public function add($parcel_no , Request $request)
{
    $val = $request->val;
    
    // Log the incoming request
    Log::info('========== BULK ADD PARCEL DEBUG ==========');
    Log::info('Searching for parcel number: ' . $parcel_no);
    Log::info('User branch ID: ' . Sentinel::getUser()->branch_id);
    
    // First, check if ANY parcel exists with this number (no filters)
    $anyParcel = Parcel::where('parcel_no', $parcel_no)->first();
    
    if (!$anyParcel) {
        Log::error('No parcel found with number: ' . $parcel_no);
        return response()->json([
            'error' => true, 
            'message' => 'Parcel not found in database: ' . $parcel_no
        ]);
    }
    
    Log::info('Parcel exists in database:', [
        'id' => $anyParcel->id,
        'status' => $anyParcel->status,
        'branch_id' => $anyParcel->branch_id,
        'created_at' => $anyParcel->created_at
    ]);
    
    // Now try to find with status filter
    $allowedStatuses = [
        'received', 
        'transferred-received-by-branch', 
        'delivery-assigned', 
        're-schedule-delivery',
        'pickup-assigned',
        'received-by-pickup-man',
        'pending'
    ];
    
    Log::info('Allowed statuses: ' . implode(', ', $allowedStatuses));
    
    $parcel = Parcel::where('parcel_no', $parcel_no)
        ->whereIn('status', $allowedStatuses)
        ->latest()
        ->first();

    if(!blank($parcel)):
        Log::info('Parcel found with allowed status:', [
            'id' => $parcel->id,
            'status' => $parcel->status,
            'branch_id' => $parcel->branch_id
        ]);
        
        // Check branch
        if ($parcel->branch_id && $parcel->branch_id != Sentinel::getUser()->branch_id):
            Log::warning('Branch mismatch!', [
                'parcel_branch' => $parcel->branch_id,
                'user_branch' => Sentinel::getUser()->branch_id
            ]);
            return response()->json([
                'error' => true, 
                'message' => 'This parcel is not in your branch. Parcel branch: ' . $parcel->branch_id . ', Your branch: ' . Sentinel::getUser()->branch_id
            ]);
        endif;
        
        $view = view('admin.bulk.new-parcel-row', compact('parcel','val'))->render();
        
        return response()->json([
            'val' => $val, 
            'view' => $view,
            'parcel' => [
                'id' => $parcel->id,
                'parcel_no' => $parcel->parcel_no
            ]
        ]);
    endif;
    
    // If we get here, the parcel exists but has an invalid status
    Log::warning('Parcel found but with invalid status:', [
        'id' => $anyParcel->id,
        'status' => $anyParcel->status,
        'allowed_statuses' => $allowedStatuses
    ]);
    
    return response()->json([
        'error' => true, 
        'message' => 'Parcel found but status "' . $anyParcel->status . '" is not allowed for bulk assignment. Allowed statuses: ' . implode(', ', $allowedStatuses)
    ]);
}

    public function bulkTransferCreate()
    {
        $branchs = Branch::where('user_id','!=', Sentinel::getUser()->id)->get();
        return view('admin.bulk.branch-transfer',compact('branchs'));
    }

    public function transferAdd($parcel_no , Request $request)
    {
        $val = $request->val;
        $parcel = Parcel::where('parcel_no', $parcel_no)->whereIn('status',['received','transferred-received-by-branch', 'pickup-assigned'])->latest()->first();
        if (!empty($parcel) && $parcel->branch_id != Sentinel::getUser()->branch_id):
            return response()->json(['error' => true, 'message' => __('this_parcel_is_not_in_your_branch')]);
        endif;

        if(!blank($parcel)):
            $view = view('admin.bulk.new-parcel-row', compact('parcel','val'))->render();
            return response()->json(['val' => $val, 'view' => $view]);
        endif;
    }

    public function bulkTransferSave(DeliveryAssignRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->assign->bulkTransferSave($request)):
                $parcels = [];
                $delivery_man = DeliveryMan::find($request['delivery_man']);
                $delivery_man = $delivery_man->user->first_name.' '.$delivery_man->user->last_name;
                foreach ($request['parcels'] as $parcel_id):
                    $parcels[] = $this->assign->get($parcel_id);
                endforeach;
                return view('admin.bulk.print', compact('parcels', 'delivery_man'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    public function bulkTransferReceive()
    {
        $branchs = Branch::where('user_id','!=', Sentinel::getUser()->id)->get();
        return view('admin.bulk.transfer-receive',compact('branchs'));
    }

    public function transferReceive($parcel_no , Request $request)
    {
        $val = $request->val;
        $parcel = Parcel::where('parcel_no', $parcel_no)->where('status','transferred-to-branch')->latest()->first();

        if ($parcel->transfer_to_branch_id != Sentinel::getUser()->branch_id):
            return response()->json(['error' => true, 'message' => __('this_parcel_is_not_transferred_to_your_branch')]);
        endif;

        if(!blank($parcel)):
            $view = view('admin.bulk.new-parcel-row', compact('parcel','val'))->render();
            return response()->json(['val' => $val, 'view' => $view]);
        endif;
    }

    public function bulkTransferReceivePost(DeliveryAssignRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->assign->bulkTransferReceive($request)):
                $parcels = [];
                $delivery_man = DeliveryMan::find($request['delivery_man']);
                $delivery_man = $delivery_man->user->first_name.' '.$delivery_man->user->last_name;
                foreach ($request['parcels'] as $parcel_id):
                    $parcels[] = $this->assign->get($parcel_id);
                endforeach;
                $receive = 'receive';
                return view('admin.bulk.print', compact('parcels', 'delivery_man', 'receive'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function createPickup()
    {
        return view('admin.bulk.pickup');
    }

    public function getParcels(Request $request)
    {
        $merchant = Merchant::find($request->merchant);

        Log::info('BulkController@getParcels: Search params', [
            'merchant_id' => $request->merchant,
            'executed_by' => $request->attributes->get('verified_user_id')
        ]);

        if (blank($merchant)):
            return response()->json(['error' => true, 'message' => __('merchant_not_found')]);
        endif;

        $parcels = $merchant->parcels()->get();

        if(!blank($parcels)):
            $view = view('admin.bulk.parcels', compact('parcels'))->render();
            return response()->json(['view' => $view]);
        else:
            return response()->json(['error' => true, 'message' => __('no_parcel_found_for_this_merchant')]);
        endif;
    }

    public function bulkPickupAssign(BulkPickupAssign $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if($this->assign->bulkPickupAssign($request)):
                $parcels = [];
                foreach ($request->parcels as $parcel_id): // Use the name from your HTML/JS
    $parcels[] = $this->assign->get($parcel_id);
endforeach;

                return back()->with('success', __('pickup_assigned_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e){
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
}
