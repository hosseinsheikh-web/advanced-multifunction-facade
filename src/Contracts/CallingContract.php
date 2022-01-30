<?php

namespace Palpalasi\AdvancedMultifunctionFacade\Contracts;

interface CallingContract
{
    // For before calling the facade
    // @return array that key is facade method and value is Listener class
    public function calling();

    // For after calling the facade
    public function called();
}
