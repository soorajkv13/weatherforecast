<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherController extends Controller
{
  public function getWeather()
  {
      return view('weather');
  }
}
