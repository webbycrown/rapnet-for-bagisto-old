@push('scripts')

    @inject('coreConfigRepository', 'Webkul\Core\Repositories\CoreConfigRepository')

                            @php
                            $nameKey = $item['key'] . '.' . $field['name'];

                            $name = $coreConfigRepository->getNameField($nameKey);

                            $value = $coreConfigRepository->getValueByRepository($field);

                            $validations = $coreConfigRepository->getValidations($field);

                            $isRequired = Str::contains($validations, 'required') ? 'required' : '';

                            $channelLocaleInfo = $coreConfigRepository->getChannelLocaleInfo($field, $currentChannel->code, $currentLocale->code);
                            @endphp


                                <div class="flex justify-between">
                                    <label class="flex gap-1 items-center mb-1.5 text-xs text-gray-800 dark:text-white font-medium" for="apis[repnet][template_setting][reorder_fields]">{!! __($field['title']) . ( __($field['title']) ? '<span class="'.$isRequired.'"></span>' : '') !!}</label>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="col-md-6">
                                            <div class="form-group">

                                <?php 

                                function custom_esc_attr($input) { 
                                    /*$input =  htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
                                    $displayed_entity = htmlentities($input, ENT_QUOTES, 'UTF-8');*/
                                    return $input;
                                }

                                $reorder_fields = 'checkbox,action,company,country,stock_num,shape,size,color,fancy_color_intensity,fancy_color_overtone,fancy_color_dominant_color,clarity,cut,polish,symmetry,fluor_intensity,depth_percent,table_percent,measurement,lab,price_per_carat,discount,total_sales_price';

                                $api_fields = array(
                                    'diamond_id'          =>  'Id',
                                    'shape'             =>  'Shape',
                                    'size'              =>  'Size',
                                    'color'             =>  'Color',
                                    'fancy_color_dominant_color'  =>  'Fancy Color Dominant Color',
                                    'fancy_color_secondary_color'   =>  'Fancy Color Secondary Color',
                                    'fancy_color_overtone'      =>  'Fancy Color Overtone',
                                    'fancy_color_intensity'     =>  'Fancy Color Intensity',
                                    'clarity'             =>  'Clarity',
                                    'cut'               =>  'Cut',
                                    'symmetry'            =>  'Symmetry',
                                    'polish'            =>  'Polish',
                                    'depth_percent'         =>  'Depth Percent',
                                    'table_percent'         =>  'Table Percent',
                                    'meas_length'           =>  'Meas Length',
                                    'meas_width'          =>  'Meas Width',
                                    'meas_depth'          =>  'Meas Depth',
                                    'measurement'           =>  'Measurement',
                                    'girdle_min'          =>  'Girdle Min',
                                    'girdle_max'          =>  'Girdle Max',
                                    'girdle_condition'        =>  'Girdle Condition',
                                    'culet_size'          =>  'Culet Size',
                                    'culet_condition'         =>  'Culet Condition',
                                    'fluor_color'           =>  'Fluor Color',
                                    'fluor_intensity'         =>  'Fluor Intensity',
                                    'has_cert_file'         =>  'Has Cert File',
                                    'lab'               =>  'Lab',
                                    'currency_code'         =>  'Currency Code',
                                    'currency_symbol'       =>  'Currency Symbol',
                                    'total_sales_price'       =>  'Price',
                                    'total_sales_price_in_currency' =>  'Total Sales Price In Currency',
                                    'cert_num'            =>  'CER. NO',
                                    'stock_num'           =>  'Stock Num',
                                    'has_image_file'        =>  'Has Image File',
                                    'image_file_url'        =>  'Image',
                                    'video_url'           =>  'Video Url',
                                    'has_sarineloupe'         =>  'Has Sarineloupe',
                                    'eye_clean'           =>  'Eye Clean',

                                    /* Single diamond attributes start */
                                    'account_id'          =>  'Account Id',
                                    'company'             =>  'Company',
                                    'name'              =>  'Name',
                                    'email'             =>  'Email',
                                    'phone'             =>  'Phone',
                                    'country'             =>  'Country',
                                    'state'             =>  'State',
                                    'city'              =>  'City',
                                    /* Single diamond attributes end */

                                    'discount'            =>  'Discount',
                                    'price_per_carat'       =>  'Price/CT',
                                    'action'            =>  'Action',
                                    'checkbox'            =>  'Checkbox',
                                );

                                $reorder_fields_arr = [];
                                $default_rap_field_arr = [];
                                if( $reorder_fields ){
                                    $default_rap_field_arr = explode( ',', $reorder_fields );  
                                }


                                $field = array(
                                    'type' => 'sortable',
                                    'title' => 'Reorder Fields',
                                    'desc' => 'Reorder field for <strong>RapNet Shop</strong> list layout.',
                                    'id' => 'sortable_value',
                                    'field_desc' => 'Reorder field for <b>RapNet Shop</b> list layout.',
                                    'connected_class' => 'connectedClass',
                                    'sortable_type' => 'connected_list', 
                                    'fields_title' => 'Inactive',
                                    'default_title' => 'Active',
                                    'sortable_list' => array(
                                        'fields' => $api_fields,
                                        'default' => array_intersect_key(array_fill_keys($default_rap_field_arr,"rapnet"), $api_fields )
                                    )
                                );

                                $values_fields_arr = $val = $all_arr = $default_val_arr = $default_values_fields_arr = $default_arr = [];      
                                $flag = 0;
                                $labels = '';
                                $default = ( isset( $field['default'] ) && ( !empty( $field['default'] ) ) ? $field['default'] : '' );
                                $all_fields_title = ( isset( $field['fields_title'] ) && !empty( $field['fields_title'] ) ) ? $field['fields_title'] : '';
                                $default_fields_title = ( isset( $field['default_title'] ) && !empty( $field['default_title'] ) ) ? $field['default_title'] : '';
                                $all_fields_id = ( isset( $field['id'] ) && !empty( $field['id'] ) ) ? $field['id'].'_all_fields' : '';
                                $name = ( isset( $field['id'] ) && !empty( $field['id'] ) ) ? $field['id'] : '';
                                $class = ( isset( $field['class'] ) && ( !empty( $field['class'] ) ) ? $field['class'] : '' );
                                $default_fields_id = ( isset( $field['id'] ) && !empty( $field['id'] ) ) ? $field['id'].'_default_fields' : '';
                                $sortable_type = ( isset( $field['sortable_type'] ) && !empty( $field['sortable_type'] ) ) ? $field['sortable_type'] : '';
                                $connected_class = ( isset( $field['connected_class'] ) && !empty( $field['connected_class'] ) ) ? $field['connected_class'] : '';
                                $option_value = core()->getConfigData($nameKey, $currentChannel->code, $currentLocale->code) ?? '';
                                $IsLabel_overwrited = is_string($option_value) && is_array(json_decode($option_value, true));


                                if( $IsLabel_overwrited ){
                                    $values = (array)json_decode( $option_value );
                                    $labels = (array)$values['values'];
                                }         
                                echo '<div class="'.custom_esc_attr( 'sortable_wrapper '.$class ).'">';

                                // --------------| Active Sortable |-------------- //

                                if( isset( $field['sortable_list']['default'] ) && !empty( $field['sortable_list']['default'] ) ){

                                    $flag = 1;

                                    echo '<div class="sortable">';
                                    echo '<p>Active</p>';
                                    echo '<ul id="'.custom_esc_attr( $default_fields_id ).'" class="'.custom_esc_attr( 'ui-sortable-connected ui-sortable-active '.$connected_class ).'">';

                                    foreach ( $field['sortable_list']['default'] as $default_key => $default_value ) {
                                        $default_val_arr[] = $default_key;
                                    }

                                    $value = ( $option_value != '' ) ? (array)json_decode( $option_value ) : $default_val_arr;

                                    $value_slug = isset($value['slug']) ? $value['slug'] : array();

                                    $fields_arr = ( $option_value != '' ) ? $value_slug : $value;       

                                    if( $fields_arr ){
                                        $fields = array_unique( array_filter( $fields_arr ) );

                                        foreach ( $fields as $field_key ) {
                                            $default_arr[] = $field_key;
                                            $values_fields_arr[$field_key] = ( ( $IsLabel_overwrited ) ? $labels[$field_key] : $field['sortable_list']['fields'][$field_key] );
                                            echo '<li class="ui-state-default ui-sortable-handle" id="'.custom_esc_attr( $field_key ).'">';
                                            echo '<input name="name['.$field_key.']" class="hidden ui-sortable-name" value="'.custom_esc_attr( ( ( $IsLabel_overwrited ) ? $labels[$field_key] : $field['sortable_list']['fields'][$field_key] ) ).'"><span class="label">'.html_entity_decode( ( $IsLabel_overwrited ) ? $labels[$field_key] : $field['sortable_list']['fields'][$field_key] ).'</span>';
                                            echo '<a href="javascript:;" class="edit-label icon-edit"></a>';
                                            echo '</li>';
                                        }
                                    }

                                    echo '</ul>';
                                    echo '</div>';

                                }

                                // --------------| Active Sortable |-------------- //

                                // --------------| Deactive Sortable |-------------- //

                                if( isset( $field['sortable_list']['fields'] ) && !empty( $field['sortable_list']['fields'] ) ){
                                    echo '<div class="sortable">';
                                    echo '<p>Inactive</p>';
                                    echo '<ul id="'.custom_esc_attr( $all_fields_id ).'" class="'.custom_esc_attr( ( ( $flag ) ? 'ui-sortable-connected': '' ).' '.$connected_class ).'">';

                                    foreach ($field['sortable_list']['fields'] as $default_key => $default_value) {
                                        $all_val_arr[] = $default_key;
                                    }

                                    $value = ( $option_value != '' ) ? (array)json_decode( $option_value ) : $all_val_arr;      

                                    $value_slug = isset($value['slug']) ? $value['slug'] : array();

                                    $fields_arr = ( $option_value != '' ) ? $value_slug : $value;     

                                    if( $sortable_type == 'connected_list' ){

                                        $value_data = isset($value['values']) ? $value['values'] : array();           
                                        $fields_arr = ( $option_value != '' ) ? array_keys( (array)$value_data ) : $value;            
                                    }else{
                                        $field_unique = array_diff( $all_val_arr, $fields_arr );            
                                        $fields_arr = array_merge( $fields_arr, $field_unique );
                                    }

                                    $fields = $fields_arr;

                                    if( $fields ){
                                        $fields = array_unique( array_filter( $fields ) );
                                        foreach ( $fields as $field_key ) {
                                            if( !in_array( $field_key, $default_arr ) ){
                                                $all_arr[] = $field_key;
                                                echo '<li class="ui-state-default ui-sortable-handle" id="'.custom_esc_attr( $field_key ).'">';
                                                $default_values_fields_arr[$field_key] = ( ( $IsLabel_overwrited ) ? $labels[$field_key] : $field['sortable_list']['fields'][$field_key] );
                                                echo '<input name="name['.$field_key.']" class="hidden ui-sortable-name" value="'.custom_esc_attr( ( ( $IsLabel_overwrited ) ? $labels[$field_key] : $field['sortable_list']['fields'][$field_key] ) ).'"><span class="label">'.html_entity_decode( ( $IsLabel_overwrited ) ? $labels[$field_key] : $field['sortable_list']['fields'][$field_key] ).'</span>';
                                                echo '<a href="javascript:;" class="edit-label icon-edit"></a>';
                                                echo '</li>';
                                            }
                                        }
                                    }

                                    echo '</ul>';
                                    echo '</div>';

                                }

                                if( $sortable_type == 'connected_list' ){

                                    $all_value_arr = array_merge( $values_fields_arr, $default_values_fields_arr );

                                    $val['slug'] = $default_arr;
                                    $val['values'] = $all_value_arr;
                                    $val = json_encode( $val );
                                    $target = '#'.$default_fields_id;
                                    $reference = '#'.$all_fields_id.', #'.$default_fields_id;

                                    $input_target = "input[name='".custom_esc_attr( $name )."']";
                                    $on_update = 'var order = jQuery("'.custom_esc_attr( $target ).' .ui-state-default").map( function() {return this.id;}).get();
                                    var input_value = jQuery("'.custom_esc_attr( $input_target ).'").val();
                                    input_value = JSON.parse(input_value);
                                    var order_obj = { slug : order }
                                    jQuery.extend( input_value, order_obj );
                                    input_value = JSON.stringify(input_value);
                                    jQuery("'.custom_esc_attr( $input_target ).'").val(input_value);';
                                    $sortable = '.sortable({connectWith: ".'.custom_esc_attr( $connected_class ).'", update: function(event, ui) {'.$on_update.'}}).disableSelection();';
                                }else{

                                    $val['slug'] = $all_arr;
                                    $val['values'] = $default_values_fields_arr;
                                    $val = json_encode( $val );
                                    $target = '#'.$all_fields_id;
                                    $reference = '#'.$all_fields_id;

                                    $input_target = "input[name='".custom_esc_attr( $name )."']";
                                    $on_update = 'var order = jQuery("'.custom_esc_attr( $target ).' .ui-state-default").map( function() {return this.id;}).get();
                                    var input_value = jQuery("'.custom_esc_attr( $input_target ).'").val();
                                    input_value = JSON.parse(input_value);
                                    var order_obj = { slug : order }
                                    jQuery.extend(input_value , order_obj );
                                    input_value = JSON.stringify(input_value);
                                    jQuery("'.custom_esc_attr( $input_target ).'").val(input_value);';
                                    $sortable = '.sortable({update: function(event, ui) {'.$on_update.'} });';
                                }

                                // echo "<input type='hidden' class='sortable-value' name='".custom_esc_attr( $name )."' value='".custom_esc_attr( $val )."'>";
                                echo "<input type='hidden' class='sortable-value' name='apis[repnet][template_setting][reorder_fields]' value='".custom_esc_attr( $val )."'>";

                                // echo '<input type="hidden" name="keys[]" value="'.json_encode($item).'">';

                                // --------------| Deactive Sortable |-------------- //

                                echo '</div>';

                                // --------------| Script |-------------- //
                                ?>    

                                <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
                                {{-- <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script> --}}
                                <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
                                <script>
                                    jQuery(document).ready(function($){
                                        setTimeout(function(){
                                            <?php echo 'jQuery( "'. $reference .'" )'.$sortable; ?>    

                                            // Set styles for .sortable_wrapper
                                            $('.sortable_wrapper').css({
                                                'display': 'flex',
                                                'flex-wrap': 'wrap'
                                            });

                                            // Set styles for .sortable
                                            $('.sortable_wrapper .sortable').css({
                                                'max-width': '310px',
                                                'width': '100%',
                                                'margin-right': '30px'
                                            });

                                            // Set styles for .hidden class
                                            $('.sortable_wrapper .sortable .hidden').css({
                                                'display': 'none'
                                            });

                                            // Set styles for .ui-sortable
                                            $('.sortable_wrapper .sortable .ui-sortable').css({
                                                'background': '#f7f7f7',
                                                'border-bottom': '1px solid #f0f0f1',
                                                'margin-bottom': '0',
                                                'margin-top': '0',
                                                'height': '100%'
                                            });

                                            // Set styles for .ui-sortable li
                                            $('.sortable_wrapper .sortable .ui-sortable li').css({
                                                'margin': '0',
                                                'border': '1px solid #f0f0f1',
                                                'padding': '10px 20px',
                                                'background': '#fff',
                                                'border-bottom': '0',
                                                'display': 'flex',
                                                'align-items': 'center',
                                                'color': '#001e1d',
                                                'font-size': '16px',
                                                'cursor': 'pointer',
                                                'justify-content': 'space-between'
                                            });

                                            // Set styles for .ui-sortable-name class
                                            $('.sortable_wrapper .sortable .ui-sortable li .ui-sortable-name').css({
                                                'padding': '4px 5px !important'
                                            });

                                            // Set styles for .label class
                                            $('.sortable_wrapper .sortable .ui-sortable li .label').css({
                                                'padding': '2.609px 0',
                                                'word-break': 'break-word'
                                            });

                                            // Set styles for both .ui-sortable-name and .label classes
                                            $('.sortable_wrapper .sortable .ui-sortable li .ui-sortable-name, .sortable_wrapper .sortable .ui-sortable li .label').css({
                                                'width': 'calc(100% - 60px) !important'
                                            });

                                            // Set styles for a:link within .ui-sortable li
                                            $('.sortable_wrapper .sortable .ui-sortable li a:link').css({
                                                'margin-left': '8px'
                                            });

                                            // Set styles for :before pseudo-element
                                            $('.sortable_wrapper .sortable .ui-sortable li:before').css({
                                                'content': "''",
                                                'background': 'url(\'../images/menu.svg\') no-repeat center center',
                                                'width': '18px',
                                                'height': '18px',
                                                'background-size': '100%',
                                                'margin-right': '17px'
                                            });

                                        },100);


                                    



                                    });

                                </script>

                                </div>
                                </div>

                                </div>

                                </div>

                                <script type="text/javascript">
                                    (function($) {
                                        $(document).ready(function () {

                                            jQuery(document).on('click', '.sortable .ui-sortable-handle .edit-label', function(){
                                                jQuery(this).parent().find('.ui-sortable-name').toggleClass('hidden').focus();
                                                jQuery(this).parent().find('.label').toggleClass('hidden');
                                                if ( jQuery(this).parent().find('.ui-sortable-name').hasClass('hidden') ) {
                                                    jQuery(this).parent().find('.ui-sortable-name').hide();
                                                } else {
                                                    jQuery(this).parent().find('.ui-sortable-name').show();
                                                }
                                                jQuery(this).parent().find('.ui-sortable-name').css({'border': '1px solid black', 'padding': '5px'}).focus();
                                            });

                                            jQuery(document).on('change', '.sortable .ui-sortable-handle .ui-sortable-name', function(){
                                                var label = jQuery(this).val();
                                                jQuery(this).parent().find('.label').text(label);
                                                var obj = {};
                                                var slug_obj = [];
                                                var main_obj = {};
                                                var uiRef = ( jQuery(this).parents('.ui-sortable').hasClass('ui-sortable-connected') ) ? '.ui-sortable.ui-sortable-active li' : '.ui-sortable li';

                                                jQuery(this).parents('.sortable_wrapper').find(uiRef).each(function( index ) {
                                                    var slug = jQuery(this).attr('id');
                                                    slug_obj.push(slug);
                                                });

                                                jQuery(this).parents('.sortable_wrapper').find('.ui-sortable li').each(function( index ) {

                                                    var slug = jQuery(this).attr('id');
                                                    var label = jQuery(this).find('.ui-sortable-name').val();

                                                    obj[slug] = label;
                                                });
                                                main_obj['slug'] = slug_obj;
                                                main_obj['values'] = obj;       
                                                main_obj = JSON.stringify(main_obj);        

                                                jQuery(this).parents('.sortable_wrapper').find('.sortable-value').val(main_obj);
                                            });

                                            jQuery(document).on('sortupdate', '.sortable_wrapper  .ui-sortable', function(){        
                                                var obj = {};
                                                var slug_obj = [];
                                                var main_obj = {};
                                                var uiRef = ( jQuery(this).hasClass('ui-sortable-connected') ) ? '.ui-sortable.ui-sortable-active li' : '.ui-sortable li';
                                                jQuery(this).parents('.sortable_wrapper').find(uiRef).each(function( index ) {
                                                    var slug = jQuery(this).attr('id');
                                                    slug_obj.push(slug);
                                                });
                                                jQuery(this).parents('.sortable_wrapper').find('.ui-sortable li').each(function( index ) {
                                                    var slug = jQuery(this).attr('id');
                                                    var label = jQuery(this).find('.ui-sortable-name').val();
                                                    obj[slug] = label;
                                                });
                                                main_obj['slug'] = slug_obj;
                                                main_obj['values'] = obj;       
                                                main_obj = JSON.stringify(main_obj);        

                                                jQuery(this).parents('.sortable_wrapper').find('.sortable-value').val(main_obj);
                                            });

                                            jQuery(document).mouseup(function(e){
                                                var container = jQuery(".ui-sortable-handle .label.hidden").parents('.ui-sortable-handle');

                                                if (!container.is(e.target) && container.has(e.target).length === 0){
                                                    container.find('.label.hidden').removeClass('hidden');
                                                    container.find('.ui-sortable-name').addClass('hidden');
                                                }
                                            });
                                /* sortable js */


                                //copy
                                            $(document).on('click','.input-field .copy_secretkey',function(){

                                                var copy_input = jQuery(this);
                                                var copy_text = jQuery(this).parents('.input-field').find('.copy_text').val();
                                                copyToClipboard(copy_input, copy_text);
                                            });

                                            function copyToClipboard(copy_input, textToCopy) {
                                                navigator.clipboard.writeText(textToCopy)
                                                .then(() => {
                                                    copy_input.addClass('btn-success');
                                                    copy_input.removeClass('btn-clean');
                                                    copy_input.find('i.la.la-copy').hide();
                                                    copy_input.find('i.la.la-check').show();
                                                    setTimeout(function(){
                                                        copy_input.removeClass('btn-success');
                                                        copy_input.addClass('btn-clean');
                                                        copy_input.find('i.la.la-copy').show();
                                                        copy_input.find('i.la.la-check').hide();
                                                    },2000);
                                                })
                                                .catch((err) => {
                                                    console.error('Unable to copy text to clipboard', err);
                                                });
                                            }

                                        });

                                })(jQuery);
                                function previewThumnailImage(event) {
                                    var reader = new FileReader();
                                    reader.onload = function(){
                                        var output = document.getElementById('preview-img');
                                        output.src = reader.result;
                                    }
                                    reader.readAsDataURL(event.target.files[0]);
                                };
                                </script>

@endpush