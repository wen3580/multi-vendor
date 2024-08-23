<?php

namespace App\Admin\Controllers;

use App\Models\AffiliatePayout;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class PayoutController extends AdminController
{
    protected $title = 'Payouts';

    protected function grid(): Grid
    {
        $grid = new Grid(new AffiliatePayout());
        $grid->model()->latest();

        $grid->column('id', 'ID')->sortable();
        $grid->column('payout_no', 'Payout No');
        $grid->column('affiliate_id', '推广员ID');
        $grid->column('amount', '金额');
        $grid->column('currency', '币种');
        $grid->column('method', '打款方式');
        $grid->column('status', '状态')->label([
            'draft' => 'default',
            'processing' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
        ]);
        $grid->column('paid_at', '打款时间');
        $grid->column('created_at', '创建时间');

        return $grid;
    }

    protected function form(): Form
    {
        $form = new Form(new AffiliatePayout());

        $form->number('shop_id', 'Shop ID')->required();
        $form->number('affiliate_id', '推广员ID')->required();
        $form->text('payout_no', 'Payout No')->required();
        $form->decimal('amount', '金额')->required();
        $form->text('currency', '币种')->default('USD')->required();
        $form->select('method', '打款方式')->options([
            'paypal' => 'paypal',
            'bank' => 'bank',
            'manual' => 'manual',
        ])->default('manual');
        $form->select('status', '状态')->options([
            'draft' => 'draft',
            'processing' => 'processing',
            'paid' => 'paid',
            'failed' => 'failed',
        ])->default('draft');
        $form->datetime('paid_at', '打款时间');
        $form->textarea('note', '备注');

        return $form;
    }
}
