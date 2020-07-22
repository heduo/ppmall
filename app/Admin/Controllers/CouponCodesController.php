<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CouponCodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\CouponCode';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CouponCode());

        $grid->model()->orderBy('created_at', 'desc');

        $grid->column('id', __('Id'))->sortable();

        $grid->column('name', __('Name'));
        $grid->column('code', __('Code'));
        $grid->column('description', __('Description'));
        $grid->column('type', __('Type'))->display(function ($value) {
            return CouponCode::$typeMap[$value];
        });
        $grid->column('value', __('Value'))->display(function ($value) {
            return $this->type === CouponCode::TYPE_FIXED ? 'A$' . $value : $value . '%';
        });
        // $grid->column('total', __('Total'));
        // $grid->column('used', __('Used'));
        $grid->column('usage', 'Usage')->display(function ($value) {
            return "{$this->used} / {$this->total}";
        });

        $grid->column('min_amount', __('Min amount'));
        $grid->column('not_before', __('Not before'));
        $grid->column('not_after', __('Not after'));
        $grid->column('enabled', __('Enabled'))->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->column('created_at', __('Created at'));
        // $grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {
            $actions->disableView();
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CouponCode::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('code', __('Code'));
        $show->field('type', __('Type'));
        $show->field('value', __('Value'));
        $show->field('total', __('Total'));
        $show->field('used', __('Used'));
        $show->field('min_amount', __('Min amount'));
        $show->field('not_before', __('Not before'));
        $show->field('not_after', __('Not after'));
        $show->field('enabled', __('Enabled'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CouponCode);

        $form->display('id', 'ID');
        $form->text('name', 'Name')->rules('required');
        $form->text('code', 'Code')->rules(function ($form)
        {
            if ($id = $form->model()->id) {
                return 'nullable|unique:coupon_codes,code,'.$id.',id';
            }else{
                return 'nullable|unique:coupon_codes';
            }
        });
        $form->radio('type', 'Type')->options(CouponCode::$typeMap)->rules('required')->default(CouponCode::TYPE_FIXED);
        $form->text('value', 'Discount')->rules(function ($form) {
            if (request()->input('type') === CouponCode::TYPE_PERCENT) {
                return 'required|numeric|between:1,99';
            } else {
                return 'required|numeric|min:0.01';
            }
        });
        $form->text('total', 'Total Quantity')->rules('required|numeric|min:0');
        $form->text('min_amount', 'On Minimum Amount')->rules('required|numeric|min:0');
        $form->datetime('not_before', 'Start Time');
        $form->datetime('not_after', 'End Time');
        $form->radio('enabled', 'Enable')->options(['1' => 'Yes', '0' => 'Not']);

        $form->saving(function (Form $form) {
            if (!$form->code) {
                $form->code = CouponCode::findAvailableCode();
            }
        });

        return $form;
    }
}
