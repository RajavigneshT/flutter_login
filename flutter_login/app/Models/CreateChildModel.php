<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreateChildModel extends Model
{
    protected $fillable = [
        'ChildName',
        'SchoolName',
        'BusRoute',
        'ParentName',
        'Address',
        'ContactNo',
        'user_id',
        'Gender'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
