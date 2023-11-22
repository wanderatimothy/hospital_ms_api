<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'visit_date',
        'visit_outcome',
        'has_appointment',
        'appointment_id',
        'patient_id',
        'arrival_time',
        'departure_time',
        'phone_number',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'is_insured',
        'health_insurance_coverage',
        'insurance_provider_id',
        'created_by',
        'last_modified_by'
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
        'date_of_birth' => 'date',
    ];

    protected $dates = ['deleted_at'];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user who created the entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastModifiedBy()
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

}
