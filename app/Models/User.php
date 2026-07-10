<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;  // <-- IDAGDAG ITO!

class User extends Authenticatable implements \Illuminate\Contracts\Auth\MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'account_id',
        'first_name',
        'last_name',
        'course',
        'year_level',
        'course_year',
        'department_id',
        'is_active',
        'is_cleared',
        'cleared_at',
        'admin_2fa_enabled',
        'admin_2fa_code',
        'admin_2fa_expires_at',
        'birthdate',
        'email_verified_at',
        'verification_otp',
        'otp_expires_at',
        'verification_token',
        'verification_pending',
        'profile_photo',  // <-- IDAGDAG ITO!
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_otp',
        'verification_token',
        'profile_photo',  // <-- Itago ang BLOB data para safe
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_cleared' => 'boolean',
            'cleared_at' => 'datetime',
            'admin_2fa_enabled' => 'boolean',
            'admin_2fa_expires_at' => 'datetime',
            'birthdate' => 'date',
            'otp_expires_at' => 'datetime',
            'verification_pending' => 'boolean',
        ];
    }

    // ============ RELATIONSHIPS ============
    
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }
    
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }
    
    public function clearanceRequests()
    {
        return $this->hasMany(ClearanceRequest::class, 'student_id');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // ============ HELPER METHODS ============
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }
    
    public function isSupport()
    {
        return $this->role === 'support';
    }

    public function isOfficer()
    {
        return $this->role === 'officer';
    }
    
    /**
     * Get profile photo URL (for BLOB storage)
     */
   public function getProfilePhotoUrlAttribute()
{
    // Check if user has BLOB photo in database
    if ($this->profile_photo && !empty($this->profile_photo)) {
        try {
            // Try to detect mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $this->profile_photo);
            finfo_close($finfo);
            
            // If detection fails, default to jpeg
            if (!$mimeType) {
                $mimeType = 'image/jpeg';
            }
            
            // I-validate kung valid na image ang BLOB
            $testImage = @imagecreatefromstring($this->profile_photo);
            if ($testImage !== false) {
                imagedestroy($testImage);
                return 'data:' . $mimeType . ';base64,' . base64_encode($this->profile_photo);
            }
        } catch (\Exception $e) {
            \Log::warning('Invalid profile photo BLOB for user: ' . $this->id);
        }
    }

    // Default avatar using ui-avatars.com
    $name = urlencode($this->first_name ?? $this->name ?? 'User');
    return "https://ui-avatars.com/api/?background=3b82f6&color=fff&name={$name}&size=64&bold=true";
}
    
    /**
     * Check if user has custom profile photo
     */
    public function hasProfilePhoto()
    {
        return !empty($this->profile_photo);
    }
    
    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    /**
     * Check if student is fully cleared
     */
    public function isFullyCleared()
    {
        return $this->is_cleared === true;
    }
    
    // ============ EMAIL VERIFICATION METHODS ============
    
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }
    
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'verification_otp' => null,
            'otp_expires_at' => null,
            'verification_token' => null,
            'verification_pending' => false,
        ])->save();
    }
    
    public function sendEmailVerificationNotification()
    {
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        $this->forceFill([
            'verification_otp' => bcrypt($otp),
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();
        
        \Illuminate\Support\Facades\Mail::send('emails.verification-otp', [
            'otp' => $otp,
            'name' => $this->first_name,
            'email' => $this->email
        ], function ($message) {
            $message->to($this->email)
                    ->subject('Verify Your Email - Student Clearance System');
        });
        
        return $otp;
    }
    
    public function verifyOtp($otp)
    {
        if (!$this->verification_otp || !$this->otp_expires_at) {
            return false;
        }
        
        if (now()->gt($this->otp_expires_at)) {
            return false;
        }
        
        if (\Illuminate\Support\Facades\Hash::check($otp, $this->verification_otp)) {
            $this->markEmailAsVerified();
            return true;
        }
        
        return false;
    }
    
    // ============ BIRTHDATE HELPER ============
    
    public function getAgeAttribute()
    {
        if (!$this->birthdate) {
            return null;
        }
        return $this->birthdate->age;
    }
    
    public function isAtLeastAge($minAge)
    {
        if (!$this->birthdate) {
            return false;
        }
        return $this->birthdate->age >= $minAge;
    }
}