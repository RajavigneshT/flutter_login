<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreateBusRouteModel extends Model
{

    protected $fillable = [
        'N_BusRoute_From',
        'N_BusRoute_To',
        'N_BusRouteFare_Amount',
    ];
}
