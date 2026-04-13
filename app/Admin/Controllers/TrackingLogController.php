<?php

namespace App\Admin\Controllers;

use App\Models\AffiliateClick;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;

class TrackingLogController extends AdminController
{
    protected $title = 'Tracking Logs';

    protected function grid(): Grid
    {
        $grid = new Grid(new AffiliateClick());
        $grid->model()->latest();

        $grid->column('id', 'ID')->sortable();
        $grid->column('click_uuid', 'Click UUID');
        $grid->column('affiliate_id', '推广员ID');
        $grid->column('ref_code', 'Ref Code');
        $grid->column('landing_url', 'Landing URL')->limit(40);
        $grid->column('referer', 'Referer')->limit(40);
        $grid->column('ip', 'IP');
        $grid->column('created_at', '点击时间')->sortable();

        $grid->filter(function ($filter) {
            $filter->equal('affiliate_id', '推广员ID');
            $filter->like('ref_code', 'Ref Code');
            $filter->between('created_at', '点击时间')->datetime();
        });

        $grid->disableCreateButton();

        return $grid;
    }
}
