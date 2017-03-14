<?php

namespace App\Admin\Controllers;

use App\Invoice;
use App\Item;
use App\Transaction;
use App\User;
use App\Wash;

use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

use DB;
use Illuminate\Support\Facades\URL;

class WashController extends Controller
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

            $content->header('Wash');
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

            $content->header('Wash');
            $content->description(__('Edit'));

            $wash = Wash::find($id);
            $headers = [__('Code'), __('Status'), __('Total Price'), __('Downpayment'),__('Debt'), __('Finished at')];
            $rows = [
                [
                    $wash->code,
                    __(ucwords($wash->status)),
                    money_format('%n', $wash->invoice->total_price),
                    money_format('%n', $wash->invoice->downpayment),
                    money_format('%n', $wash->invoice->debt),
                    $wash->finished_at
                ]
            ];
            $table = new Table($headers, $rows);
            $box = new Box('Info', $table);
            $content->row($box->solid()->style('info'));

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
        return Admin::content(function (Content $content) {

            $content->header('Wash');
            $content->description(__('Create'));

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Wash::class, function (Grid $grid) {

            $grid->created_at()->sortable();
            $grid->column('code', __('Code'))->display( function ($code) {
                $link = 'admin/wash/' . $this->id . '/edit';
                return "<a href='" . URL::to($link) . "''>" . $code . "</a>";
            });
            $grid->column('user.name', __('Customer'));
            $grid->column('admin_user.name', __('Employee'));
            $grid->status(__('Status'));
            $grid->notes(__('Notes'));
            $grid->finished_at();

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
        return Admin::form(Wash::class, function (Form $form) {

            $form->tab('Contact', function (Form $form){
                $wash = new Wash;
                $new_code = $wash->new_code;

                $form->hidden('id', 'ID');
                $form->select('status', __('Status'))->options(array('process' => __('Process'), 'finish' => __('Finish')))->default('process');
                $form->select('user_id', __('Customer'))->options(function ($id) {
                    $user = User::find($id);

                    if ($user) {
                        return array($user->id => $user->name);
                    }
                })->ajax('/admin/api/users')->rules('required');
                $form->textarea('notes', __('Notes'));


                $form->display('total_kg');
                $form->display('total_pcs');
                $form->hidden('code')->value($new_code);

                $form->display('created_at', 'Created At');
                $form->hidden('updated_at', 'Updated At');
            })->tab(__('Detail'), function (Form $form){
                $form->hasMany('wash_details', __('Washes'), function (Form\NestedForm $form) {
                    $form->select('item_id', __('Item'))->options(function ($id) {
                        $item = Item::find($id);

                        if ($item) {
                            return array($item->id => $item->name . " (" . $item->unit . ") (Rp. " . $item->price . ")", $item->price);
                        }

                    })->ajax('/admin/api/items')->rules('required');
                    $form->number('qty', __('Qty'))->default(1)->rules('numeric|min:1');
                    $form->text('notes', __('Notes'))->placeholder('ex, Jumlah Pakaian');
                    $form->currency('price', __('Price'))->symbol('Rp')->readOnly();
                    $form->currency('subtotal', __('Subtotal'))->symbol('Rp')->readOnly();
                });
            });



            $form->saved(function (Form $form) {

                $wash_id = $form->model()->id;

                $total_price = 0;
                $total_pcs = 0;
                $total_kg = 0;

                /*
                 * Update Wash
                 */
                $wash = Wash::find($wash_id);
                if (!empty($wash)) {
                    foreach ($form->wash_details as $key => $wash_detail){

                        $item = Item::find($wash_detail['item_id']);
                        if($item->unit == 'pcs')
                            $total_pcs += $wash_detail['qty'];
                        else
                            $total_kg += $wash_detail['qty'];

                        $total_price += $item->price * $wash_detail['qty'];
                    }
                    $wash->total_pcs = $total_pcs;
                    $wash->total_kg = $total_kg;

                    $wash->finished_at = true; // call finished_at

                    $wash->save();
                }

                /*
                 * Create empty Invoice
                 */
                $wash_invoice = Invoice::where('wash_id', $wash_id)->first();
                // check if there is no transaction
                if (empty($wash_invoice))
                {
                    // creating new blank transaction
                    $invoice = new Invoice();
                    $invoice->wash_id = $wash_id;
                    $invoice->user_id = $form->user_id;
                    $invoice->downpayment = 0;
                    $invoice->total_price = $total_price;
                    $invoice->debt = $total_price;
                    $invoice->save();
                }
                else
                {
                    $wash_invoice->total_price = $total_price;
                    $wash_invoice->debt = $total_price;
                    $wash_invoice->save();
                }

                return redirect('/admin/wash/'.$wash_id.'/edit');

            });
        });
    }

    public function washes(Request $request)
    {
        $q = $request->get('q');
        return Wash::where('code', 'like', "%$q%")->join('users', 'washes.user_id', '=', 'users.id')->paginate(null, array('washes.id', DB::raw('concat(users.name) as text'), 'name'));
    }
}
