<?php

namespace App\Admin\Controllers;

use App\Models\AffiliateAttribution;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AttributionController extends AdminController
{
    protected $title = 'Attributions';

    protected function grid(): Grid
    {
        $grid = new Grid(new AffiliateAttribution());
        $grid->model()->latest();

        $grid->column('id', 'ID')->sortable();
        $grid->column('order_id', '订单号');
        $grid->column('affiliate_id', '推广员ID');
        $grid->column('attribution_type', '归因方式')->using([
            'coupon' => 'coupon',
            'cookie' => 'cookie',
            'manual' => 'manual',
        ])->label([
            'coupon' => 'success',
            'cookie' => 'primary',
            'manual' => 'warning',
        ]);
        $grid->column('coupon_code', '优惠码');
        $grid->column('ref_code', 'ref code');
        $grid->column('click_uuid', 'click uuid');
        $grid->column('attributed_at', '归因时间');

        $grid->filter(function ($filter) {
            $filter->equal('order_id', '订单号');
            $filter->equal('affiliate_id', '推广员ID');
            $filter->equal('attribution_type', '归因方式')->select([
                'coupon' => 'coupon',
                'cookie' => 'cookie',
                'manual' => 'manual',
            ]);
            $filter->between('attributed_at', '归因时间')->datetime();
        });

        $grid->disableCreateButton();

        return $grid;
    }

    protected function detail($id): Show
    {
        $show = new Show(AffiliateAttribution::findOrFail($id));
        $show->field('id', 'ID');
        $show->field('shop_id', 'Shop ID');
        $show->field('order_id', '订单号');
        $show->field('order_gid', 'Shopify Order GID');
        $show->field('affiliate_id', '推广员ID');
        $show->field('click_uuid', 'Click UUID');
        $show->field('attribution_type', '归因方式');
        $show->field('coupon_code', '优惠码');
        $show->field('ref_code', 'Ref Code');
        $show->field('attributed_at', '归因时间');
        $show->field('created_at', '创建时间');

        return $show;
    }
}
