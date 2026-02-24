<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\DeliveryManInterface;
use App\Http\Requests\Admin\DeliveryMan\DeliveryManStoreRequest;
use App\Http\Requests\Admin\DeliveryMan\DeliveryManUpdateRequest;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\RepoResponseTrait;
use App\Models\DeliveryMan;
use App\Models\LogActivity;
use Brian2694\Toastr\Facades\Toastr;
use App\DataTables\Admin\DeliveryManDataTable;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Log;

class DeliveryManController extends Controller
{
    use ApiReturnFormatTrait, RepoResponseTrait;
    protected $delivery_man;

    public function __construct(DeliveryManInterface $delivery_man)
    {
        $this->delivery_man = $delivery_man;
    }

    public function index(DeliveryManDataTable $dataTable, Request $request)
    {
        try {
            // Get all branches for the filter dropdown
            $branchs = Branch::all();
            
            // Get total count for the header
            $totalDeliveryMen = DeliveryMan::count();
            
            Log::info('DeliveryManController index method', [
                'user_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null,
                'branchs_count' => $branchs->count(),
                'total_delivery_men' => $totalDeliveryMen
            ]);
            
            // Pass data to the DataTable and view
            return $dataTable
                ->with([
                    'branchs' => $branchs,
                    'total_delivery_men' => $totalDeliveryMen
                ])
                ->render('admin.delivery-man.index', compact('branchs', 'totalDeliveryMen'));
                
        } catch (\Exception $e) {
            Log::error('DeliveryManController index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back();
        }
    }

    public function create()
    {
        $branchs = Branch::all();
        return view('admin.delivery-man.create', compact('branchs'));
    }

    public function store(DeliveryManStoreRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        
        try {
            $result = $this->delivery_man->store($request);
            
            Log::info('Delivery Man Store Result', [
                'result' => $result,
                'request' => $request->all()
            ]);
            
            if ($result) {
                Toastr::success(__('created_successfully'));
                return redirect()->route('delivery.man')->with('success', 'Delivery man created successfully');
            } else {
                Toastr::error(__('something_went_wrong_please_try_again'));
                return back()->with('error', __('something_went_wrong_please_try_again'))->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Delivery Man Store Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Toastr::error(__('something_went_wrong_please_try_again'));
            return back()->with('error', __('something_went_wrong_please_try_again'))->withInput();
        }
    }

    public function edit($id)
    {
        $delivery_man = DeliveryMan::find($id);
        
        if (hasPermission('read_all_delivery_man') || 
            ($delivery_man->created_by == Sentinel::getUser()->id)) {
            
            $branchs = Branch::all();
            return view('admin.delivery-man.edit', compact('delivery_man', 'branchs'));
        } else {
            return back()->with('danger', __('access_denied'));
        }
    }

    public function update(DeliveryManUpdateRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        
        try {
            if ($this->delivery_man->update($request)) {
                return redirect()->route('delivery.man')->with('success', __('updated_successfully'));
            }
        } catch (\Exception $e) {
            Log::error('Delivery Man Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function delete($id)
    {
        if (isDemoMode()) {
            return response()->json([
                'status' => 'error',
                'message' => __('this_function_is_disabled_in_demo_server')
            ]);
        }
        
        try {
            $delivery_man = $this->delivery_man->get($id);
            
            if (hasPermission('read_all_delivery_man') || 
                ($delivery_man->created_by == Sentinel::getUser()->id)) {
                
                if ($this->delivery_man->delete($id)) {
                    return response()->json([
                        'status' => 'success',
                        'message' => __('deleted_successfully')
                    ]);
                }
            }
            
            return response()->json([
                'status' => 'error',
                'message' => __('something_went_wrong_please_try_again')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delivery Man Delete Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => __('something_went_wrong_please_try_again')
            ]);
        }
    }

    public function statusChange(Request $request)
    {
        if (isDemoMode()) {
            return response()->json([
                'status' => 400,
                'message' => __('this_function_is_disabled_in_demo_server')
            ]);
        }
        
        try {
            $status = $this->delivery_man->statusChange($request);
            
            if ($status) {
                return response()->json([
                    'status' => 200,
                    'message' => __('updated_successfully')
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Delivery Man Status Change Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 400,
                'message' => __('something_went_wrong_please_try_again')
            ]);
        }
    }

    public function personalInfo($id)
    {
        $delivery_man = $this->delivery_man->get($id);
        
        if (hasPermission('read_all_delivery_man') || 
            ($delivery_man->created_by == Sentinel::getUser()->id)) {
            
            return view('admin.delivery-man.details.personal-info', compact('delivery_man'));
        } else {
            return back()->with('danger', __('access_denied'));
        }
    }

    public function accountActivity($id)
    {
        $delivery_man = $this->delivery_man->get($id);
        
        if (hasPermission('read_all_delivery_man') || 
            ($delivery_man->created_by == Sentinel::getUser()->id)) {
            
            $login_activities = LogActivity::where('user_id', $delivery_man->user_id)
                ->orderBy('id', 'desc')
                ->paginate(5);
                
            return view('admin.delivery-man.details.account-activity', compact('login_activities', 'delivery_man'));
        } else {
            return back()->with('danger', __('access_denied'));
        }
    }

    public function filter(Request $request)
    {
        $branchs = Branch::all();
        $delivery_men = $this->delivery_man->filter($request);
        
        return view('admin.delivery-man.index', compact('delivery_men', 'branchs'));
    }

    public function statements($id)
    {
        $delivery_man = $this->delivery_man->get($id);
        $statements = $delivery_man->accountStatements()->paginate(5);
        
        return view('admin.delivery-man.details.statements', compact('statements', 'delivery_man'));
    }
}