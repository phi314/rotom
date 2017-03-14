<?php

namespace App\Admin\Controllers;

use App\Transaction;

use App\Wash;
use App\Wash_detail;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;

class TransactionController extends Controller
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

            $content->header(__('Transaction'));
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

            $content->header(__('Transaction'));
            $content->description(__('Edit'));

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        $error = new MessageBag([
            'title'   => __('Warning'),
            'message' => __('Mush have invoice'),
        ]);

        return back()->with(compact('error'));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Transaction::class, function (Grid $grid) {

            $grid->created_at()->sortable();
            $grid->column('wash.code', __('Code'))->display( function ($code) {
                $link = 'admin/transaction/' . $this->id . '/edit';
                return "<a href='" . URL::to($link) . "''>" . $code . "</a>";
            });
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
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Transaction::class, function (Form $form) {

            $form->hidden('id', 'ID');
            $form->hidden('wash_id', 'ID');

            $form->currency('downpayment', __('Downpayment'))->symbol('Rp')->rules('numeric')->readOnly();;
            $form->currency('pay', __('Pay'))->symbol('Rp')->rules('numeric');
            $form->currency('pay_change', __('Pay Change'))->symbol('Rp')->readOnly();
            $form->currency('debt', __('Debt'))->symbol('Rp')->readOnly();
            $form->currency('total_price', __('Total Price'))->symbol('Rp')->readOnly();

            $form->display('created_at', 'Created At');
            $form->hidden('updated_at', 'Updated At');

        });
    }
}
