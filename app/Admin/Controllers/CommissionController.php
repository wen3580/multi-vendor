<?php

namespace App\Admin\Controllers;

use App\Models\AffiliateCommission;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CommissionController extends AdminController
{
    protected $title = 'Commissions';

    protected function grid(): Grid
    {
        $grid = new Grid(new AffiliateCommission());
        $grid->model()->latest();

        $grid->column('id', '佣金ID')->sortable();
        $grid->column('order_id', '订单号');
        $grid->column('affiliate_id', '推广员ID');
        $grid->column('commission_base_amount', '佣金基数');
        $grid->column('commission_type', '佣金类型');
        $grid->column('commission_value', '佣金值');
        $grid->column('commission_amount', '佣金金额');
        $grid->column('currency', '币种');
        $grid->column('status', '状态')->label([
            'pending' => 'warning',
            'approved' => 'info',
            'paid' => 'success',
            'void' => 'default',
            'refunded' => 'danger',
        ]);
        $grid->column('approved_at', '审核时间');
        $grid->column('paid_at', '支付时间');

        $grid->filter(function ($filter) {
            $filter->equal('affiliate_id', '推广员ID');
            $filter->equal('status', '状态')->select([
                'pending' => 'pending',
                'approved' => 'approved',
                'paid' => 'paid',
                'void' => 'void',
                'refunded' => 'refunded',
            ]);
            $filter->between('commission_amount', '金额范围');
            $filter->between('created_at', '创建时间')->datetime();
        });

        return $grid;
    }

    protected function detail($id): Show
    {
        $show = new Show(AffiliateCommission::findOrFail($id));
        $show->field('id', '佣金ID');
        $show->field('shop_id', 'Shop ID');
        $show->field('order_id', '订单号');
        $show->field('order_gid', 'Order GID');
        $show->field('affiliate_id', '推广员ID');
        $show->field('attribution_id', '归因ID');
        $show->field('commission_base_amount', '佣金基数');
        $show->field('commission_type', '佣金类型');
        $show->field('commission_value', '佣金值');
        $show->field('commission_amount', '佣金金额');
        $show->field('currency', '币种');
        $show->field('status', '状态');
        $show->field('approved_at', '审核时间');
        $show->field('paid_at', '支付时间');
        $show->field('note', '备注');

        return $show;
    }

    protected function form(): Form
    {
        $form = new Form(new AffiliateCommission());

        $form->number('shop_id', 'Shop ID')->required();
        $form->number('affiliate_id', '推广员ID')->required();
        $form->number('order_id', '订单号')->required();
        $form->text('order_gid', 'Order GID');
        $form->number('attribution_id', '归因ID');
        $form->decimal('commission_base_amount', '佣金基数')->required();
        $form->select('commission_type', '佣金类型')->options(['percent' => 'percent', 'fixed' => 'fixed'])->required();
        $form->decimal('commission_value', '佣金值')->required();
        $form->decimal('commission_amount', '佣金金额')->required();
        $form->text('currency', '币种')->default('USD')->required();
        $form->select('status', '状态')->options([
            'pending' => 'pending',
            'approved' => 'approved',
            'paid' => 'paid',
            'void' => 'void',
            'refunded' => 'refunded',
        ])->default('pending');
        $form->textarea('note', '备注');

        return $form;
    }
}
