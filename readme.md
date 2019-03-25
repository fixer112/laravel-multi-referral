# laravel-multi-referral

A Multi Referral System With Laravel

## Installation

Via [Composer](https://getcomposer.org) to add the package to your project's dependencies:

```bash
$ composer require devi/laravel-multi-referral "~1.0"
```

First add service providers into the config/app.php

```php
Devi\MultiReferral\MultiReferralServiceProvider::class
```

Publish the migrations

```bash
$ php artisan vendor:publish --provider="Devi\MultiReferral\MultiReferralServiceProvider" --tag="migrations"
```

Publish the config

```bash
$ php artisan vendor:publish --provider="Devi\MultiReferral\MultiReferralServiceProvider" --tag="config"
```

## Setup the model

Add MultiReferral Trait to your User model.

```php
use Devi\MultiReferral\Traits\UserReferral

class User extends Model
{
    use MultiReferral;
    
    protected $fillable = [
            'name', 'email', 'password','login'
        ];
}
```

## Setup the RegisterController

Add a login field to the validator

```php
// Within App\Http\Controllers\Auth\RegisterController Class...

protected function validator(array $data)
    {
        return Validator::make($data, [
            'login' => ['required', 'string', 'min:3','max:100','unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }
    
protected function create(array $data)
    {
        $user = User::create([
            'login' => $data['login'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $parents = $user->findAndSaveAllParents();

        //actions for parents
        if(!empty($parents)){
            foreach ($parents as $parent){
                $userId = $parent['user_id'];
                $level = $parent['level'];
                //bonus accruals...
            }
        }
        return $user;
    }
```

## Setup the register page

```html
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Login') }}</label>

    <div class="col-md-6">
        <input id="name" type="text" class="form-control{{ $errors->has('login') ? ' is-invalid' : '' }}" name="login" value="{{ old('login') }}" required autofocus>

        @if ($errors->has('login'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('login') }}</strong>
            </span>
        @endif
    </div>
</div>
```




## Usage

Assigning CheckReferral Middleware To Routes.

```php
// Within App\Http\Kernel Class...

protected $routeMiddleware = [
    'referral' => \Devi\MultiReferral\Http\Middleware\CheckReferral::class,
];
```

Once the middleware has been defined in the HTTP kernel, you may use the middleware method to assign middleware to a route:

```php
Route::middleware(['referral'])->group(function () {
    Route::get('/', 'HomeController@index');
});
```

Get referral link

```php
$user = auth()->user();
$user->getReferralLink();
```

Get a list of referrals 

```php
use Devi\MultiReferral\Models\ReferralList;

$user = auth()->user();
$referralLists = ReferralList::whereUserId($user->id)->orderBy("created_at","desc")->get();
dump($referralLists[0]->user->email);
```


## License

Licensed under the [MIT license](https://github.com/segeysomok/laravel-multi-referral/blob/master/LICENSE).
