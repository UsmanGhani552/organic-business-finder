<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Mail\SendOtpMail;
use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ImageUploadTrait;
    // public static $user = new self;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'social_id',
        'provider',
        'password',
        'type',
        'image',
        'otp',
        'certificate',
        'is_free_trial',
        'free_trial_started_at',
        'free_trial_ends_at',
        'subscription_status',
        'otp_expires_at'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function registerUser($data)
    {
        // dd($data);
        $data['password'] = Hash::make($data['password']);
        $data['certificate'] = (new self)->uploadImage(request(), 'certificate', 'images/user/certificates');
        $user = self::create($data);
        $user->saveFcmToken($data['fcm_token']);
        return $user;
    }

    public function saveFcmToken($fcmToken)
    {
        DeviceToken::updateOrCreate(
            ['fcm_token' => trim($fcmToken)],
            ['user_id' => $this->id]
        );
    }

    public static function editProfile($user, array $data)
    {
        $data['image'] = (new self)->uploadImage(request(), 'image', 'images/user', "images/user/{$user->image}", $user->image);
        $data['certificate'] = (new self)->uploadImage(request(), 'certificate', 'images/user/certificates', "images/user/certificates/{$user->certificate}", $user->certificate);
        $user->update($data);
        return $user->fresh();
    }

    public static function changePassword($user, array $data): void
    {
        $data['password'] = Hash::make($data['password']);
        $user->update(['password' => $data['password']]);
    }

    public static function editImage($user): void
    {
        $uploadedImagePath = (new self)->uploadImage(request(), 'image', 'images/user', "images/user/{$user->image}", $user->image);
        $user->update(['image' => $uploadedImagePath]);
    }

    public static function saveOtp($user, $otp): void
    {
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);
    }

    // for admin
    public static function storeUser(array $data): void {
        // dd($data);
        $data['password'] = Hash::make($data['password']);
        $data['image'] = (new self)->uploadImage(request(), 'image', 'images/user');
        self::create($data);
    }
    public static function updateUser(array $data, $user): void {
        $data['password'] = $data['password'] ? Hash::make($data['password']) : $user->password;
        $data['image'] = (new self)->uploadImage(request(), 'image', 'images/user', "images/user/{$user->image}", $user->image);
        $user->update($data);
    }

    public function startFreeTrial() {
        $this->update([
            'is_free_trial' => true,
            'free_trial_started_at' => now(),
            'free_trial_ends_at' => now()->addDays(getSetting('Free Trial Days')),
        ]);
    }

    public function endFreeTrial() {
        $this->update([
            'is_free_trial' => false,
        ]);
    }

    public function startSubscription() {
        $this->update([
            'subscription_status' => 1,
        ]);
    }

    public function stopSubscription() {
        $this->update([
            'subscription_status' => 0,
        ]);
    }

    public function farms()
    {
        return $this->hasMany(Farm::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function savedFarms()
    {
        return $this->belongsToMany(Farm::class, 'saved_farms')
            ->withPivot('save') // Include the 'save' attribute
            ->withTimestamps();;
    }

    public function subscription() {
        return $this->hasOne(Subscription::class);
    }
}
