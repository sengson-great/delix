<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BankingPayments;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Account\WithdrawBatchRequest;
use App\Models\Account\Account;
use App\Models\Account\MerchantWithdraw;
use App\Models\PaymentMethod;
use App\Enums\PaymentMethodType;
use App\Models\WithdrawBatch;
use App\DataTables\Admin\BulkPaymentDataTable;
use App\Models\MerchantPaymentAccount;
use App\Repositories\Interfaces\Admin\BankAccountInterface;
use App\Repositories\Interfaces\Admin\BulkWithdrawInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class BulkWithdrawController extends Controller
{
    protected $withdraws;
    protected $bank_accounts;

    public function __construct(BulkWithdrawInterface $withdraws, BankAccountInterface $bank_accounts)
    {
        $this->withdraws = $withdraws;
        $this->bank_accounts = $bank_accounts;
    }

    public function index(BulkPaymentDataTable $dataTable, Request $request)
    {
        $accounts = Account::all()->where('user_id', Sentinel::getUser()->id);
        $withdraws = $this->withdraws->paginate(\Config::get('parcel.paginate'));

        return $dataTable
            ->render('admin.withdraws.bulk.index', compact('withdraws', 'accounts'));
    }

public function create()
{
    $preferences = settingHelper('preferences');
    
    // Fix: Search by 'key' instead of 'title'
    $preference = $preferences->where('key', 'create_payment_request')->first();
    
    // Check if preference exists and has value '1'
    if ($preference && $preference->value == '1') {
        $accounts = Account::all()->where('user_id', Sentinel::getUser()->id);
        $methods = PaymentMethod::where('status', 'active')->get();

        return view('admin.withdraws.bulk.create', compact('accounts', 'methods'));
    } else {
        return back()->with('danger', __('service_unavailable'));
    }
}

public function store(WithdrawBatchRequest $request)
{
    if (isDemoMode()) {
        Toastr::error(__('this_function_is_disabled_in_demo_server'));
        return back();
    }
    
    try {
        // TEMPORARY: Bypass permission check for testing
        // Remove this after testing!
        
        if ($this->withdraws->store($request)) {
            return redirect()->route('admin.withdraws.bulk')->with('success', __('created_successfully'));
        } else {
            return back()->with('danger', __('something_went_wrong_please_try_again'))->withInput();
        }
        
    } catch (\Exception $e) {
        \Log::error('Store method exception', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()->with('danger', __('something_went_wrong_please_try_again'))->withInput();
    }
}

    public function edit($id)
    {
        try {
            $batch = $this->withdraws->get($id);
            $methods = PaymentMethod::where('status', 'active')->get();

            return view('admin.withdraws.bulk.edit', compact('batch', 'methods'));
        } catch (\Exception $e) {
            return back()->with('danger', __('not_found'));
        }
    }

    public function update(WithdrawBatchRequest $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            if ($this->withdraws->update($request)):
                return redirect()->route('admin.withdraws.bulk')->with('success', __('updated_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function processPayment(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $batch = $this->withdraws->get($request->id);

            $total_withdraw_amount = $batch->withdraws->sum('amount');

            $balance = $this->bank_accounts->bankRemainingBalance('', $request->account, '', '');

            if ($balance < $total_withdraw_amount) {
                return back()->with('danger', __('the_account_has_no_available_balance'));
            }

            if ($this->withdraws->changeStatus($request->id, 'processed', $request)):
                return back()->with('success', __('processed_successfully'));
            else:
                return back()->with('danger', __('something_went_wrong_please_try_again'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }

    public function batches(Request $request)
    {
        $withdraw = MerchantWithdraw::find($request->withdraw_id);
        $withdraws = MerchantPaymentAccount::where('id', $withdraw->withdraw_to)->with('paymentAccount')->first();
        if ($withdraw->payment_method_type != 'cash') {
            $batches = WithdrawBatch::where('status', 'pending')->where('batch_type', $withdraws->paymentAccount->name)->get();
        }
        if ($withdraw->payment_method_type == 'cash') {
            $batches = WithdrawBatch::where('status', 'pending')->where('batch_type', 'Offline')->get();
        }

        return view('admin.withdraws.batch-options', compact('batches', 'withdraw'))->render();
    }

    public function invoice($id)
    {
        $withdraw = $this->withdraws->get($id);

        return view('admin.withdraws.bulk.invoice', compact('withdraw'));
    }


public function download($id)
{
    if (isDemoMode()) {
        Toastr::error(__('this_function_is_disabled_in_demo_server'));
        return back();
    }
    
    try {
        \Log::info('========== DOWNLOAD STARTED ==========');
        \Log::info('Download requested for batch ID: ' . $id);
        
        // Get the batch
        $batch = $this->withdraws->get($id);
        \Log::info('Batch retrieved:', $batch ? $batch->toArray() : 'null');
        
        if (!$batch) {
            \Log::error('Batch not found with ID: ' . $id);
            return back()->with('danger', __('Batch not found'));
        }
        
        // Check each field
        \Log::info('Batch number: ' . ($batch->batch_number ?? 'NULL'));
        \Log::info('Type: ' . ($batch->type ?? 'NULL'));
        \Log::info('Title: ' . ($batch->title ?? 'NULL'));
        
        // Use the correct column names
        $batch_type = $batch->type ?? 'Unknown';
        $batch_number = $batch->batch_number ?? 'BATCH-' . $id;
        
        // Generate filename
        $file_name = $batch_number . ' - ' . date('Y-m-d') . '(' . $batch_type . ')' . '.xlsx';
        \Log::info('Generated filename: ' . $file_name);
        
        // Check if BankingPayments class exists
        if (!class_exists('App\Exports\BankingPayments')) {
            \Log::error('BankingPayments class not found');
            return back()->with('danger', 'Export class not found');
        }
        
        \Log::info('Attempting to create BankingPayments instance');
        $export = new \App\Exports\BankingPayments($id, $batch_number, $batch_type);
        \Log::info('BankingPayments instance created');
        
        \Log::info('Attempting to download');
        return Excel::download($export, $file_name);
        
    } catch (\Exception $e) {
        \Log::error('========== DOWNLOAD EXCEPTION ==========');
        \Log::error('Message: ' . $e->getMessage());
        \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
        \Log::error('Trace: ' . $e->getTraceAsString());
        
        return back()->with('danger', __('something_went_wrong_please_try_again') . ': ' . $e->getMessage());
    }
}



    public function delete($id)
    {
        if (isDemoMode()) {
            $success = __('this_function_is_disabled_in_demo_server');
            return response()->json([
                'status' => 500,
                'message' => $success,
            ]);
        }
        try {
            if ($this->withdraws->delete($id)):
                $success[0] = __('deleted_successfully');
                $success[1] = 'success';
                $success[2] = __('deleted');
                return response()->json($success);
            else:
                $success[0] = __('something_went_wrong_please_try_again');
                $success[1] = 'error';
                $success[2] = __('oops');
                return response()->json($success);
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
}
