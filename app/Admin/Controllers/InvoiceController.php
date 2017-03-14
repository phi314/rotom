<?php

namespace App\Admin\Controllers;

use App\Invoice;

use App\Transaction;
use App\Wash;
use App\Wash_detail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class InvoiceController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header(__('Invoice'));
            $content->description(__('Listing'));

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header(__('Invoice'));
            $content->description(__('Edit'));

            $invoice = Invoice::find($id);
            $last_transaction_pay = Transaction::getLastTransactionPay($invoice->id);

            /*
             * Wash Info
             */
            $wash = Wash::find($invoice->wash_id);
            $headers = [__('Code'), __('Customer'), __('Status'), __('Total Price'), __('Last Transaction'), __('Finished at')];
            $rows = [
                [
                    $wash->code,
                    $wash->user->name.' ('.$wash->user->phone.') ',
                    __(ucwords($wash->status)),
                    money_format('%n', $invoice->total_price),
                    money_format('%n', $last_transaction_pay),
                    $wash->finished_at
                ]
            ];
            $table = new Table($headers, $rows);
            $box = new Box(__('Info'), $table);
            $content->row($box->solid()->style('info'));

            /*
             * Detail Wash
             */
            $wash_details = Wash_detail::where('wash_id', $wash->id)->get();
            $headers = [__('Item'), __('Qty'), __('Price'), __('Subtotal'), __('Notes')];
            $rows = [];
            foreach ($wash_details as $wash_detail) {
                $rows[] = [
                    $wash_detail->item->name,
                    $wash_detail->qty.' ('.$wash_detail->item->unit.')',
                    money_format('%n', $wash_detail->price),
                    money_format('%n', $wash_detail->subtotal),
                    $wash_detail->notes
                ];
            }
            $table = new Table($headers, $rows);
            $box = new Box(__('Wash Details'), $table);
            $content->row($box->solid()->style('success'));
            $content->body($this->form()->edit($id));
            $content->body(new InfoBox('Print', 'print', 'aqua', '/admin/invoice/'.$id.'/pdf', 'Print'));

        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $error = new MessageBag([
                'title'   => __('Warning'),
                'message' => __('Must have washes'),
            ]);

            return back()->with(compact('error'));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Invoice::class, function (Grid $grid) {

            $grid->created_at()->sortable();
            $grid->column('wash.code', __('Code'))->display( function ($code) {
                $link = 'admin/invoice/' . $this->id . '/edit';
                return "<a href='" . URL::to($link) . "''>" . $code . "</a>";
            });
            $grid->column('user.name', __('Customer'));
            $grid->column(__('Detail'))->expand(function () {

                $wash = (array) $this->wash;
                $wash_details = Wash_detail::where('wash_id', '=', $wash['id'])->get();

                $row = [];
                foreach($wash_details as $wash_detail)
                {
                    $row[] = [
                        $wash_detail->item->name,
                        $wash_detail->price,
                        $wash_detail->qty,
                        $wash_detail->item->unit,
                        $wash_detail->subtotal,
                        $wash_detail->notes
                    ];
                }

                return new Table([__('Name'), __('Price'), __('Qty'), __('Unit'), __('Subtotal'), __('Notes')], $row);

            }, 'Wash');
            $grid->downpayment(__('Downpayment'));
            $grid->debt(__('Debt'));
            $grid->total_price(__('Total Price'));
            $grid->column('admin_user.name', __('Employee'));
            $grid->updated_at();

            $grid->disableCreation();

            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });

            $grid->model()->orderBy('created_at', 'desc');

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Invoice::class, function (Form $form) {

            $form->hidden('id', 'ID');
            $form->hidden('wash_id', 'ID');

            $form->currency('downpayment', __('Downpayment'))->symbol('Rp')->rules('numeric')->readOnly();;
            $form->currency('debt', __('Debt'))->symbol('Rp')->readOnly();
            $form->currency('total_price', __('Total Price'))->symbol('Rp')->readOnly();
            $form->display('created_at', __('Created At'));
            $form->hidden('updated_at', 'Updated At');

            $form->hasMany('transactions', __('Transaction'), function (Form\NestedForm $form) {
                $form->currency('pay', __('Pay'))->symbol('Rp');
                $form->radio('is_accepted', __('Accepted'))->options([1 => 'Yes', 0=> 'No'])->default(1);
                $form->display('created_at', __('Created At'));
                $form->disableRemoveHasMany();
            });

            $form->disableReset();

            $form->saved(function (Form $form) {

                $transactions = Transaction::where('invoice_id', $form->id)->where('is_accepted', true)->get();
                $invoice = Invoice::find($form->id);

                $total_transaction = 0;

                // if transaction is not empty
                if (!empty($transactions))
                {
                    foreach($transactions as $transaction)
                    {
                        $total_transaction += $transaction->pay;
                    }

                    $invoice->downpayment = $total_transaction;
                    $invoice->debt = $invoice->total_price - $total_transaction;
                }
                else
                {
                    $invoice->debt = $invoice->total_price;
                }

                $invoice->save();

                return redirect('/admin/invoice/'.$invoice->id.'/edit');
            });
        });
    }

    public function pdf(Request $request)
    {
        $invoice_id = $request->id;
        $invoice = Invoice::find($invoice_id)->first();

        $wash_details = Wash_detail::where('wash_id', $invoice->wash_id)->get();

        $data = [
            'created_at' => strftime('%A, %d %B %Y %k:%M', strtotime(Carbon::now())),
            'invoice' => $invoice,
            'wash_details' => $wash_details
        ];

        $pdf = PDF::loadView('pdf/invoice', $data);
        return $pdf->stream('invoce.pdf');
    }
}
