<?php

namespace Modules\Order\Http\Controllers\Admin;

use Modules\Order\Entities\OrderStatus;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Order\Admin\OrderStatusTable;
use Modules\Order\Admin\OrderStatusTabs;
use Modules\Order\Http\Requests\SaveOrderStatusRequest;

class OrderStatusController
{
    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = OrderStatus::class;

    /**
     * The weights/tabs configurations manager class name.
     *
     * @var string
     */
    protected $tabs = OrderStatusTabs::class;

    protected $resourceName = 'order_status';

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'order::statuses.status';

    /**
     * View path of the resource files.
     *
     * @var string
     */
    protected $viewPath = 'order::admin.order_statuses';

    /**
     * Form validation request selector name.
     *
     * @var string
     */
    protected $validation = SaveOrderStatusRequest::class;


}
