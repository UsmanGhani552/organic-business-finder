<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\ImageUploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ImageUploadTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'image'
        
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

    public static function registerUser($data){

        $data['password'] = Hash::make($data['password']);
        $user = self::create($data);
        return $user;
    }

    public function saveFcmToken($fcmToken){
        $this->deviceTokens()->updateOrCreate(
            ['fcm_token' => $fcmToken],
            ['user_id' => $this->id],
        );
    }

    public static function editProfile($user,array $data){
        $data['image'] = (new self)->uploadImage(request(),'image','user',"user/{$user->image}" , $user->image);
        $user->update($data); 
        return $user->fresh();
    }

    public static function changePassword($user,array $data): void{
        $data['password'] = Hash::make($data['password']);
        $user->update($data);
    }

    public static function editImage($user): void{
        // dd($data['image']);
        $uploadedImagePath = (new self)->uploadImage(request(),'image','user',"user/{$user->image}" , $user->image);
        $user->update(['image' => $uploadedImagePath]);
    }

    public function farms() {
        return $this->hasMany(Farm::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function savedFarms(){
        return $this->belongsToMany(Farm::class,'saved_farms');
    }
}
