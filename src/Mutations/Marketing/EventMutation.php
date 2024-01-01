<?php

namespace Webkul\GraphQLAPI\Mutations\Marketing;

use Exception;
use Illuminate\Support\Facades\Validator;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Marketing\Repositories\EventRepository;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class EventMutation extends Controller
{
    /**
     * Initialize _config, a default request parameter with route
     *
     * @param array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Marketing\Repositories\EventRepository $eventRepository
     * @return void
     */
    public function __construct(
        protected EventRepository $eventRepository
    ) {
        $this->guard = 'admin-api';

        auth()->setDefaultDriver($this->guard);

        $this->_config = request('_config');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($rootValue, array $args, GraphQLContext $context)
    {
        if (! isset($args['input']) || 
            (isset($args['input']) && ! $args['input'])) {
            throw new Exception(trans('bagisto_graphql::app.admin.response.error-invalid-parameter'));
        }

        $params = $args['input'];

        $validator = Validator::make($params, [
            'name'          => 'required',
            'description'   => 'required',
            'date'         => 'required',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->messages());
        }

        try {

            $params['date'] = \Carbon\Carbon::parse($params['date'])->format('Y-m-d');

            $event = $this->eventRepository->create($params);

            return $event;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($rootValue, array $args, GraphQLContext $context)
    {
        if (!isset($args['id']) || 
            ! isset($args['input']) || 
            (isset($args['input']) && ! $args['input'])) {
            throw new Exception(trans('bagisto_graphql::app.admin.response.error-invalid-parameter'));
        }

        $params = $args['input'];
        $id = $args['id'];

        $validator = Validator::make($params, [
            'name'           => 'required',
            'description'    => 'required',
            'date'         => 'required',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->messages());
        }

        try {

            $event = $this->eventRepository->findOrFail($id);

            $params['date'] = \Carbon\Carbon::parse($params['date'])->format('Y-m-d');

            $event = $this->eventRepository->update($params, $id);

            return $event;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($rootValue, array $args, GraphQLContext $context)
    {
        if (!isset($args['id']) || 
            (isset($args['id']) && ! $args['id'])) {
            throw new Exception(trans('bagisto_graphql::app.admin.response.error-invalid-parameter'));
        }

        $id = $args['id'];

        $event = $this->eventRepository->find($id);

        try {

            if ($event != Null) {

                $event->delete();

                return [
                    'status' => true,
                    'message' => trans('admin::app.marketing.communications.events.delete-success', ['name' => 'Event'])
                ];
            } else {

                return [
                    'status' => false,
                    'message' => trans('admin::app.response.delete-failed', ['name' => 'Event'])
                ];
            }
        } catch (Exception $e) {

            throw new Exception($e->getMessage());
        }
    }
}
