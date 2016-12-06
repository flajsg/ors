<?php
namespace Ors\Orsapi\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class TypDhotelApi extends BaseFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'orsapi.typdhotel'; }


}
