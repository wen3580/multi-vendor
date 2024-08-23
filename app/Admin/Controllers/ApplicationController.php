<?php

namespace App\Admin\Controllers;

use App\Models\AffiliateApplication;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ApplicationController extends AdminController
{
    protected $title = 'Applications';

    protected function grid(): Grid
    {
        $grid = new Grid(new AffiliateApplication());
        $grid->model()->latest();

        $grid->column('id', '申请ID')->sortable();
        $grid->column('email', '邮箱');
        $grid->column('first_name', '名');
        $grid->column('last_name', '姓');
        $grid->column('phone', '手机');
        $grid->column('status', '状态')->dot([
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ]);
        $grid->column('reviewed_by', '审核人');
        $grid->column('reviewed_at', '审核时间');
        $grid->column('created_at', '提交时间')->sortable();

        $grid->filter(function ($filter) {
            $filter->like('email', '邮箱');
            $filter->equal('status', '状态')->select([
                'pending' => 'pending',
                'approved' => 'approved',
                'rejected' => 'rejected',
            ]);
        });

        return $grid;
    }

    protected function detail($id): Show
    {
        $show = new Show(AffiliateApplication::findOrFail($id));
        $show->field('id', '申请ID');
        $show->field('shop_id', 'Shop ID');
        $show->field('email', '邮箱');
        $show->field('first_name', '名');
        $show->field('last_name', '姓');
        $show->field('phone', '手机');
        $show->field('social_links', '社媒链接');
        $show->field('note', '备注');
        $show->field('status', '状态');
        $show->field('reviewed_by', '审核人');
        $show->field('reviewed_at', '审核时间');
        $show->field('created_at', '提交时间');

        return $show;
    }

    protected function form(): Form
    {
        $form = new Form(new AffiliateApplication());
        $form->number('shop_id', 'Shop ID')->required();
        $form->email('email', '邮箱')->required();
        $form->text('first_name', '名');
        $form->text('last_name', '姓');
        $form->mobile('phone', '手机');
        $form->textarea('social_links', '社媒链接');
        $form->textarea('note', '备注');
        $form->select('status', '状态')->options([
            'pending' => 'pending',
            'approved' => 'approved',
            'rejected' => 'rejected',
        ])->default('pending');

        return $form;
    }
}
