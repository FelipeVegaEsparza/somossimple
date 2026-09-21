<?php

namespace Tests\Feature;

use App\Enums\LoyaltyActivityType;
use App\Enums\Module;
use App\Models\Business;
use App\Models\LoyaltyActivity;
use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyReward;
use App\Models\LoyaltyWalletCard;
use App\Models\User;
use App\Services\Loyalty\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyTest extends TestCase
{
    use RefreshDatabase;

    private function negocio(string $name = 'Café Patagonia'): array
    {
        $user = User::factory()->create();
        $business = Business::createForAccount($user, $name);
        $business->moduleAccess()->where('module', Module::Loyalty->value)->update(['active' => true]);

        return [$user, $business];
    }

    private function programa(Business $business): LoyaltyProgram
    {
        return app(LoyaltyService::class)->program($business);
    }

    public function test_el_modulo_aparece_en_el_panel_y_en_el_menu(): void
    {
        [$user] = $this->negocio();

        $this->actingAs($user)->get(route('panel.modules'))->assertOk()->assertSee('Fidelización');

        $this->actingAs($user)->get(route('panel.loyalty.index'))
            ->assertOk()
            ->assertSee('Fidelización');

        // Sin el módulo activo no es accesible.
        [$user2, $business2] = $this->negocio('Otro Negocio');
        $business2->moduleAccess()->where('module', Module::Loyalty->value)->update(['active' => false]);

        $this->actingAs($user2)->get(route('panel.loyalty.index'))->assertNotFound();
    }

    public function test_la_configuracion_del_programa_se_guarda(): void
    {
        [$user, $business] = $this->negocio();

        $this->actingAs($user)->put(route('panel.loyalty.program.update'), [
            'name' => 'Club Amigos',
            'description' => 'Gana visitas y obtén premios.',
            'type' => 'visits',
            'unit_name' => 'visitas',
            'earn_units' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('panel.loyalty.program'));

        $program = $business->fresh()->loyaltyProgram;
        $this->assertEquals('Club Amigos', $program->name);
        $this->assertEquals('visits', $program->type->value);
        $this->assertEquals('visitas', $program->unit_name);
        $this->assertNull($program->earn_amount);
    }

    public function test_cada_cliente_recibe_un_identificador_unico(): void
    {
        [$user] = $this->negocio();

        $this->actingAs($user)->post(route('panel.loyalty.members.store'), ['name' => 'Juan Pérez', 'phone' => '+56911111111']);
        $this->actingAs($user)->post(route('panel.loyalty.members.store'), ['name' => 'Ana Soto']);

        $members = LoyaltyMember::orderBy('id')->get();
        $this->assertEquals('CLI-000001', $members[0]->code);
        $this->assertEquals('CLI-000002', $members[1]->code);
        $this->assertNotEquals($members[0]->token, $members[1]->token);
        $this->assertNotEmpty($members[0]->token);
    }

    public function test_registrar_puntos_actualiza_saldo_y_historial(): void
    {
        [$user, $business] = $this->negocio();
        $member = app(LoyaltyService::class)->createMember($business, ['name' => 'Juan Pérez']);

        $this->actingAs($user)->post(route('panel.loyalty.quick.store'), [
            'member' => $member->code,
            'action' => 'points',
            'amount' => 100,
        ])->assertRedirect();

        $this->assertEquals(100, $member->fresh()->points);
        $activity = LoyaltyActivity::first();
        $this->assertEquals(LoyaltyActivityType::EarnPoints, $activity->type);
        $this->assertEquals(100, $activity->units);
        $this->assertEquals($user->id, $activity->user_id);
    }

    public function test_canjear_una_recompensa_descuenta_y_registra_el_canje(): void
    {
        [$user, $business] = $this->negocio();
        $loyalty = app(LoyaltyService::class);
        $member = $loyalty->createMember($business, ['name' => 'Juan Pérez']);
        $reward = LoyaltyReward::create([
            'business_id' => $business->id,
            'name' => 'Hamburguesa gratis',
            'requirement_type' => 'points',
            'requirement_units' => 50,
            'is_active' => true,
        ]);

        $loyalty->register($member, LoyaltyActivityType::EarnPoints, 100, 'Compra', $user);

        // Al cruzar el umbral se registra "recompensa obtenida".
        $this->assertEquals(1, $member->activities()->where('type', LoyaltyActivityType::RewardEarned->value)->count());

        $this->actingAs($user)
            ->post(route('panel.loyalty.members.reward', [$member, $reward]))
            ->assertRedirect();

        $this->assertEquals(50, $member->fresh()->points);
        $this->assertEquals(1, $member->activities()->where('type', LoyaltyActivityType::RewardRedeemed->value)->count());
    }

    public function test_no_se_puede_canjear_sin_unidades_suficientes(): void
    {
        [$user, $business] = $this->negocio();
        $member = app(LoyaltyService::class)->createMember($business, ['name' => 'Ana']);
        $reward = LoyaltyReward::create([
            'business_id' => $business->id,
            'name' => 'Café gratis',
            'requirement_type' => 'points',
            'requirement_units' => 500,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('panel.loyalty.members.reward', [$member, $reward]))
            ->assertSessionHas('error');

        $this->assertEquals(0, $member->fresh()->points);
        $this->assertEquals(0, $member->activities()->count());
    }

    public function test_el_qr_del_cliente_se_genera(): void
    {
        [$user, $business] = $this->negocio();
        $member = app(LoyaltyService::class)->createMember($business, ['name' => 'Ana']);

        $response = $this->actingAs($user)->get(route('panel.loyalty.members.qr', $member));

        $response->assertOk();
        $this->assertStringContainsString('image/svg+xml', $response->headers->get('content-type'));
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_pagina_publica_registro_y_tarjeta_digital(): void
    {
        [, $business] = $this->negocio();
        $this->programa($business);

        $this->get(route('loyalty.public', $business->slug))->assertOk()->assertSee('Fidelización');

        $this->post(route('loyalty.register', $business->slug), [
            'name' => 'María López',
            'phone' => '+56922222222',
        ])->assertRedirect();

        $member = LoyaltyMember::first();
        $this->assertEquals('CLI-000001', $member->code);

        $this->get(route('loyalty.card', [$business->slug, $member->token]))
            ->assertOk()
            ->assertSee('María López')
            ->assertSee($member->code);

        $this->get(route('loyalty.qr', [$business->slug, $member->token]))
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml');
    }

    public function test_la_pagina_publica_requiere_el_modulo_activo(): void
    {
        [, $business] = $this->negocio();
        $business->moduleAccess()->where('module', Module::Loyalty->value)->update(['active' => false]);

        $this->get(route('loyalty.public', $business->slug))->assertNotFound();
    }

    public function test_wallet_no_configurado_no_simula_la_integracion(): void
    {
        [$user, $business] = $this->negocio();
        $member = app(LoyaltyService::class)->createMember($business, ['name' => 'Ana']);

        $this->actingAs($user)
            ->post(route('panel.loyalty.members.wallet', [$member, 'apple']))
            ->assertSessionHas('error');

        $card = LoyaltyWalletCard::where('loyalty_member_id', $member->id)->where('platform', 'apple')->first();
        $this->assertNotNull($card);
        $this->assertEquals('not_added', $card->status->value);

        // La ficha muestra claramente que no está configurado.
        $this->actingAs($user)->get(route('panel.loyalty.members.show', $member))
            ->assertOk()
            ->assertSee('Apple Wallet aún no está configurado.');
    }

    public function test_el_perfil_publico_enlaza_fidelizacion(): void
    {
        [, $business] = $this->negocio();

        $this->get(route('p.show', $business->slug))
            ->assertOk()
            ->assertSee(route('loyalty.public', $business->slug), false)
            ->assertSee('Fidelización');
    }

    public function test_aislamiento_entre_negocios(): void
    {
        [$userA, $businessA] = $this->negocio('Negocio A');
        [, $businessB] = $this->negocio('Negocio B');

        $memberA = app(LoyaltyService::class)->createMember($businessA, ['name' => 'Cliente A']);

        // El usuario B no puede ver la ficha del cliente del negocio A.
        $userB = $businessB->account;
        $this->actingAs($userB)->get(route('panel.loyalty.members.show', $memberA))->assertNotFound();
    }
}
