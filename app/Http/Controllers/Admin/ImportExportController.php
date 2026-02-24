<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ParcelsImport;
use App\Models\Shop;
use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Log;
use Maatwebsite\Excel\Validators\ValidationException;


class ImportExportController extends Controller
{
    public function importExportView()
    {
        $merchants = User::where('user_type', 'merchant')->get();
        return view('admin.bulk.import', compact('merchants'));
    }
    
    public function getShopsByMerchant(Request $request)
    {
        $shops = Shop::where('merchant_id', $request->merchant_id)->select('id', 'shop_name')
            ->get();
        return response()->json($shops);
    }

    public function export()
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        try {
            $filename = (Sentinel::getUser()->user_type == 'merchant' || Sentinel::getUser()->user_type == 'merchant_staff') 
                ? 'admin/excel/merchant-parcel-import-sample.xlsx' 
                : 'admin/excel/staff-parcel-import-sample.xlsx';
                
            if (file_exists(public_path($filename))):
                $filepath = public_path($filename);
                return Response::download($filepath);
            else:
                return back()->with('danger', __('file_not_found'));
            endif;
        } catch (\Exception $e) {
            return back()->with('danger', __('something_went_wrong_please_try_again'));
        }
    }
    
    public function import(Request $request)
    {
        if (isDemoMode()) {
            Toastr::error(__('this_function_is_disabled_in_demo_server'));
            return back();
        }
        
        try {
            // Check if file exists in the request
            if (!$request->hasFile('file')) {
                return back()->with('danger', __('please_select_a_file'));
            }
            
            $file = $request->file('file');
            
            // Check if file is valid
            if (!$file->isValid()) {
                return back()->with('danger', __('file_upload_failed'));
            }
            
            // Check file extension
            $extension = $file->getClientOriginalExtension();
            $allowedExtensions = ['xlsx', 'csv', 'xls'];
            
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return back()->with('danger', __('file_type_not_supported') . ' (' . $extension . '). ' . __('allowed_types') . ': ' . implode(', ', $allowedExtensions));
            }
            
            // Check file size (max 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                return back()->with('danger', __('file_too_large_max_5mb'));
            }
            
            // Store the file
            $path = $file->store('imports');
            
            if (!$path) {
                return back()->with('danger', __('file_upload_failed'));
            }
            
            // Import the file
            $import = new ParcelsImport();
            $import->import($path);
            
            // Delete the file after import
            if (file_exists(storage_path('app/' . $path))) {
                unlink(storage_path('app/' . $path));
            }
            
            return back()->with('success', __('successfully_imported'));
            
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            
            foreach ($failures as $failure) {
                $messages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            $errorMessage = implode('<br>', $messages);
            
            // Clean up file if it exists
            if (isset($path) && file_exists(storage_path('app/' . $path))) {
                unlink(storage_path('app/' . $path));
            }
            
            return back()->with('danger', $errorMessage);
            
        } catch (\Maatwebsite\Excel\Exceptions\NoTypeDetectedException $e) {
            // Clean up file if it exists
            if (isset($path) && file_exists(storage_path('app/' . $path))) {
                unlink(storage_path('app/' . $path));
            }
            
            return back()->with('danger', __('invalid_file_format'));
            
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            Log::error('Import trace: ' . $e->getTraceAsString());
            
            // Clean up file if it exists
            if (isset($path) && file_exists(storage_path('app/' . $path))) {
                unlink(storage_path('app/' . $path));
            }
            
            return back()->with('danger', __('something_went_wrong_please_try_again') . ': ' . $e->getMessage());
        }
    }
}