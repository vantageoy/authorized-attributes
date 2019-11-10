<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductTest extends Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Gate::policy(Product::class, ProductPolicy::class);
    }

    /** @test */
    public function it_should_show_admin_notes_for_admins()
    {
        Auth::login(new User(['role' => 'admin']));
        
        $product = new Product([
            'admin_notes' => 'foo',
            'buy_price' => 100,
            'price' => 200,
        ]);

        $this->assertSame($product->toArray(), [
            'admin_notes' => $product->admin_notes,
            'price' => $product->price,
        ]);
    }

    /** @test */
    public function it_should_show_admin_notes_for_employees()
    {        
        Auth::login(new User(['role' => 'admin']));
        $product = new Product([
            'admin_notes' => 'foo',
            'buy_price' => 100,
            'price' => 200,
        ]);

        Auth::login(new User(['role' => 'employee']));

        $this->assertSame($product->toArray(), [
            'admin_notes' => $product->admin_notes,
            'price' => $product->price,
        ]);
    }

    /** @test */
    public function it_should_allow_admin_notes_be_edited_by_admins()
    {        
        Auth::login(new User(['role' => 'admin']));
        $product = new Product([
            'admin_notes' => 'foo',
            'buy_price' => 100,
            'price' => 200,
        ]);

        $this->assertSame($product->toArray()['admin_notes'], $product->admin_notes);
    }

    /** @test */
    public function it_should_disallow_admin_notes_be_edited_by_employees()
    {        
        Auth::login(new User(['role' => 'employee']));
        $product = new Product([
            'admin_notes' => 'foo',
            'buy_price' => 100,
            'price' => 200,
        ]);

        $this->assertFalse(isset($product->toArray()['admin_notes']));
    }
}

class Product extends Illuminate\Database\Eloquent\Model
{
    use Vantage\AuthorizedAttributes;

    protected $fillable = ['admin_notes', 'buy_price', 'price'];
    protected $hidden = ['buy_price'];
}

class User extends Illuminate\Database\Eloquent\Model implements Illuminate\Contracts\Auth\Authenticatable
{
    use Illuminate\Auth\Authenticatable;
    
    protected $fillable = ['role'];
}

class ProductPolicy
{
    public function seeAdminNotes(User $user, Product $product)
    {
        return in_array($user->role, ['admin', 'employee']);
    }

    public function editAdminNotes(User $user, Product $product)
    {
        return $user->role === 'admin';
    }
}
