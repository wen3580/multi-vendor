<?php

namespace App\Admin\Controllers;

use App\Models\Affiliate;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AffiliateController extends AdminController
{
    protected $title = 'Affiliates';

    protected function grid(): Grid
    {
        $grid = new Grid(new Affiliate());
        $grid->model()->latest();

        $grid->column('id', 'ID')->sortable();
        $grid->column('email', '邮箱');
        $grid->column('first_name', '名');
        $grid->column('last_name', '姓');
        $grid->column('phone', '手机');
        $grid->column('status', '状态')->label([
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'default',
            'blocked' => 'danger',
        ]);
        $grid->column('referral_code', 'Referral Code');
        $grid->column('default_discount_code', '默认优惠码');
        $grid->column('created_at', '创建时间')->sortable();

        $grid->filter(function ($filter) {
            $filter->like('email', '邮箱');
            $filter->equal('status', '状态')->select([
                'pending' => 'pending',
                'approved' => 'approved',
                'rejected' => 'rejected',
                'blocked' => 'blocked',
            ]);
            $filter->between('created_at', '创建时间')->datetime();
            $filter->equal('default_discount_code', '有默认 coupon')->radio([
                '' => '全部',
                '1' => '有',
                '0' => '无',
            ])->where(function ($query) {
                if ($this->input === '1') {
                    $query->whereNotNull('default_discount_code');
                }
                if ($this->input === '0') {
                    $query->whereNull('default_discount_code');
                }
            });
        });

        return $grid;
    }

    protected function detail($id): Show
    {
        $show = new Show(Affiliate::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('email', '邮箱');
        $show->field('first_name', '名');
        $show->field('last_name', '姓');
        $show->field('phone', '手机');
        $show->field('status', '状态');
        $show->field('referral_code', 'Referral Code');
        $show->field('referral_slug', 'Referral Slug');
        $show->field('default_discount_code', '默认优惠码');
        $show->field('commission_type', '佣金类型');
        $show->field('commission_value', '佣金值');
        $show->field('payout_method', '收款方式');
        $show->field('payout_account', '收款账号');
        $show->field('notes', '备注');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });

        return $show;
    }

    protected function form(): Form
    {
        $form = new Form(new Affiliate());

        $form->number('shop_id', 'Shop ID')->required();
        $form->email('email', '邮箱')->required();
        $form->text('first_name', '名');
        $form->text('last_name', '姓');
        $form->mobile('phone', '手机');
        $form->select('status', '状态')->options([
            'pending' => 'pending',
            'approved' => 'approved',
            'rejected' => 'rejected',
            'blocked' => 'blocked',
        ])->default('pending')->required();

        $form->divider('推广配置');
        $form->text('referral_code', 'Referral Code')->required();
        $form->text('referral_slug', 'Referral Slug');
        $form->text('default_discount_code', '默认优惠码');

        $form->divider('佣金配置');
        $form->select('commission_type', '佣金类型')->options(['percent' => 'percent', 'fixed' => 'fixed']);
        $form->decimal('commission_value', '佣金值');

        $form->divider('收款配置');
        $form->select('payout_method', '收款方式')->options(['paypal' => 'paypal', 'bank' => 'bank', 'manual' => 'manual']);
        $form->text('payout_account', '收款账号');
        $form->textarea('notes', '备注');

        $form->saving(function (Form $form) {
            if ($form->status === 'approved' && empty($form->model()->commission_type)) {
                $form->commission_type = $form->commission_type ?: 'percent';
                $form->commission_value = $form->commission_value ?: 10;
            }
        });

        return $form;
    }
}
