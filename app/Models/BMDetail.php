<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BMDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_imdt';
    public $timestamps = false;
    protected $guarded = [];
}
