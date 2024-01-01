<?php

namespace Webkul\GraphQLAPI\Mutations\Shop\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;
use Carbon\Carbon;
use Exception;
use Hash;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\GraphQLAPI\Validators\Customer\CustomException;

class AccountMutation extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * allowedImageMimeTypes array
     *
     */
    protected $allowedImageMimeTypes = [
        'png'   => 'image/png',
        'jpe'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'bmp'   => 'image/bmp',
        'webp'  => 'image/webp',
    ];

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository
    )
    {
        $this->guard = 'api';

        auth()->setDefaultDriver($this->guard);
        
        $this->middleware('auth:' . $this->guard);        
    }

    /**
     * Returns a current customer data.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($rootValue, array $args , GraphQLContext $context)
    {
        if (! bagisto_graphql()->validateAPIUser($this->guard)) {
            throw new CustomException(
                trans('bagisto_graphql::app.admin.response.invalid-header'),
                'Invalid request header parameter.'
            );
        }
        
        if ( bagisto_graphql()->guard($this->guard)->check() ) {
            $customer = $this->customerRepository->find(bagisto_graphql()->guard($this->guard)->user()->id);

            return [
                'status'    => $customer ? true : false,
                'customer'  => $customer,
                'message'   => trans('bagisto_graphql::app.shop.response.customer-details')
            ];
        } else {
            return [
                'status'    => false,
                'customer'  => null,
                'message'   => trans('bagisto_graphql::app.shop.customer.no-login-customer')
            ];
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
        if (! bagisto_graphql()->validateAPIUser($this->guard)) {
            throw new CustomException(
                trans('bagisto_graphql::app.admin.response.invalid-header'),
                'Invalid request header parameter.'
            );
        }

        if (! bagisto_graphql()->guard($this->guard)->check() ) {
            throw new CustomException(
                trans('bagisto_graphql::app.shop.customer.no-login-customer'),
                'No Login Customer Found.'
            );
        }

        $customer = bagisto_graphql()->guard($this->guard)->user();
        $data = $args['input'];
        $isPasswordChanged = false;
        $validator = Validator::make($data, [
            'first_name'            => 'string|required',
            'last_name'             => 'string|required',
            'gender'                => 'required',
            'date_of_birth'         => 'string|before:today',
            'email'                 => 'email|unique:customers,email,' . $customer->id,
            'oldpassword'           => 'required_with:password',
            'password'              => 'confirmed|min:6|required_with:oldpassword',
            'password_confirmation' => 'required_with:password',
            'upload_type'           => 'in:file,path,base64',
            'image.*'               => 'mimes:bmp,jpeg,jpg,png,webp',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = [];
            foreach ($validator->messages()->toArray() as $field => $message) {
                $errorMessage[] = is_array($message) ? $message[0] : $message;
            }
            
            throw new CustomException(
                implode(" ,", $errorMessage),
                'Invalid Update Customer Details.'
            );
        }

        try {
            if (
                isset ($data['date_of_birth']) 
                && $data['date_of_birth'] == ""
            ) {
                unset($data['date_of_birth']);
            }

            $data['date_of_birth'] = (isset($data['date_of_birth']) && $data['date_of_birth']) ? Carbon::createFromTimeString(str_replace('/', '-', $data['date_of_birth']) . '00:00:01')->format('Y-m-d') : '';

            if (isset ($data['oldpassword'])) {
                if ( $data['oldpassword'] != "" || $data['oldpassword'] != null) {

                    if ( Hash::check($data['oldpassword'], $customer->password) ) {
                        $isPasswordChanged = true;
                        $data['password'] = bcrypt($data['password']);
                    } else {
                        throw new CustomException(
                            trans('shop::app.customer.account.profile.unmatch'),
                            'Wrong Customer Password.'
                        );
                    }
                } else {
                    unset($data['password']);
                }
            }
    
            Event::dispatch('customer.update.before');
    
            if ($customer = $this->customerRepository->update($data, $customer->id)) {
                if ($isPasswordChanged) {
                    Event::dispatch('user.admin.update-password', $customer);
                }
    
                Event::dispatch('customer.update.after', $customer);

                if (
                    core()->getCurrentChannel()->theme != 'default' 
                    && ! empty($data['upload_type'])
                ) {
                    if ($data['upload_type'] == 'file') {
                        if (! empty($data['image']))  {
                            $customer->image = $data['image']->storePublicly('customer/' . $customer->id);
                            $customer->save();
                        } else {
                            if ($customer->image) {
                                Storage::delete($customer->image);
                            }
        
                            $customer->image = null;
                            $customer->save();
                        }
                    }

                    if (
                        in_array($data['upload_type'], ['path', 'base64']) 
                        && ! empty($data['image_url'])
                    ) {
                        $data['save_path'] = 'customer/' . $customer->id;

                        bagisto_graphql()->saveImageByURL($customer, $data, 'image_url');
                    }
                }

                return [
                    'status'    => $customer ? true : false,
                    'customer'  => $customer,
                    'message'   => trans('shop::app.customer.account.profile.edit-success')
                ];
            } else {
                throw new CustomException(
                    trans('shop::app.customer.account.profile.edit-fail'),
                    'Customer Profile Update Failed.'
                );
            }
        } catch (Exception $e) {
            throw new CustomException(
                $e->getMessage(),
                'Customer Update Failed.'
            );
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
        if (! bagisto_graphql()->validateAPIUser($this->guard)) {
            throw new CustomException(
                trans('bagisto_graphql::app.admin.response.invalid-header'),
                'Invalid request parameters.'
            );
        }

        if (! bagisto_graphql()->guard($this->guard)->check()) {
            throw new CustomException(
                trans('bagisto_graphql::app.shop.customer.no-login-customer'),
                'Customer not logged in'
            );
        }

        $data = $args['input'];
        $customer = bagisto_graphql()->guard($this->guard)->user();

        try {
            if (Hash::check($data['password'], $customer->password)) {

                if ($customer->orders->whereIn('status', ['pending', 'processing'])->first()) {
                    return [
                        'status'    => false,
                        'success'  =>  trans('shop::app.customers.account.profile.order-pending')
                    ];
                } else {
                    $this->customerRepository->delete($customer->id);

                    return [
                        'status'    => false,
                        'success'   => trans('shop::app.customers.account.profile.delete-success')
                    ];
                }
            } else {
                return [
                    'status'    => false,
                    'success'   => trans('shop::app.customers.account.profile.wrong-password')
                ];
            }
        } catch (Exception $e) {
            throw new CustomException(
                $e->getMessage(),
                'Something went wrong, try again'
            );
        }
    }
}