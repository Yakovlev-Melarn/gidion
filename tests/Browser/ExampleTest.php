<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function testBasicExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://www.ozon.ru/api/entrypoint-api.bx/page/json/v2?url=/searchSuggestions/search/?text=&from_global=true')->dd();
            //$res->dd();
//                    ->assertSee('Gidion');
        });
    }
}
