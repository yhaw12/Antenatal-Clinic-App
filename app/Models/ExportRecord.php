<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportRecord extends Model
{
    protected $table = 'exports';
    protected $fillable = ['exported_by','file_path','filters','encrypted'];
    protected $casts = ['filters' => 'array', 'encrypted' => 'boolean'];
}
