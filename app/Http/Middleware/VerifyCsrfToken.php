<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login', 'log_out', 'register','forgot_password','reset_password', 'media', 'page_group','book','subject','question_type',
        'organization','page','section','question','layout','resource','class','class_flow',
        'keyword','domain','sub_domain','knowledge_unit','particular','resource_category','codex'
    ];

}
