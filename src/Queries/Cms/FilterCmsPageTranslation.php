<?php

namespace Webkul\GraphQLAPI\Queries\Cms;

use Webkul\GraphQLAPI\Queries\BaseFilter;

class FilterCmsPageTranslation extends BaseFilter
{
    /**
     * filter the data .
     *
     * @param  object  $query
     * @param  array $input
     * @return \Illuminate\Http\Response
     */
    public function __invoke($query, $input)
    {
        $arguments = $this->getFilterParams($input);

        return $query->where($arguments);
    }
}  
