<?php

namespace Palpalasi\AdvancedMultifunctionFacade\Contracts;

interface CheckAuthorizationContract
{
    public static function authorize();

    // false is off
    // true is on
    public static function authorizeAndCalling();
}
