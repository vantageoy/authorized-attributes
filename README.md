## Authorized Model Attributes for Laravel 5

Provides ability to dynamically add hidden columns to the models.

**Note:** There is also a `makeHidden()` method available if you only need hide certain columns *only from one model*.

### Installation

Just require the package to your Laravel project.

```
composer require salomoni/authorized-attributes
```

### Usage

Use the `Salomoni\AuthorizedAttributes` trait

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Salomoni\AuthorizedAttributes;

class Post extends Model
{
    use AuthorizedAttributes;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['author_comments'];
    
    /**
     * The attributes that should be fillable from requests.
     *
     * @var array
     */
    protected $fillable = ['content'];
}
```

[Create and register a model policy](https://laravel.com/docs/authorization#creating-policies). Add methods for the hidden attributes in camel-case prefixed with `see`.

```php
<?php

namespace App\Policies;

use App\Post;
use App\User;

class PostPolicy
{
    /**
     * Determine if a post author_comments atrribute can be seen and changed by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return bool
     */
    public function seeAuthorComments(User $user, Post $post)
    {
        return $user->isAuthor() || $user->created($post);
    }
    
    /**
     * Determine if the Post content atrribute can be changed by the user.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return bool
     */
    public function changeContent(User $user, Post $post)
    {
        return $user->isAuthor() || $user->created($post);
    }
}
```

## Other

### Disable policy checks

E.g.

```config(['authorized-attributes.enabled' => false]); // disable attribute policy checks```

### Mixin with always hidden attributes

The attributes will be hidden if no policy or ability (method) are found.

### Modify the ability method name

```php
<?php

class Post extends Model
{
    /**
     * Get the method name for the attribute visibility ability in the model policy.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttributeViewAbilityMethod($attribute)
    {
        return $attribute;
    }
    
    /**
     * Get the method name for the attribute change ability in the model policy.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttributeUpdateAbilityMethod($attribute)
    {
        return $attribute;
    }

}
```
