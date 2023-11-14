<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_no',
        'first_name',
        'last_name',
        'image',
        'other_names',
        'title',
        'gender',
        'email',
        'phone_number',
        'blood_group',
        'last_visit_date',
        'address',
        'emergency_contact_first_name',
        'emergency_contact_last_name',
        'emergency_contact_phone_number',
        'emergency_contact_relationship',
        'is_insured',
        'health_insuarance_coverage',
        'has_extra_fields',
        'created_by',
        'last_modified_by',
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
