<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name', 'store_path', 'entity','mime_type' , 'size' , 'shared_with', 'entity_id', 'document_type_id', 'is_encoded', 'content', 'created_by', 'last_modified_by'
    ];


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastModifiedBy()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }
}
