<?php

namespace App\Admin\Controllers;

use App\Models\WebhookLog;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;

class WebhookLogController extends AdminController
{
    protected $title = 'Webhook Logs';

    protected function grid(): Grid
    {
        $grid = new Grid(new WebhookLog());
        $grid->model()->latest();

        $grid->column('id', 'ID')->sortable();
        $grid->column('topic', 'Topic');
        $grid->column('webhook_id', 'Webhook ID');
        $grid->column('shop_id', 'Shop ID');
        $grid->column('hmac_valid', 'HMAC')->bool();
        $grid->column('process_status', '处理状态')->label([
            'pending' => 'warning',
            'processed' => 'success',
            'failed' => 'danger',
            'ignored' => 'default',
        ]);
        $grid->column('error_message', '错误信息')->limit(40);
        $grid->column('created_at', '创建时间')->sortable();

        $grid->filter(function ($filter) {
            $filter->like('topic', 'Topic');
            $filter->equal('process_status', '处理状态')->select([
                'pending' => 'pending',
                'processed' => 'processed',
                'failed' => 'failed',
                'ignored' => 'ignored',
            ]);
        });

        $grid->disableCreateButton();

        return $grid;
    }
}
