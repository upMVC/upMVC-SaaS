<?php

/**
 * Composer package providers for this host app.
 *
 * Floor: SaasServiceProvider (tenancy + Harvest).
 * Optional: uncomment Payments after `composer require bitshost/upmvc-payments-pack`.
 *
 * @see https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md
 */

return [
    \BitsHost\UpmvcSaas\SaasServiceProvider::class,
    // \BitsHost\UpmvcPayments\PaymentsServiceProvider::class,
];
