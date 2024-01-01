<?php

namespace Webkul\GraphQLAPI\Mutations\Catalog;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Event;
use App\Http\Controllers\Controller;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Exception;
use Webkul\Product\Helpers\ProductType;
use Webkul\Core\Rules\Slug;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Models\ProductAttributeValue;

class ProductMutation extends Controller
{
     /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * @var int
     */
    protected $id;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Product\Repositories\ProductAttributeValueRepository $productAttributeValueRepository
     * @return void
     */
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductAttributeValueRepository $productAttributeValueRepository
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

        $data = $args['input'];

        if (
            ProductType::hasVariants($data['type'])
            && (! isset($data['super_attributes'])
                || ! count($data['super_attributes']))
        ) {
            throw new Exception(trans('admin::app.catalog.products.configurable-error'));
        }

        if ( isset($data['super_attributes']) && $data['super_attributes']) {
            $super_attributes = [];
            foreach ($data['super_attributes'] as $key => $super_attribute) {
                if (isset($super_attribute['attribute_code']) && 
                    isset($super_attribute['values']) && 
                    is_array($super_attribute['values'])) {
                    $super_attributes[$super_attribute['attribute_code']] = $super_attribute['values'];
                }
            }

            $data['super_attributes'] = $super_attributes;
        }

        $validator = Validator::make($data, [
            'type'                => 'required',
            'attribute_family_id' => 'required',
            'sku'                 => ['required', 'unique:products,sku', new Slug],
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->messages());
        }

        try {
            Event::dispatch('catalog.product.create.before');

            $product = $this->productRepository->create($data);

            Event::dispatch('catalog.product.create.after', $product);

            return $product;
        } catch (Exception $e) {
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
        if (
            ! isset($args['id']) 
            || ! isset($args['input']) 
            || (isset($args['input']) 
            && ! $args['input'])
        ) {
            throw new Exception(trans('bagisto_graphql::app.admin.response.error-invalid-parameter'));
        }

        $data = $args['input'];
        $id = $args['id'];

        if (! empty($data['custom_attributes'])) {
            foreach ($data['custom_attributes'] as $customAttribute) {
                $data[$customAttribute['name']] = $customAttribute['value'];
            }

            unset($data['custom_attributes']);
        }

        $product = $this->productRepository->findOrFail($id);

        // Only in case of configurable product type
        if (isset($product->type) && $product->type == 'configurable' && 
            isset($data['variants']) && $data['variants']) {
            $data['variants'] = bagisto_graphql()->manageConfigurableRequest($data);
        }

        // Only in case of grouped product type
        if (isset($product->type) && $product->type == 'grouped' && 
            isset($data['links']) && $data['links']) {

            if (isset($data['links'])) {
                foreach ($data['links'] as $linkProduct) {
                    $productLink = $this->productRepository->findOrFail($linkProduct['associated_product_id']);
                    if ($productLink && $productLink->type != 'simple') {
                        throw new Exception("$productLink->type is not a added to Grouped product");
                    }
                }
            }

            $data['links'] = bagisto_graphql()->manageGroupedRequest($product, $data);
        }

        // Only in case of downloadable product type
        if (isset($product->type) && $product->type == 'downloadable') {
            if (isset($data['downloadable_links']) && $data['downloadable_links']) {
                $data['downloadable_links'] = bagisto_graphql()->manageDownloadableLinksRequest($product, $data);
            }

            if (isset($data['downloadable_samples']) && $data['downloadable_samples']) {
                $data['downloadable_samples'] = bagisto_graphql()->manageDownloadableSamplesRequest($product, $data);
            }
        }

        // Only in case of bundle product type
        if (isset($product->type) && $product->type == 'bundle' && 
            isset($data['bundle_options']) && $data['bundle_options']) {

            if (isset($data['bundle_options'])) {
                foreach ($data['bundle_options'] as $bundleProduct) {
                    foreach ($bundleProduct['products'] as $prod) {
                        $productLink = $this->productRepository->findOrFail($prod['product_id']);
                        if ($productLink && $productLink->type != 'simple') {
                            throw new Exception("$productLink->type is not a added to Bundle product");
                        }
                    }
                }
            }

            $data['bundle_options'] = bagisto_graphql()->manageBundleRequest($product, $data);
        }

        // Only in case of customer group price
        if (isset($data['customer_group_prices']) && $data['customer_group_prices']) {
            $data['customer_group_prices'] = bagisto_graphql()->manageCustomerGroupPrices($product, $data);
        }

        $validator = $this->validateFormData($id, $data);

        if ($validator->fails()) {
            throw new Exception($validator->messages());
        }

        $multiselectAttributeCodes = array();

        foreach ($product->attribute_family->attribute_groups as $attributeGroup) {
            $customAttributes = $product->getEditableAttributes($attributeGroup);
            if (count($customAttributes)) {
                foreach ($customAttributes as $attribute) {
                    if ($attribute->type == 'multiselect') {
                        array_push($multiselectAttributeCodes, $attribute->code);
                    }
                }
            }
        }

        if (count($multiselectAttributeCodes)) {
            foreach ($multiselectAttributeCodes as $multiselectAttributeCode) {
                if (! isset($data[$multiselectAttributeCode])) {
                    $data[$multiselectAttributeCode] = array();
                }
            }
        }

        $imageUrls = $videoUrls = [];

        if (isset($data['images'])) {
            $imageUrls = $data['images'];
            unset($data['images']);
        }

        if (isset($data['videos'])) {
            $videoUrls = $data['videos'];
            unset($data['videos']);
        }

        $inventories = [];

        if (isset($data['inventories'])) {
            foreach ($data['inventories'] as $key => $inventory) {
                if (isset($inventory['inventory_source_id']) && isset($inventory['qty'])) {
                    $inventories[$inventory['inventory_source_id']] = $inventory['qty'];
                }
            }

            $data['inventories'] = $inventories;
        }

        try {
            Event::dispatch('catalog.product.update.before', $id);

            $product = $this->productRepository->update($data, $id);

            Event::dispatch('catalog.product.update.after', $product);

            if (isset($product->id)) {
                $uploadParams = [
                    'resource'    => $product,
                    'data'        => $imageUrls,
                    'path'        => 'product/',
                    'data_type'   => 'images',
                    'upload_type' => ! isset($args['upload_type']) ? 'path' : $args['upload_type']
                ];
                
                bagisto_graphql()->uploadProductImages($uploadParams);

                bagisto_graphql()->uploadProductImages(array_merge($uploadParams, ['data' => $videoUrls, 'data_type' => 'videos']));

                return $product;
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function validateFormData($id, $data)
    {
        $this->id = $id;
        $product = $this->productRepository->findOrFail($this->id);
        $validateRules =
            array_merge($product->getTypeInstance()->getTypeValidationRules(), [
                'sku'                => ['required', 'unique:products,sku,' . $this->id, new \Webkul\Core\Rules\Slug],
                // 'images.*'           => 'nullable|mimes:jpeg,jpg,bmp,png',
                'special_price_from' => 'nullable|date',
                'special_price_to'   => 'nullable|date|after_or_equal:special_price_from',
                'special_price'      => ['nullable', new \Webkul\Core\Rules\Decimal, 'lt:price'],
            ]);

        foreach ($product->getEditableAttributes() as $attribute) {
            if ($attribute->code == 'sku' || $attribute->type == 'boolean') {
                continue;
            }

            $validations = [];

            if (! isset($validateRules[$attribute->code])) {
                array_push($validations, $attribute->is_required ? 'required' : 'nullable');
            } else {
                $validations = $validateRules[$attribute->code];
            }

            if ($attribute->type == 'text' && $attribute->validation) {
                array_push(
                    $validations,
                    $attribute->validation == 'decimal'
                        ? new \Webkul\Core\Rules\Decimal
                        : $attribute->validation
                );
            }

            if ($attribute->type == 'price') {
                array_push($validations, new \Webkul\Core\Rules\Decimal);
            }

            if ($attribute->is_unique) {
                array_push($validations, function ($field, $value, $fail) use ($attribute) {
                    $column = ProductAttributeValue::$attributeTypeFields[$attribute->type];

                    if (!$this->productAttributeValueRepository->isValueUnique($this->id, $attribute->id, $column, request($attribute->code))) {
                        $fail('The :attribute has already been taken.');
                    }
                });
            }

            $validateRules[$attribute->code] = $validations;
        }

        return Validator::make($data, $validateRules);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($rootValue, array $args, GraphQLContext $context)
    {
        if (! isset($args['id']) || 
            ( isset($args['id']) && ! $args['id'])) {
            throw new Exception(trans('bagisto_graphql::app.admin.response.error-invalid-parameter'));
        }

        $id = $args['id'];
        $product = $this->productRepository->findOrFail($id);

        try {
            $this->productRepository->delete($id);

            return ['success' => trans('admin::app.response.delete-success', ['name' => 'Product'])];
        } catch (\Exception $e) {
            throw new Exception(trans('admin::app.response.delete-failed', ['name' => 'Product']));
        }
    }
}
