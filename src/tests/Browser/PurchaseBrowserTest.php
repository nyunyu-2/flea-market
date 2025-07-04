<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class PurchaseBrowserTest extends DuskTestCase
{
    /** @test */
    public function 小計画面で変更が即時反映される()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/cart')
                ->assertSee('選択中の支払い方法')
                ->select('payment_method_select', 'paypal')
                ->waitForText('選択中の支払い方法: PayPal')
                ->assertSee('選択中の支払い方法: PayPal');
        });
    }
}

