<?php

namespace App\DataTables\Admin;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use App\Models\DeliveryMan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class DeliveryManDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('name_email', function ($delivery_man) {
                return ($delivery_man->user->first_name ?? '') . ' ' . 
                    ($delivery_man->user->last_name ?? '') . '<br>' . 
                    ($delivery_man->user->email ?? '');
            })
            ->addColumn('branch', function ($delivery_man) {
                return $delivery_man->user->branch->name ?? 'Pending';
            })
            ->addColumn('address', function ($delivery_man) {
                return $delivery_man->user->address ?? 'N/A';
            })
            ->addColumn('last_login', function ($delivery_man) {
                return $delivery_man->last_login ? Carbon::parse($delivery_man->last_login)->format('Y-m-d') : 'Never';
            })
            ->addColumn('fee', function ($delivery_man) {
                return $delivery_man->fee ?? 0;
            })
            ->addColumn('status', function ($delivery_man) {
                return $delivery_man->status == 'active' ? 'Active' : 'Inactive';
            })
            ->addColumn('current_amount', function ($delivery_man) {
                return $delivery_man->current_amount ?? 0;
            })
            ->addColumn('options', function ($delivery_man) {
                return '<a href="'.route('delivery.man.edit', $delivery_man->id).'" class="btn btn-sm btn-primary">Edit</a>';
            })
            ->rawColumns(['name_email', 'options', 'status'])
            ->setRowId('id');
    }
    public function query(DeliveryMan $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['user', 'user.branch']); // Eager load relationships
        
        // Permission check
        if (!hasPermission('read_all_delivery_man')) {
            $query->whereHas('user', function ($q) {
                $q->where('branch_id', \Sentinel::getUser()->branch_id)
                    ->orWhereNull('branch_id');
            });
        }
        
        // Order by
        if ($this->request->has('order') && isset($this->request->order[0]['dir'])) {
            $orderBy = $this->request->order[0]['dir'];
            $query->orderBy('created_at', $orderBy);
        } else {
            $query->latest();
        }
        
        // Branch filter
        if ($this->request->filled('branch')) {
            $query->whereHas('user', function ($q) {
                if ($this->request->branch == 'pending') {
                    $q->whereNull('branch_id');
                } else {
                    $q->where('branch_id', $this->request->branch);
                }
            });
        }
        
        // Status filter
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        
        // Email filter
        if ($this->request->filled('email')) {
            $query->whereHas('user', function ($q) {
                $q->where('email', 'like', '%' . $this->request->email . '%');
            });
        }
        
        // Name filter
        if ($this->request->filled('name')) {
            $query->whereHas('user', function ($q) {
                $q->where('first_name', 'like', '%' . $this->request->name . '%')
                    ->orWhere('last_name', 'like', '%' . $this->request->name . '%');
            });
        }
        
        // Global search
        if ($this->request->filled('search.value')) {
            $search = $this->request->input('search.value');
            $query->where(function ($query) use ($search) {
                $query->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('phone_number', 'like', "%$search%");
                });
            });
        }
        
        return $query;
    }



    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->setTableAttribute('style', 'width:99.8%')
            ->footerCallback('function ( row, data, start, end, display ) {

                $(".dataTables_length select").addClass("form-select form-select-lg without_search mb-3");
                selectionFields();
            }')
            ->parameters([
                'dom'        => 'Blfrtip',
                'buttons'    => [
                    [],
                ],
                'lengthMenu' => [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
                'language'   => [
                    'searchPlaceholder' => __('search'),
                    'lengthMenu'        => '_MENU_ '.__('delivery_man_per_page'),
                    'search'            => '',
                ],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('id')->data('DT_RowIndex')->title('#')->searchable(false)->width(10),
            Column::computed('options')->title(__('options'))->addClass('text-center'),
            Column::computed('name_email')->title(__('name_email')),
            Column::computed('branch')->title(__('branch')),
            Column::computed('address')->title(__('address')),
            Column::computed('last_login')->title(__('login')),
            Column::computed('fee')->title(__('fee')),
            Column::computed('status')->title(__('status'))->exportable(false),
            Column::computed('current_amount')->title(__('amount'))
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center'),

        ];
    }

    protected function filename(): string
    {
        return 'delivery_man'.date('YmdHis');
    }
}
