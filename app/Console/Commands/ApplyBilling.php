<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;

class ApplyBilling extends Command
{
    protected $signature = 'app:apply-billing';

    protected $description = 'Desactiva módulos de pago atrasados (sin pago al día tras la gracia) conservando sus datos';

    public function handle(BillingService $billing): int
    {
        $count = $billing->applyBilling();

        $this->info("Módulos de pago desactivados por impago: {$count}.");

        return self::SUCCESS;
    }
}
