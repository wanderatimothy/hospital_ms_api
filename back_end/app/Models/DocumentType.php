<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use HasFactory , SoftDeletes;

        protected $fillable = [
            'name', 'description', 'branch_id', 'created_by', 'last_modified_by'
        ];

      // Define relationships
      public function createdBy()
      {
          return $this->belongsTo(User::class, 'created_by');
      }
  
      public function lastModifiedBy()
      {
          return $this->belongsTo(User::class, 'last_modified_by');
      }

      public function branch(){

        return $this->belongsTo(Branch::class, 'branch_id')->select('name');
    }
}
