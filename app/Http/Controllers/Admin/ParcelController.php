<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\User;
use App\Models\Branch;
use App\Models\Charge;
use App\Models\Parcel;
use App\Models\Merchant;
use App\Models\CodCharge;
use App\Models\ThirdParty;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Exports\ClosingReport;
use App\Traits\SmsSenderTrait;
use Illuminate\Support\Carbon;
use App\Exports\FilteredParcel;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\DataTables\Admin\ParcelDataTable;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Repositories\Interfaces\ParcelInterface;
use App\Repositories\Interfaces\DeliveryManInterface;
use App\Http\Requests\Admin\Parcel\ParcelStoreRequest;
use App\Http\Requests\Admin\Parcel\ParcelUpdateRequest;
use App\Http\Requests\Admin\Parcel\PartialDeliveryRequest;
use App\Http\Requests\Admin\Parcel\TransferToBranchRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Image;


class ParcelController extends Controller
{
    use SmsSenderTrait;
    protected $parcels;
    protected $delivery_man;

    public function __construct(ParcelInterface $parcels, DeliveryManInterface $delivery_man)
    {
        $this->parcels = $parcels;
        $this->delivery_man = $delivery_man;
    }

    public function index(Request $request, ParcelDataTable $dataTable)
    {
        //Log::info('ParcelController@index: Rendering parcel list.', ['user_id' => Sentinel::getUser()->id]);
        $data['title'] = __('parcel');
        $countData = $dataTable->getTotalCount();
        $data['countData'] = $countData;
        $data['charges'] = Charge::all();
        $data['cod_charges'] = CodCharge::all();
        $data['branchs'] = Branch::all();
        $data['third_parties'] = ThirdParty::where('status', true)->orderBy('name')->get();
        return $dataTable->render('admin.parcel.index', $data);
    }

    public function create()
    {
        $user = Sentinel::getUser();
        //Log::info('ParcelController@create: Accessing create parcel view.', ['user_id' => $user->id]);

        $charges = Charge::all();
        $branchs = Branch::all();
        
        Log::info('Current user: ' . ($user ? $user->shop_name : 'No user authenticated'));
        
        $merchant_id = $user->merchant_id ?? null;
        
        if ($merchant_id) {
            $shops = Shop::where('merchant_id', $merchant_id)->get();
            Log::info('Fetched shops for merchant.', ['merchant_id' => $merchant_id, 'shop_count' => $shops->count()]);
        } else {
            $shops = collect(); 
            //Log::warning('Merchant ID not found for user during parcel creation.', ['user_id' => $user->id]);
        }
        
        $default_shop = Shop::where('merchant_id', $merchant_id)->where('default', 1)->first();
        
        return view('admin.parcel.create', compact('charges', 'branchs', 'shops', 'default_shop'));
    }

    public function store(ParcelStoreRequest $request)
    {
        Log::info('ParcelController@store: Attempting to store parcel.', ['input' => $request->all()]);

        if (isDemoMode()) {
            Log::warning('ParcelController@store: Action blocked by Demo Mode.');
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        
        try {
            $preferences = settingHelper('preferences');
            $createParcelPref = null;
            if ($preferences) {
                $createParcelPref = $preferences->where('key', 'create_parcel')->first();
            }
            
            $canCreateParcel = false;
            if ($createParcelPref) {
                if (isset($createParcelPref->staff)) {
                    $canCreateParcel = ($createParcelPref->staff == 1);
                } elseif (isset($createParcelPref->value)) {
                    $value = json_decode($createParcelPref->value, true);
                    $canCreateParcel = (isset($value['staff']) && $value['staff'] == 1);
                }
            }
            
            Log::info('Parcel creation check:', [
                'preferences_exists' => $preferences ? 'yes' : 'no',
                'create_parcel_exists' => $createParcelPref ? 'yes' : 'no',
                'can_create' => $canCreateParcel ? 'yes' : 'no'
            ]);
            
            if (!$createParcelPref) {
                $canCreateParcel = true;
                Log::info('No create_parcel preference found, defaulting to true');
            }
            
            if ($canCreateParcel) {
                if ($this->parcels->store($request)) {
                    Log::info('Parcel stored successfully.');
                    return redirect()->route('parcel')->with('success', __('created_successfully'));
                } else {
                    Log::error('Parcel repository failed to store data.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'))->withInput();
                }
            } else {
                Log::warning('Parcel creation disabled by preferences.');
                return redirect()->route('parcel')->with('danger', __('service_unavailable'));
            }
        } catch (\Exception $e) {
            Log::error('Parcel Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('danger', __('something_went_wrong_please_try_again'))->withInput();
        }
    }

    public function edit($id)
    {
        Log::info('ParcelController@edit: Fetching parcel for edit.', ['parcel_id' => $id]);
        $parcel = $this->parcels->get($id);

        if (
            hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
            || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
        ):
            Log::info('ParcelController@edit: Permission granted.');
            $charges = Charge::all();
            $branchs = Branch::all();
            
            $allowed_statuses = ['pending', 'pickup-assigned', 're-schedule-pickup', 'received-by-pickup-man', 'received', 'transferred-to-branch', 'delivery-assigned', 're-schedule-delivery'];
            
            if (
                in_array($parcel->status, $allowed_statuses)
                || ($parcel->status == "returned-to-warehouse" && $parcel->is_partially_delivered == false)
                || ($parcel->status == "return-assigned-to-merchant" && $parcel->is_partially_delivered == false)
            ):
                return view('admin.parcel.edit', compact('parcel', 'charges', 'branchs'));
            else:
                Log::warning('ParcelController@edit: Edit rejected due to current status.', ['status' => $parcel->status]);
                return back()->with('danger', __('you_are_not_allowed_to_update_this_parcel'));
            endif;
        else:
            Log::warning('ParcelController@edit: Access denied for user.', ['user_id' => Sentinel::getUser()->id]);
            return back()->with('danger', __('access_denied'));
        endif;
    }

    public function update(ParcelUpdateRequest $request)
    {
        Log::info('ParcelController@update: Processing update.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($this->parcels->update($request)):
                    Log::info('Parcel updated successfully.', ['parcel_id' => $request->id]);
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    Log::error('Parcel update failed in repository.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;

            else:
                Log::warning('Parcel update access denied.', ['parcel_id' => $request->id]);
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Parcel Update Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelDelete(Request $request)
    {
        Log::info('ParcelController@parcelDelete: Attempting delete.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($parcel->status == 'deleted'):
                    Log::warning('Delete attempted on already deleted parcel.');
                    return back()->with('danger', __('this_parcel_has_already_been_deleted'));
                endif;

                if ($this->parcels->parcelDelete($request)):
                    Log::info('Parcel deleted successfully.', ['parcel_id' => $request->id]);
                    return redirect()->route('parcel')->with('success', __('deleted_successfully'));
                else:
                    Log::error('Parcel delete repository failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Parcel delete access denied.', ['parcel_id' => $request->id]);
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Parcel Delete Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function assignPickupMan(Request $request)
    {
        Log::info('ParcelController@assignPickupMan: Assigning pickup man.', ['parcel_id' => $request->id, 'pickup_man_id' => $request->pickup_man_id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($this->parcels->assignPickupMan($request)):
                    Log::info('Pickup man assigned.');
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    Log::error('Assign pickup man repository failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Assign pickup man access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Assign Pickup Man Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function assignDeliveryMan(Request $request)
    {
        Log::info('ParcelController@assignDeliveryMan: Starting bulk assignment.', ['ids' => $request->ids, 'delivery_man_id' => $request->delivery_man_id]);
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (!$parcel) {
                    Log::warning('Bulk Delivery Man Assignment: Parcel not found.', ['id' => $id]);
                    continue;
                }
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($this->parcels->assignDeliveryMan($request, $id, 'normal')):
                        Log::info('Delivery man assigned to parcel.', ['parcel_id' => $id]);
                        $message = __('delivery_man_assigned_successfully');
                    else:
                        Log::error('Failed to assign delivery man to parcel.', ['parcel_id' => $id]);
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    Log::warning('Delivery man assignment access denied.', ['parcel_id' => $id]);
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Assign Delivery Man Bulk Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reSchedulePickup(Request $request)
    {
        Log::info('ParcelController@reSchedulePickup: AJAX request.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $data = $this->parcels->reSchedulePickup($request);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Re-schedule Pickup AJAX Error: ' . $e->getMessage());
            $message = __('something_went_wrong_please_try_again');
            return response()->json($message, 404);
        }
    }

    public function parcelCod(Request $request)
    {
        try {
            $parcel = $this->parcels->get($request->id);
            Log::info('ParcelController@parcelCod: Fetching COD.', ['parcel_id' => $request->id]);
            $data[1] = $parcel->price;
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Parcel COD Error: ' . $e->getMessage());
            $message = __('something_went_wrong_please_try_again');
            return response()->json($message, 404);
        }
    }

    public function reSchedulePickupMan(Request $request)
    {
        Log::info('ParcelController@reSchedulePickupMan: Submitting rescheduling.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->parcels->reSchedulePickupMan($request)):
                Log::info('Pickup rescheduled successfully.');
                return redirect()->route('parcel')->with('success', __('pickup_rescheduled_successfully'));
            else:
                Log::error('Reschedule repository failed.');
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            Log::error('Re-schedule Pickup Man Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reScheduleDelivery(Request $request)
    {
        Log::info('ParcelController@reScheduleDelivery: AJAX request.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $data = $this->parcels->reScheduleDelivery($request);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Re-schedule Delivery AJAX Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reScheduleDeliveryMan(Request $request)
    {
        Log::info('ParcelController@reScheduleDeliveryMan: Submitting rescheduling.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->parcels->reScheduleDeliveryMan($request)):
                Log::info('Delivery rescheduled successfully.');
                return redirect()->route('parcel')->with('success', __('delivery_rescheduled_successfully'));
            else:
                Log::error('Delivery reschedule repository failed.');
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            Log::error('Re-schedule Delivery Man Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function returnAssignToMerchant(Request $request)
    {
        Log::info('ParcelController@returnAssignToMerchant: Bulk action start.', ['ids' => $request->ids]);
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);

                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($this->parcels->returnAssignToMerchant($request, $id)):
                        Log::info('Parcel assigned for return.', ['parcel_id' => $id]);
                        $message = __('return_assign_successfully');
                    else:
                        Log::error('Failed to assign return for parcel.', ['parcel_id' => $id]);
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    Log::warning('Return assignment access denied.', ['parcel_id' => $id]);
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Return Assign Bulk Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelCancel(Request $request)
    {
        Log::info('ParcelController@parcelCancel: Attempting cancellation.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($parcel->status == 'cancel'):
                    Log::warning('Cancel attempted on already cancelled parcel.');
                    return back()->with('danger', __('this_parcel_has_already_been_cancelled'));
                endif;

                if ($parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified' || $parcel->status == 'returned-to-merchant' || $parcel->status == 'partially-delivered'):
                    Log::warning('Cancel rejected: Parcel already completed.', ['status' => $parcel->status]);
                    return back()->with('danger', __('this_parcel_can_not_be_cancelled'));
                endif;

                if ($this->parcels->parcelCancel($request)):
                    Log::info('Parcel cancelled successfully.', ['parcel_id' => $request->id]);
                    return redirect()->route('parcel')->with('success', __('cancelled_successfully'));
                else:
                    Log::error('Parcel cancel repository failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Parcel cancel access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Parcel Cancel Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelReceiveByPickupman(Request $request)
    {
        Log::info('ParcelController@parcelReceiveByPickupman: Bulk receive start.', ['ids' => $request->ids]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($parcel->status == 'received-by-pickup-man'):
                        Log::warning('Parcel already received by pickup man.', ['parcel_id' => $id]);
                        $error = 1;
                        $message = __('this_parcel_has_already_been_received_by_pickup_man');
                        continue;
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'received-by-pickup-man', $request->note)):
                        Log::info('Parcel status updated to received-by-pickup-man.', ['parcel_id' => $id]);
                        $message = __('updated_successfully');
                    else:
                        Log::error('Failed to update status for parcel.', ['parcel_id' => $id]);
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    Log::warning('Access denied during pickup man receive.', ['parcel_id' => $id]);
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Receive By Pickup Man Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelReceive(Request $request)
    {
        Log::info('ParcelController@parcelReceive: Bulk receive at branch.', ['ids' => $request->ids]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):
                    if ($parcel->status != 'received'):
                        if ($this->parcels->parcelStatusUpdate($parcel->id, 'received', $request->note, $request->branch)):
                            Log::info('Parcel received at branch.', ['parcel_id' => $id, 'branch' => $request->branch]);
                            $message = __('updated_successfully');
                        else:
                            Log::error('Failed to receive parcel at branch.', ['parcel_id' => $id]);
                            $error = 1;
                            $message = __('something_went_wrong_please_try_again_later');
                        endif;
                    endif;
                else:
                    Log::warning('Receive access denied.', ['parcel_id' => $id]);
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Parcel Receive Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelDelivery(Request $request)
    {
        Log::info('ParcelController@parcelDelivery: Bulk delivery confirmation.', ['ids' => $request->ids]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):

                    if ($parcel->status == 'partially-delivered' || $parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified'):
                        Log::warning('Delivery rejected: Parcel already confirmed as delivered.', ['parcel_id' => $id]);
                        return back()->with('danger', __('this_parcel_has_already_confirmed_as_delivered'));
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'delivered', $request->note)):
                        Log::info('Parcel marked as delivered.', ['parcel_id' => $id]);
                        $message = __('updated_successfully');
                    else:
                        Log::error('Failed to mark parcel as delivered.', ['parcel_id' => $id]);
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    Log::warning('Delivery confirmation access denied.', ['parcel_id' => $id]);
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Parcel Delivery Confirm Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function partialDelivery(PartialDeliveryRequest $request)
    {
        Log::info('ParcelController@partialDelivery: Processing partial delivery.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($parcel->status == 'partially-delivered' || $parcel->status == 'delivered' || $parcel->status == 'delivered-and-verified'):
                    Log::warning('Partial delivery rejected: Already confirmed as delivered.');
                    return back()->with('danger', __('this_parcel_has_already_confirmed_as_delivered'));
                endif;

                if ($this->parcels->partialDelivery($request)):
                    Log::info('Partial delivery saved successfully.');
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    Log::error('Partial delivery repository failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Partial delivery access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Partial Delivery Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function parcelReturnToGreenx(Request $request)
    {
        Log::info('ParcelController@parcelReturnToGreenx: Bulk return to warehouse.', ['ids' => $request->ids]);
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):

                    if ($parcel->status == 'returned-to-warehouse'):
                        Log::warning('Return to warehouse rejected: Already returned.', ['parcel_id' => $id]);
                        return back()->with('danger', __('this_parcel_has_already_confirmed_as_returned_to_warehouse'));
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'returned-to-warehouse', $request->note)):
                        Log::info('Parcel returned to warehouse.', ['parcel_id' => $id]);
                        $message = __('updated_successfully');
                    else:
                        Log::error('Failed to mark as returned to warehouse.', ['parcel_id' => $id]);
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    Log::warning('Return to Greenx access denied.', ['parcel_id' => $id]);
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Return To Warehouse Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function returnToMerchant(Request $request)
    {
        Log::info('ParcelController@returnToMerchant: Bulk return to merchant.', ['ids' => $request->ids]);
        try {
            $parcelIdsArray = explode(',', $request->ids);
            $error = 0;
            foreach ($parcelIdsArray as $id) {
                $parcel = $this->parcels->get($id);
                if (
                    hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                    || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
                ):

                    if ($parcel->status == 'returned-to-merchant'):
                        Log::warning('Return to merchant rejected: Already returned.', ['parcel_id' => $id]);
                        return back()->with('danger', __('this_parcel_has_already_confirmed_as_returned_to_merchant'));
                    endif;

                    if ($this->parcels->parcelStatusUpdate($parcel->id, 'returned-to-merchant', $request->note)):
                        Log::info('Parcel returned to merchant.', ['parcel_id' => $id]);
                        $message = __('updated_successfully');
                    else:
                        Log::error('Failed to mark as returned to merchant.', ['parcel_id' => $id]);
                        $error = 1;
                        $message = __('something_went_wrong_please_try_again_later');
                    endif;
                else:
                    Log::warning('Return to merchant access denied.', ['parcel_id' => $id]);
                    $message = __('access_denied');
                endif;
            }
            return redirect()->route('parcel')->with($error == 1 ? 'error' : 'success', $message);
        } catch (\Exception $e) {
            Log::error('Return To Merchant Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reverseFromCancel(Request $request)
    {
        Log::info('ParcelController@reverseFromCancel: Reversing cancellation.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($this->parcels->reverseUpdate($parcel->id, $parcel->status_before_cancel, $request->note)):
                    Log::info('Cancellation reversed.', ['previous_status' => $parcel->status_before_cancel]);
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    Log::error('Reverse update repository failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Reverse from cancel access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Reverse From Cancel Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function transferTobranch(TransferToBranchRequest $request)
    {
        Log::info('ParcelController@transferTobranch: Transferring parcel.', ['parcel_id' => $request->id, 'to_branch' => $request->branch]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($parcel->branch_id == $request->branch):
                    Log::warning('Transfer rejected: Branch is the same.');
                    return back()->with('danger', __('branch_must_be_different'));
                endif;

                if ($parcel->status == 'transferred-to-branch'):
                    Log::warning('Transfer rejected: Already transferred.');
                    return back()->with('danger', __('this_parcel_has_already_assigned_for_transfer_to_branch'));
                endif;

                if ($this->parcels->parcelStatusUpdate($parcel->id, 'transferred-to-branch', $request->note, $request->branch, $request->delivery_man)):
                    Log::info('Transfer initiated successfully.');
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    Log::error('Transfer update repository failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Transfer access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Transfer To Branch Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function transferReceiveTobranch(Request $request)
    {
        Log::info('ParcelController@transferReceiveTobranch: Receiving transferred parcel.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):

                if ($parcel->status == 'transferred-received-by-branch'):
                    Log::warning('Transfer receive rejected: Already received.');
                    return back()->with('danger', __('this_parcel_has_already_assigned_for_transfer_to_branch'));
                endif;

                if ($this->parcels->parcelStatusUpdate($parcel->id, 'transferred-received-by-branch', $request->note, $parcel->transfer_to_branch_id)):
                    Log::info('Transferred parcel received successfully.');
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    Log::error('Transfer receive update failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Transfer receive access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Transfer Receive Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function deliveryReverse(Request $request)
    {
        Log::info('ParcelController@deliveryReverse: Reversing delivery status.', ['parcel_id' => $request->id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = $this->parcels->get($request->id);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                if ($this->parcels->deliveryReverse($request)):
                    Log::info('Delivery status reversed successfully.');
                    return redirect()->route('parcel')->with('success', __('updated_successfully'));
                else:
                    Log::error('Delivery reverse repository failed.');
                    return back()->with('danger', __('something_went_wrong_please_try_again'));
                endif;
            else:
                Log::warning('Delivery reverse access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Delivery Reverse Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }


    public function filter(Request $request)
    {
        Log::info('ParcelController@filter: Applying filters.', ['filters' => $request->all()]);
        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $branchs = Branch::all();
        $third_parties = ThirdParty::where('status', true)->orderBy('name')->get();

        $query = Parcel::query();

        if (!hasPermission('read_all_parcel')) {
            $query->where(function ($q) {
                $q->where('branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', \Sentinel::getUser()->branch_id);
            });
        }

        if ($request->parcel_no != "") {
            $query->where('parcel_no', $request->parcel_no);
            $parcels = $query->paginate(\Config::get('parcel.paginate'));
            Log::info('Filter: Searched by parcel_no.', ['count' => $parcels->total()]);
            return view('admin.parcel.index', compact('parcels', 'cod_charges', 'charges', 'branchs', 'third_parties'));
        }

        if ($request->phone_no != "") {
            $query->where('customer_phone_number', 'LIKE', '%' . $request->phone_no);
            $parcels = $query->paginate(\Config::get('parcel.paginate'));
            Log::info('Filter: Searched by phone_no.', ['count' => $parcels->total()]);
            return view('admin.parcel.index', compact('parcels', 'cod_charges', 'charges', 'branchs', 'third_parties'));
        }

        if ($request->created_from != "") {
            $created_from = date("Y-m-d", strtotime($request->created_from));
            $query->whereDate('created_at', '>=', "{$created_from}%");
            if ($request->created_to != "") {
                $created_to = date("Y-m-d", strtotime($request->created_to));
                $query->whereDate('created_at', '<=', "{$created_to}%");
            }
        }

        if ($request->pickup_branch != 'all') {
            $query->when($request->pickup_branch == 'pending', function ($search) {
                $search->where('pickup_branch_id', null);
            })->when($request->pickup_branch != 'pending', function ($search) use ($request) {
                $search->where('pickup_branch_id', $request->pickup_branch);
            });
        }

        if ($request->branch != "any") {
            $query->where('branch_id', $request->branch);
        }

        if ($request->merchant != "") {
            $query->where('merchant_id', $request->merchant);
        }
        if ($request->phone_number != "") {
            $query->whereHas('merchant', function ($inner_query) use ($request) {
                $inner_query->where('phone_number', 'LIKE', "%{$request->phone_number}%");
            });
        }

        if ($request->customer_invoice_no != "") {
            $query->where('customer_invoice_no', 'LIKE', "%{$request->customer_invoice_no}%");
        }

        if ($request->status != "any") {
            $query->when($request->status == 'pending-return', function ($q) {
                $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
            })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
        }

        if ($request->pickup_man != "any") {
            $query->where('pickup_man_id', $request->pickup_man);
        }

        if ($request->delivery_man != "any") {
            $query->where('delivery_man_id', $request->delivery_man);
        }

        if ($request->weight != "any") {
            $query->where('weight', $request->weight);
        }

        if ($request->parcel_type != "any") {
            $query->where('parcel_type', $request->parcel_type);
        }

        if ($request->location != "any") {
            $query->where('location', $request->location);
        }

        if ($request->pickup_date != "") {
            $pickup_date = date("Y-m-d", strtotime($request->pickup_date));
            $query->where('pickup_date', 'LIKE', "%{$pickup_date}%");
        }

        if ($request->delivery_date != "") {
            $delivery_date = date("Y-m-d", strtotime($request->delivery_date));
            $query->where('delivery_date', 'LIKE', "%{$delivery_date}%");
        }

        if ($request->third_party != "any") {
            $query->where('third_party_id', $request->third_party);
        }

        if ($request->delivered_date != "") {
            $delivered_date = date("Y-m-d", strtotime($request->delivered_date));
            $query->where('delivered_date', 'LIKE', "%{$delivered_date}%");
        }
        if ($request->returned_date != "") {
            $returned_date = date("Y-m-d", strtotime($request->returned_date));
            $query->where('returned_date', 'LIKE', "%{$returned_date}%");
        }
        
        if ($request->has('download')):
            Log::info('Filter: Triggering Excel download.');
            $file_name = 'Filtered Parcels ' . date('Y-m-d-s') . '.xlsx';
            return Excel::download(new FilteredParcel($query), $file_name);
        endif;

        $parcels = $query->latest()->paginate(\Config::get('parcel.parcel_merchant_paginate'));
        Log::info('Filter: Results fetched.', ['total_results' => $parcels->total()]);
        return view('admin.parcel.index', compact('parcels', 'cod_charges', 'charges', 'branchs', 'third_parties'));
    }

    public function getParcelDownload(Request $request)
    {
        Log::info('ParcelController@getParcelDownload: Starting download process.');
        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $branchs = Branch::all();
        $third_parties = ThirdParty::where('status', true)->orderBy('name')->get();

        $query = Parcel::query();
        if (!hasPermission('read_all_parcel')) {
            $query->where(function ($q) {
                $q->where('branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhere('pickup_branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhereNull('pickup_branch_id')
                    ->orWhere('transfer_to_branch_id', \Sentinel::getUser()->branch_id);
            });
        }
        if ($request->merchant_id) {
            $query->where('merchant_id', $request->merchant_id);
        }
        if ($request->phone_number) {
            $query->whereHas('merchant', function ($inner_query) use ($request) {
                $inner_query->where('phone_number', 'LIKE', "%{$request->phone_number}%");
            });
        }
        if ($request->customer_invoice_no) {
            $query->where('customer_invoice_no', 'LIKE', "%{$request->customer_invoice_no}%");
        }
        if ($request->created_at != "") {
            $query->when($request->created_at ?? false, function ($query, $created_at) {
                $dateRange = $this->parseDate($created_at);
                $query->whereBetween('created_at', $dateRange);
            });
        }
        if ($request->pickup_date) {
            $pickup_date = date("Y-m-d", strtotime($request->pickup_date));
            $query->where('pickup_date', 'LIKE', "%{$pickup_date}%");
        }
        if ($request->delivery_date) {
            $delivery_date = date("Y-m-d", strtotime($request->delivery_date));
            $query->where('delivery_date', 'LIKE', "%{$delivery_date}%");
        }
        if ($request->delivered_date) {
            $delivered_date = date("Y-m-d", strtotime($request->delivered_date));
            $query->where('delivered_date', 'LIKE', "%{$delivered_date}%");
        }
        if ($request->returned_date != "") {
            $returned_date = date("Y-m-d", strtotime($request->returned_date));
            $query->where('returned_date', 'LIKE', "%{$returned_date}%");
        }
        if ($request->pickup_man_id) {
            $query->where('pickup_man_id', $request->pickup_man_id);
        }
        if ($request->delivery_man_id) {
            $query->where('delivery_man_id', $request->delivery_man_id);
        }
        if ($request->third_party_id) {
            $query->where('third_party_id', $request->third_party_id);
        }
        if ($request->status) {
            $query->when($request->status == 'pending-return', function ($q) {
                $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
            })
                ->when($request->status == 'partially-delivered', function ($q) {
                    $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                        ->where('is_partially_delivered', '=', 1);
                })
                ->when($request->status != 'pending-return' && $request->status != 'partially-delivered', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
        }
        if ($request->weight) {
            $query->where('weight', $request->weight);
        }
        if ($request->parcel_type) {
            $query->where('parcel_type', $request->parcel_type);
        }
        if ($request->location) {
            $query->where('location', $request->location);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->pickup_branch_id) {
            $query->when($request->pickup_branch_id == 'pending', function ($search) {
                $search->where('pickup_branch_id', null);
            })->when($request->pickup_branch_id != 'pending', function ($search) use ($request) {
                $search->where('pickup_branch_id', $request->pickup_branch_id);
            });
        }
        $filename = 'filtered_parcels_' . now()->format('YmdHis') . '.xlsx';
        Log::info('getParcelDownload: File ready for download.', ['filename' => $filename]);
        return Excel::download(new FilteredParcel($query), $filename);
    }

    public function shops(Request $request)
    {
        Log::info('ParcelController@shops: Fetching shops/warehouses for merchant.', ['merchant_id' => $request->merchant_id]);
        $requested_merchant = Merchant::findOrfail($request->merchant_id);
        if (isset($request->type)) {
            $warehouses = $requested_merchant->warehouse;
            return view('admin.parcel.warehouse', compact('warehouses'))->render();
        }
        $shops = $requested_merchant->shops;
        return view('admin.parcel.shops', compact('shops'))->render();
    }

    public function shop(Request $request)
    {
        Log::info('ParcelController@shop: Fetching shop details.', ['shop_id' => $request->shop_id]);
        $shop = Shop::find($request->shop_id);
        $data['shop_pickup_branch'] = $shop->branch->name ?? '';
        $data['shop_phone_number'] = $shop->shop_phone_number;
        $data['address'] = $shop->address;
        return response()->json($data);
    }

    public function warehousesProduct(Request $request)
    {
        Log::info('ParcelController@warehousesProduct: Fetching products for warehouse.', ['warehouse_id' => $request->warehouse_id]);
        $warehouses_stock = Stock::where('warehouse_id', $request->warehouse_id)->get();
        return view('admin.parcel.product', compact('warehouses_stock'));
    }

    public function default(Request $request)
    {
        Log::info('ParcelController@default: Fetching merchant default shop settings.', ['merchant_id' => $request->merchant_id]);
        $default_shop = Shop::where('merchant_id', $request->merchant_id)->where('default', 1)->first();
        $pickup_branch = Merchant::find($request->merchant_id)->user->branch_id;

        $data['shop_phone_number'] = $default_shop->shop_phone_number ?? '';
        $data['address'] = $default_shop->address ?? '';

        $branchs = Branch::all();
        $options = view('admin.parcel.branchs', compact('branchs', 'pickup_branch'))->render();

        $data['pickup_branch'] = $options;
        return response()->json($data);
    }

    public function merchantStaff(Request $request)
    {
        Log::info('ParcelController@merchantStaff: Fetching staff.', ['merchant_id' => $request->merchant_id]);
        $staffs = Merchant::find($request->merchant_id)->staffs;
        return view('admin.parcel.staffs', compact('staffs'))->render();
    }


    public function detail($id)
    {
        Log::info('ParcelController@detail: Accessing parcel detail.', ['parcel_id' => $id]);
        try {
            $parcel = Parcel::with('merchant.user', 'events', 'branch')->find($id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                return view('admin.parcel.detail', compact('parcel', 'cod_charges', 'charges'));
            else:
                Log::warning('Detail access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Parcel Detail Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function print($id)
    {
        Log::info('ParcelController@print: Printing invoice.', ['parcel_id' => $id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = Parcel::find($id);
            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                $charges = Charge::all();
                $cod_charges = CodCharge::all();
                $delivery_men = $this->delivery_man->all();
                return view('merchant.parcel.print', compact('parcel', 'cod_charges', 'charges', 'delivery_men'));
            else:
                Log::warning('Print access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Invoice Print Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function duplicate($id)
    {
        Log::info('ParcelController@duplicate: Starting duplication.', ['parcel_id' => $id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if (@settingHelper('preferences')->where('title', 'create_parcel')->first()->staff):
                $parcel = $this->parcels->get($id);
                $charges = Charge::all();
                $branchs = Branch::all();
                return view('admin.parcel.create', compact('parcel', 'charges', 'branchs'));
            else:
                Log::warning('Duplication blocked: Preference disabled.');
                return back()->with('danger', __('service_unavailable'));
            endif;
        } catch (\Exception $e) {
            Log::error('Parcel Duplicate Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function sticker($id)
    {
        Log::info('ParcelController@sticker: Printing sticker.', ['parcel_id' => $id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = Parcel::with('branch', 'pickupBranch')->find($id);
            $qr_code = QrCode::size(50)->generate($parcel->parcel_no);

            if (
                hasPermission('read_all_parcel') || $parcel->branch_id == \Sentinel::getUser()->branch_id || $parcel->pickup_branch_id == ''
                || $parcel->pickup_branch_id == \Sentinel::getUser()->branch_id || $parcel->transfer_to_branch_id == \Sentinel::getUser()->branch_id
            ):
                $label_type = settingHelper('label_sticker');
                Log::info('Sticker type selected.', ['type' => $label_type]);
                if ($label_type == 'cCorrier') {
                    return view('admin.parcel.e_courier', compact('parcel', 'qr_code'));
                } elseif ($label_type == 'pathao') {
                    return view('admin.parcel.pathao', compact('parcel', 'qr_code'));
                } else {
                    return view('admin.parcel.sticker', compact('parcel', 'qr_code'));
                }
            else:
                Log::warning('Sticker access denied.');
                return back()->with('danger', __('access_denied'));
            endif;
        } catch (\Exception $e) {
            Log::error('Sticker Generation Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function notifyPickupMan($id)
    {
        Log::info('ParcelController@notifyPickupMan: Sending SMS.', ['parcel_id' => $id]);
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $parcel = Parcel::find($id);
            $sms_body = $parcel->pickupMan->user->first_name . ', a pickup has been assigned to you. Address: ' . $parcel->pickup_address . ', Phone number: ' . $parcel->pickup_shop_phone_number . ', Pickup date: ' . $parcel->pickup_date;

            if ($this->test($sms_body, $parcel->pickupMan->phone_number, 'notify_pickup_man', setting('active_sms_provider'))):
                Log::info('SMS notification sent successfully.', ['phone' => $parcel->pickupMan->phone_number]);
                return back()->with('success', __('notified_successfully'));
            else:
                Log::error('SMS notification failed.');
                return back()->with('danger', __('unable_to_notify'));
            endif;
        } catch (\Exception $e) {
            Log::error('Notify Pickup Man Error: ' . $e->getMessage());
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function reverseOptions(Request $request)
    {
        Log::info('ParcelController@reverseOptions: Fetching UI options.', ['parcel_id' => $request->id]);
        $parcel = $this->parcels->get($request->id);
        $status = $parcel->status;
        $is_partially_delivered = $parcel->is_partially_delivered;
        return view('admin.parcel.reverse-options', compact('status', 'is_partially_delivered'))->render();
    }

    public function transferOptions(Request $request)
    {
        Log::info('ParcelController@transferOptions: Fetching UI options.', ['parcel_id' => $request->id]);
        $current_branch = $this->parcels->get($request->id)->branch_id;
        $branchs = Branch::where('id', '!=', $current_branch)->get();
        return view('admin.parcel.transfer-options', compact('branchs'))->render();
    }

    public function reverseUpdate($id, $status, $note = '')
    {
        Log::info('ParcelController@reverseUpdate: Manual reversal.', ['parcel_id' => $id, 'to_status' => $status]);
        if (isDemoMode()) {
            $success[0] = __('this_function_is_disabled_in_demo_server');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
        try {
            if ($this->parcels->reverseUpdate($id, $status)):
                Log::info('Manual reversal successful.');
                $success[0] = __('updated_successfully');
                $success[1] = 'success';
                $success[2] = __('updated');
                return response()->json($success);
            endif;
        } catch (\Exception $e) {
            Log::error('Manual Reversal Error: ' . $e->getMessage());
            $success[0] = __('something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = __('oops');
            return response()->json($success);
        }
    }

    public function parcelFiltering($slug)
    {
        Log::info('ParcelController@parcelFiltering: Slug access.', ['slug' => $slug]);
        $charges = Charge::all();
        $cod_charges = CodCharge::all();
        $branchs = Branch::all();
        $third_parties = ThirdParty::where('status', true)->orderBy('name')->get();
        $parcels = Parcel::when($slug == 'pending-return', function ($q) {
            $q->whereIn('status', ['returned-to-warehouse', 'return-assigned-to-merchant', 'cancel', 'partially-delivered']);
        })
            ->when($slug == 'partially-delivered', function ($q) {
                $q->whereIn('status', ['partially-delivered', 'returned-to-merchant'])
                    ->where('is_partially_delivered', '=', 1);
            })
            ->when($slug != 'pending-return' && $slug != 'partially-delivered', function ($q) use ($slug) {
                $q->where('status', $slug);
            })
            ->when(!hasPermission('read_all_parcel'), function ($query) {
                $query->where(function ($q) {
                    $q->where('branch_id', Sentinel::getUser()->branch_id)
                        ->orWhere('pickup_branch_id', Sentinel::getUser()->branch_id)
                        ->orWhereNull('pickup_branch_id')
                        ->orWhere('transfer_to_branch_id', Sentinel::getUser()->branch_id);
                });
            })
            ->orderByDesc('id')
            ->paginate(\Config::get('parcel.parcel_merchant_paginate'));
        return view('admin.parcel.index', compact('parcels', 'charges', 'cod_charges', 'slug', 'branchs', 'third_parties'));
    }

    public function chargeDetails(Request $request)
    {
        Log::info('ParcelController@chargeDetails: Fetching charge info.', ['request_data' => $request->all()]);
        $data = $this->parcels->chargeDetails($request);
        return response()->json($data);
    }

    public function customerDetails(Request $request)
    {
        Log::info('ParcelController@customerDetails: Fetching customer info.', ['phone' => $request->phone]);
        $data = $this->parcels->customerDetails($request);
        return response()->json($data);
    }

    public function location(Request $request)
    {
        Log::info('ParcelController@location: Fetching parcel location.', ['parcel_id' => $request->id]);
        $data['location'] = $this->parcels->get($request->id)->location;
        return response()->json($data);
    }

    public function download($id)
    {
        Log::info('ParcelController@download: Merchant closing report.', ['merchant_id' => $id]);
        $merchant = Merchant::find($id);
        $file_name = $merchant->company . ' ' . '- Closing ' . date('Y-m-d') . '.xlsx';
        return Excel::download(new ClosingReport($id), $file_name);
    }


    public function trackParcel($id)
    {
        Log::info('ParcelController@trackParcel: Request for ID/No.', ['identifier' => $id]);
        return $this->parcels->trackParcel($id);
    }

    private function parseDate($date_range)
    {
        $dates = explode('to', $date_range);
        if (count($dates) == 1) {
            $dates[1] = $dates[0];
        }
        $start_date = trim($dates[0]);
        $end_date = trim($dates[1]);
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 23:59:59';
        return [
            Carbon::parse($start_date)->format('Y-m-d H:s:i'),
            Carbon::parse($end_date)->format('Y-m-d H:s:i'),
        ];
    }

    public function batch_print(Request $request)
    {
        Log::info('ParcelController@batch_print: Batch sticker request.', ['ids' => $request->parcel_ids]);
        $parcelIds = $request->get('parcel_ids'); 
        $parcelIdsArray = explode(',', $parcelIds);
        $parcels = Parcel::whereIn('id', $parcelIdsArray)->get();
        $qrCodes = $parcels->map(function ($parcel) {
            return QrCode::size(50)->generate($parcel->parcel_no);
        });
        return view('admin.exports.sticker_download', compact('parcels', 'qrCodes'));
    }

    public function checkReceived(Request $request)
    {
        Log::info('ParcelController@checkReceived: Validation check.', ['ids' => $request->ids]);
        $ids = (array) $request->input('ids', []);
        $notReceived = Parcel::whereIn('id', $ids)->where('status', '!=', 'received')->count();
        return response()->json([
            'all_received' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }

    public function checkDeliveryAssigned(Request $request)
    {
        Log::info('ParcelController@checkDeliveryAssigned: Validation check.', ['ids' => $request->ids]);
        $ids = (array) $request->input('ids', []);
        $notAssignedCount = Parcel::whereIn('id', $ids)->where('status', '!=', 'delivery-assigned')->count();
        return response()->json([
            'all_valid' => $notAssignedCount === 0,
            'not_assigned_count' => $notAssignedCount,
        ]);
    }

    public function checkAssignedPickupman(Request $request)
    {
        $userId = $request->attributes->get('verified_user_id') ?? (Sentinel::getUser()->id ?? null);
        Log::info('ParcelController@checkAssignedPickupman: Validation check.', [
        'ids'         => $request->ids,
        'executed_by' => $userId ?? 'Still Null', 
        'session_id'  => session()->getId(),
        'is_ajax'     => $request->ajax()
    ]);
        if (!$userId) {
        return response()->json(['error' => 'User session lost after check.'], 401);
    }
        $ids = (array) $request->input('ids', []);
        $notAssigned = Parcel::whereIn('id', $ids)->where('status', '!=', 'pickup-assigned')->count();
        return response()->json([
            'all_received' => $notAssigned === 0,
            'invalid_count' => $notAssigned,
        ]);
    }

    public function checkPickedUp(Request $request)
    {
        Log::info('ParcelController@checkPickedUp: Validation check.', ['ids' => $request->ids]);
        $ids = (array) $request->input('ids', []);
        $notReceived = Parcel::whereIn('id', $ids)->where('status', '!=', 'received-by-pickup-man')->count();
        return response()->json([
            'all_received' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }

    public function checkReturnToWarehouse(Request $request)
    {
        Log::info('ParcelController@checkReturnToWarehouse: Validation check.', ['ids' => $request->ids]);
        $ids = (array) $request->input('ids', []);
        $notReceived = Parcel::whereIn('id', $ids)->where('status', '!=', 'returned-to-warehouse')->count();
        return response()->json([
            'all_valid' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }

    public function checkReturnAssignToMerchant(Request $request)
    {
        Log::info('ParcelController@checkReturnAssignToMerchant: Validation check.', ['ids' => $request->ids]);
        $ids = (array) $request->input('ids', []);
        $notReceived = Parcel::whereIn('id', $ids)->where('status', '!=', 'return-assigned-to-merchant')->count();
        return response()->json([
            'all_valid' => $notReceived === 0,
            'invalid_count' => $notReceived,
        ]);
    }

    public function export_parcel(Request $request)
    {
        Log::info('ParcelController@export_parcel: Triggering Excel export.', ['ids' => $request->parcel_ids]);
        $parcelIdsArray = explode(',', $request->parcel_ids);
        $file_name = 'Filtered Parcels ' . date('Y-m-d-s') . '.xlsx';
        return Excel::download(new FilteredParcel(Parcel::with(['destination', 'type'])->whereIn('id', $parcelIdsArray)->latest()->limit(8000)->get()), $file_name);
    }
}