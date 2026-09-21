<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Business;
use App\Models\CatalogItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function negocioConCatalogo(): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, 'Restaurante Austral');
        $business->moduleAccess()->where('module', Module::Catalog->value)->update(['active' => true]);

        return [$user, $business];
    }

    public function test_un_mismo_catalogo_sirve_para_productos_servicios_y_menu(): void
    {
        [, $restaurante] = $this->negocioConCatalogo();
        $barberiaUser = User::factory()->create();
        $barberia = Business::createForAccount($barberiaUser, 'Barbería Patagonia');
        $barberia->moduleAccess()->where('module', Module::Catalog->value)->update(['active' => true]);

        CatalogItem::create([
            'business_id' => $restaurante->id,
            'name' => 'Hamburguesa Austral',
            'price_mode' => CatalogItem::MODE_EXACT,
            'price' => 9990,
        ]);
        CatalogItem::create([
            'business_id' => $barberia->id,
            'name' => 'Corte clásico',
            'price_mode' => CatalogItem::MODE_EXACT,
            'price' => 12000,
        ]);

        $this->assertEquals(1, CatalogItem::ofBusiness($restaurante)->count());
        $this->assertEquals(1, CatalogItem::ofBusiness($barberia)->count());

        $this->get(route('p.catalog', $restaurante->slug))->assertSee('Hamburguesa Austral');
        $this->get(route('p.catalog', $barberia->slug))->assertSee('Corte clásico');
    }

    public function test_crear_y_editar_un_elemento_desde_el_panel(): void
    {
        [$user, $business] = $this->negocioConCatalogo();

        $this->actingAs($user)->post(route('panel.catalog.store'), [
            'name' => 'Corte clásico',
            'description' => 'Corte con tijera y máquina.',
            'price_mode' => 'exact',
            'price' => 12000,
            'active' => '1',
        ])->assertRedirect(route('panel.catalog.index'));

        $item = CatalogItem::ofBusiness($business)->first();
        $this->assertNotNull($item);
        $this->assertEquals('Corte con tijera y máquina.', $item->description);

        $this->actingAs($user)->put(route('panel.catalog.update', $item), [
            'name' => 'Corte clásico + barba',
            'description' => 'Corte y arreglo de barba.',
            'price_mode' => 'from',
            'price' => 18000,
            'active' => '1',
        ])->assertRedirect(route('panel.catalog.index'));

        $this->assertEquals('Corte clásico + barba', $item->fresh()->name);
        $this->assertEquals('from', $item->fresh()->price_mode);
    }

    public function test_precio_opcional_con_modalidades_exacto_desde_y_sin_precio(): void
    {
        [, $business] = $this->negocioConCatalogo();

        foreach ([
            ['name' => 'Corte clásico', 'price_mode' => 'exact', 'price' => 12000],
            ['name' => 'Instalación de calefont', 'price_mode' => 'from', 'price' => 50000],
            ['name' => 'Presupuesto a medida', 'price_mode' => 'none', 'price' => null],
        ] as $data) {
            CatalogItem::create(['business_id' => $business->id, 'active' => true] + $data);
        }

        $html = $this->get(route('p.catalog', $business->slug))->getContent();

        $this->assertStringContainsString('$12.000', $html);
        $this->assertStringContainsString('Desde $50.000', $html);
        $this->assertStringContainsString('Consultar precio', $html);
    }

    public function test_categorias_opcionales_y_agrupacion(): void
    {
        [, $business] = $this->negocioConCatalogo();
        $entradas = $business->catalogCategories()->create(['name' => 'Entradas']);

        CatalogItem::create(['business_id' => $business->id, 'category_id' => $entradas->id, 'name' => 'Papas fritas', 'price_mode' => 'exact', 'price' => 3500, 'active' => true]);
        CatalogItem::create(['business_id' => $business->id, 'name' => 'Completo italiano', 'price_mode' => 'exact', 'price' => 4000, 'active' => true]);

        $html = $this->get(route('p.catalog', $business->slug))->getContent();
        $this->assertStringContainsString('Entradas', $html);
        $this->assertStringContainsString('Papas fritas', $html);
        $this->assertStringContainsString('Completo italiano', $html);
    }

    public function test_activar_y_desactivar_elementos_conservando_datos(): void
    {
        [$user, $business] = $this->negocioConCatalogo();
        $item = CatalogItem::create(['business_id' => $business->id, 'name' => 'Barba', 'price_mode' => 'exact', 'price' => 8000, 'active' => true]);

        $this->actingAs($user)->post(route('panel.catalog.toggle', $item));
        $this->assertFalse($item->fresh()->active);
        $this->get(route('p.catalog', $business->slug))->assertDontSee('Barba');

        $this->actingAs($user)->post(route('panel.catalog.toggle', $item));
        $this->assertTrue($item->fresh()->active);
        $this->get(route('p.catalog', $business->slug))->assertSee('Barba');
    }

    public function test_al_desactivar_el_modulo_los_datos_del_catalogo_se_conservan(): void
    {
        [, $business] = $this->negocioConCatalogo();
        CatalogItem::create(['business_id' => $business->id, 'name' => 'Corte + barba', 'price_mode' => 'exact', 'price' => 18000, 'active' => true]);

        $business->moduleAccess()->where('module', Module::Catalog->value)->update(['active' => false]);

        $this->assertFalse($business->isModuleActive(Module::Catalog));
        $this->assertEquals(1, $business->catalogItems()->count(), 'los datos no se eliminan al desactivar el módulo');

        $this->get(route('p.catalog', $business->slug))->assertNotFound();

        $business->moduleAccess()->where('module', Module::Catalog->value)->update(['active' => true]);

        $this->get(route('p.catalog', $business->slug))->assertSee('Corte + barba');
    }
}
