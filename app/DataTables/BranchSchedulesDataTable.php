<?php

namespace App\DataTables;

use App\BranchSchedule;
use Yajra\DataTables\Services\DataTable;

class BranchSchedulesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        // return datatables($query)
        //     ->addColumn('action', 'branchschedules.action');
        return datatables($query)->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\BranchSchedule $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(BranchSchedule $model)
    {
        return $model->newQuery()->select('id', 'time_from', 'time_to');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        // 'dom' => 'Bfrtip',
                        'dom' => "<'row'<'col-sm-3'l><'col-sm-6 text-center'B><'col-sm-3'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                        'paging'      => true,
                        'lengthChange'=> true,
                        'searching'   => true,
                        'ordering'    => true,
                        'info'        => true,
                        'responsive'  => true,
                        'scrollY'     => "300px",
                        'order' => [2, 'asc'],
                        'select' => [
                            'style' => 'os',
                            'selector' => 'td:nth-child(2)',
                        ],
                        'lengthMenu' => [
                            [ 10, 25, 50, -1 ],
                            [ '10', '25', '50', '100', 'All' ]
                        ],
                        'buttons' => [
                            ['extend' => 'create', 'editor' => 'editor'],
                            ['extend' => 'edit', 'editor' => 'editor'],
                            ['extend' => 'remove', 'editor' => 'editor'],
                            ['extend' => 'excelHtml5', 'text' => 'Export to Excel'],
                        ]
                    ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            [
                'data' => null,
                'defaultContent' => '',
                'className' => 'control',
                'title' => '',
                'orderable' => false,
                'searchable' => false
            ],
            [
                'data' => null,
                'defaultContent' => '',
                'className' => 'select-checkbox',
                'title' => '',
                'orderable' => false,
                'searchable' => false
            ],
            'time_from',
            'time_to',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'BranchSchedules_' . date('YmdHis');
    }
}
