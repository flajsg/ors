<?php
namespace ORS\Api\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class OrsApi extends BaseFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'orsapi'; }


}
