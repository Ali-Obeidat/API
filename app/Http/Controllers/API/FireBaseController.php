<?php
  
namespace App\Http\Controllers\API;
use App\Http\Controllers\API\Controller;

use Illuminate\Http\Request;
  
class FirebaseController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        return view('firebase');
    }
}