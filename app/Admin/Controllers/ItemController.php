<?php

namespace App\Admin\Controllers;

use App\Item;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use DB;

class ItemController extends Controller
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

            $content->header(__('Item'));
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

            $content->header(__('Item'));
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
        return Admin::content(function (Content $content) {

            $content->header(__('Item'));
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
        return Admin::grid(Item::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name(__('Name'))->sortable();
            $grid->unit(__('Unit'))->sortable();
            $grid->price(__('Price'))->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Item::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', __('Name'))->rules('required');
            $form->select('unit', __('Unit'))->options(array('pcs'=>'pcs', 'kg'=>'kg', 'm'=>'meter'))->rules('required');
            $form->currency('price', __('Price'))->symbol('Rp')->rules('required');
            $form->textarea('description', __('Description'));

            $form->hidden('admin_user_id')->value(Admin::user()->id);

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function items(Request $request)
    {
        $q = $request->get('q');

        return Item::where('name', 'like', "%$q%")->paginate(null, array('id', DB::raw('concat(name," (",unit,") (Rp.", price, ")") as text'), 'unit', 'price'));
    }
}
