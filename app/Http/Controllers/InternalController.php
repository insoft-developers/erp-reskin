<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InternalController extends Controller
{
    public function generateWaCode()
    {
        return $this->generateRandomString(6, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }
}
