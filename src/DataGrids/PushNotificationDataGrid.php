<?php

namespace Webkul\GraphQLAPI\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Core\Models\Channel;
use Webkul\Core\Models\Locale;
use Webkul\DataGrid\DataGrid;
use Illuminate\Support\Facades\Storage;

class PushNotificationDataGrid extends DataGrid
{
    /**
     * Default sort order of datagrid.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Set index columns, ex: id.
     *
     * @var string
     */
    protected $primaryColumn = 'push_notification_id';

    /**
     * If paginated then value of pagination.
     *
     * @var int
     */
    protected $itemsPerPage = 10;


    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        if (core()->getRequestedChannelCode() === 'all') {
            $whereInChannels = Channel::query()->pluck('code')->toArray();
        } else {
            $whereInChannels = [core()->getRequestedChannelCode()];
        }

        if (core()->getRequestedLocaleCode() === 'all') {
            $whereInLocales = Locale::query()->pluck('code')->toArray();
        } else {
            $whereInLocales = [core()->getRequestedLocaleCode()];
        }

        $queryBuilder = DB::table('push_notification_translations as pn_trans')
        ->leftJoin('push_notifications as pn', 'pn_trans.push_notification_id', '=', 'pn.id')
        ->leftJoin('channels as ch', 'pn_trans.channel', '=', 'ch.code')
        ->leftJoin('channel_translations as ch_t', 'ch.id', '=', 'ch_t.channel_id')
        ->addSelect(
            'pn_trans.push_notification_id as notification_id',
            'pn.image',
            'pn_trans.title',
            'pn_trans.content',
            'pn_trans.channel',
            'pn_trans.locale',
            'pn.type',
            'pn.product_category_id',
            'pn.status',
            'pn.created_at',
            'pn.updated_at',
            'ch_t.name as channel_name'
        );

        $queryBuilder->groupBy('pn_trans.push_notification_id', 'pn_trans.channel', 'pn_trans.locale');

        $queryBuilder->whereIn('pn_trans.locale', $whereInLocales);
        $queryBuilder->whereIn('pn_trans.channel', $whereInChannels);

        $this->addFilter('notification_id', 'pn_trans.push_notification_id');
        $this->addFilter('title', 'pn_trans.title');
        $this->addFilter('content', 'pn_trans.content');
        $this->addFilter('channel_name', 'ch_t.name');
        $this->addFilter('status', 'pn.status');
        $this->addFilter('type', 'pn.type');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'notification_id',
            'label'      => trans('bagisto_graphql::app.admin.notification.id'),
            'type'       => 'number',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'image',
            'label'      => trans('bagisto_graphql::app.admin.notification.image'),
            'type'       => 'html',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($value) {
                return '<img src=' . Storage::url($value->image) . ' class="img-thumbnail" width="100px" height="70px" />';
            },
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('bagisto_graphql::app.admin.notification.text-title'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'content',
            'label'      => trans('bagisto_graphql::app.admin.notification.notification-content'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('bagisto_graphql::app.admin.notification.notification-type'),
            'type'       => 'price',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'channel_name',
            'label'      => trans('bagisto_graphql::app.admin.notification.store-view'),
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => false,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('bagisto_graphql::app.admin.notification.notification-status'),
            'type'       => 'boolean',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($value) {
                if ($value->status) {
                    return '<span class="badge badge-md badge-success">' . trans('bagisto_graphql::app.admin.notification.status.enabled') . '</span>';
                }

                return '<span class="badge badge-md badge-danger">' . trans('bagisto_graphql::app.admin.notification.status.disabled') . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('bagisto_graphql::app.admin.notification.created'),
            'type'       => 'datetime',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'updated_at',
            'label'      => trans('bagisto_graphql::app.admin.notification.modified'),
            'type'       => 'datetime',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('settings.push_notification.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.push_notification.edit',$row->notification_id);
                },

                'condition' => function () {
                    return true;
                },
            ]);
        }

        if (bouncer()->hasPermission('settings.push_notification.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.datagrid.delete'),
                'method' => 'POST',
                'url'    => function ($row) {
                    return route('admin.push_notification.delete',$row->notification_id);
                },

                'condition' => function () {
                    return true;
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('settings.push_notification.massdelete')) {
            $this->addMassAction([
                'title'  => trans('admin::app.datagrid.delete'),
                'url'    => route('admin.push_notification.mass-delete'),
                'method' => 'POST',
            ]);
        }

        if (bouncer()->hasPermission('settings.push_notification.massupdate')) {
            $this->addMassAction([
                'title'   => trans('admin::app.datagrid.update-status'),
                'url'     => route('admin.push_notification.mass-update'),
                'method'  => 'POST',
                'options' => [
                    [
                        'label' => trans('admin::app.datagrid.active'),
                        'value' => 1,
                    ],
                    [
                        'label' => trans('admin::app.datagrid.inactive'),
                        'value' => 0,
                    ],
                ],
            ]);
        }
    }
}
