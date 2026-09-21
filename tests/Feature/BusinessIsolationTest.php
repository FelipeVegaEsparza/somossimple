<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\ModuleAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_modelo_es_la_raiz_y_una_cuenta_tiene_un_negocio(): void
    {
        $business = Business::factory()->create();

        $this->assertNotNull($business->account);
        $this->assertTrue($business->account->business->is($business));
    }

    public function test_la_consulta_acotada_a_un_negocio_no_expone_datos_de_otro(): void
    {
        $negocioA = Business::factory()->create();
        $negocioB = Business::factory()->create();

        ModuleAccess::create(['business_id' => $negocioA->id, 'module' => Module::Catalog->value, 'active' => true]);
        ModuleAccess::create(['business_id' => $negocioA->id, 'module' => Module::Reservations->value, 'active' => true]);
        ModuleAccess::create(['business_id' => $negocioB->id, 'module' => Module::Catalog->value, 'active' => true]);

        $deNegocioA = ModuleAccess::ofBusiness($negocioA)->pluck('module')->sort()->values();
        $deNegocioB = ModuleAccess::ofBusiness($negocioB)->pluck('module')->sort()->values();

        $this->assertEquals([Module::Catalog->value, Module::Reservations->value], $deNegocioA->all());
        $this->assertEquals([Module::Catalog->value], $deNegocioB->all());
        $this->assertNotContains(Module::Reservations->value, $deNegocioB->all());
    }
}
