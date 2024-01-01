<?php

namespace Webkul\GraphQLAPI\Mutations\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Exception;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Shop\Repositories\ThemeCustomizationRepository;

class ThemeMutation extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\User\Repositories\AdminRepository  $adminRepository
     * @param  \Webkul\User\Repositories\RoleRepository  $roleRepository
     * @return void
     */
    public function __construct(
        protected ThemeCustomizationRepository $themeCustomizationRepository,
        protected ChannelRepository $channelRepository,
    ) {
        $this->guard = 'admin-api';

        auth()->setDefaultDriver($this->guard);
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

        $data = $args['input'];

        if (request()->has('id')) {
            $theme = $this->themeCustomizationRepository->find(request()->input('id'));

            return $this->themeCustomizationRepository->uploadImage(request()->all(), $theme);
        }

        $validator = Validator::make($data, [
            'name'       => 'required',
            'sort_order' => 'required|numeric',
            'type'       => 'in:product_carousel,category_carousel,static_content,image_carousel,footer_links',
            'channel_id' => 'required|in:' . implode(',', (core()->getAllChannels()->pluck("id")->toArray())),
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->messages());
        }

        Event::dispatch('theme_customization.create.before');

        $theme = $this->themeCustomizationRepository->create([
            'name'       => $data['name'],
            'sort_order' => $data['sort_order'],
            'type'       => $data['type'],
            'status'     => $data['status'],
            'channel_id' => $data['channel_id']
        ]);

        return $theme;
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

        $data = $args['input'];
        $id = $args['id'];
        $validator = Validator::make($data, [
            'name'       => 'required',
            'sort_order' => 'required|numeric',
            'type'       => 'in:product_carousel,category_carousel,static_content,image_carousel,footer_links',
            'channel_id' => 'required|in:' . implode(',', (core()->getAllChannels()->pluck("id")->toArray())),
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->messages());
        }

        $locale = core()->getRequestedLocaleCode();
        $themeData = $this->themeCustomizationRepository->find($args['id']);
        $data['type'] = $themeData->type;

        if ($data['type'] == 'static_content') {
            $data[$locale]['options']['html'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $data[$locale]['options']['html']);
            $data[$locale]['options']['css'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $data[$locale]['options']['css']);
        }

        $data['status'] = $args['input']['status'] == 'true';

        if ($data['type'] == 'image_carousel') {
            unset($data['options']);
        }

        Event::dispatch('theme_customization.update.before', $id);

        $theme = $this->themeCustomizationRepository->update($data, $id);

        if ($data['type'] == 'image_carousel') {
            $this->themeCustomizationRepository->uploadImage(
                request()->all('options'),
                $theme,
                request()->input('deleted_sliders', [])
            );
        }
        Event::dispatch('theme_customization.update.after', $theme);

        return $theme;
    }

    public function delete($rootValue, array $args, GraphQLContext $context)
    {
        if (! isset($args['id']) || 
            (isset($args['id']) && ! $args['id'])) {
            throw new Exception(trans('bagisto_graphql::app.admin.response.error-invalid-parameter'));
        }

        $id = $args['id'];

        Event::dispatch('theme_customization.delete.before', $id);
        
        $theme = $this->themeCustomizationRepository->find($id);
        $theme?->delete();

        Storage::deleteDirectory('theme/' . $theme->id);

        Event::dispatch('theme_customization.delete.after', $id);

        return ['success' => trans('admin::app.settings.themes.delete-success')];
    }
}
