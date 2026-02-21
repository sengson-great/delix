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
            if($this->assign->bulkAssign($request)):
                $parcels = [];
                $delivery_man = DeliveryMan::find($request['delivery_man']);
                $delivery_man = $delivery_man->user->first_name.' '.$delivery_man->user->last_name;
                foreach ($request['parcel_list'] as $parcel_id):
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

    public function add($parcel_no , Request $request)
    {
        $val = $request->val;
        $parcel = Parcel::where('parcel_no', $parcel_no)->whereIn('status',['received','transferred-received-by-branch','delivery-assigned','re-schedule-delivery'])->latest()->first();

        if(!blank($parcel)):
            if ($parcel->branch_id != Sentinel::getUser()->branch_id):
                return response()->json(['error' => true, 'message' => __('this_parcel_is_not_in_your_branch')]);
            endif;
            $view = view('admin.bulk.new-parcel-row', compact('parcel','val'))->render();
            return response()->json(['val' => $val, 'view' => $view]);
        endif;
    }

    public function bulkTransferCreate()
    {
        $branchs = Branch::where('user_id','!=', Sentinel::getUser()->id)->get();
        return view('admin.bulk.branch-transfer',compact('branchs'));
    }

    public function transferAdd($parcel_no , Request $request)
    {
        $val = $request->val;
        $parcel = Parcel::where('parcel_no', $parcel_no)->whereIn('status',['received','transferred-received-by-branch'])->latest()->first();
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
                foreach ($request['parcel_list'] as $parcel_id):
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
                foreach ($request['parcel_list'] as $parcel_id):
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

        if (blank($merchant)):
            return response()->json(['error' => true, 'message' => __('merchant_not_found')]);
        endif;

        $parcels = $merchant->parcels()->where('status', 'pending')->get();

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
                foreach ($request['parcels'] as $parcel_id):
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
