<?php

namespace App\Admin\Controllers;

use App\Models\AffiliateCouponCode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class CouponController extends AdminController
{
    protected $title = 'Coupons';

    protected function grid(): Grid
    {
        $grid = new Grid(new AffiliateCouponCode());
        $grid->model()->latest();

        $grid->column('id', 'ID')->sortable();
        $grid->column('code', '优惠码');
        $grid->column('affiliate_id', '推广员ID');
        $grid->column('discount_type', '折扣类型');
        $grid->column('discount_value', '折扣值');
        $grid->column('status', '状态')->dot([
            'active' => 'success',
            'inactive' => 'default',
            'expired' => 'danger',
        ]);
        $grid->column('starts_at', '开始时间');
        $grid->column('ends_at', '结束时间');
        $grid->column('created_at', '创建时间');

        return $grid;
    }

    protected function form(): Form
    {
        $form = new Form(new AffiliateCouponCode());

        $form->number('shop_id', 'Shop ID')->required();
        $form->number('affiliate_id', '推广员ID')->required();
        $form->text('discount_node_gid', 'Shopify 折扣节点ID')->required();
        $form->text('code', '优惠码')->required();
        $form->select('discount_type', '折扣类型')->options([
            'percent' => 'percent',
            'fixed' => 'fixed',
            'shipping' => 'shipping',
        ])->required();
        $form->decimal('discount_value', '折扣值')->required();
        $form->select('status', '状态')->options([
            'active' => 'active',
            'inactive' => 'inactive',
            'expired' => 'expired',
        ])->default('active');
        $form->datetime('starts_at', '开始时间');
        $form->datetime('ends_at', '结束时间');

        return $form;
    }
}
