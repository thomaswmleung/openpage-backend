<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @SWG\Swagger(
 *   basePath="/openpage-backend/public",
 *   @SWG\Info(
 *     title="Openpage API",
 *     version="1.0.0"
 *   )
 * )
 */

/**
 * @SWG\SecurityScheme(
 *   securityDefinition="token",
 *   type="apiKey",
 *   in="header",
 *   name="token"
 * )
 */

/**
 * @SWG\Tag(
 *   name="Login",
 *   description="Login related API's"
 * )
 * @SWG\Tag(
 *   name="User",
 *   description="User related API's"
 * )
 * * @SWG\Tag(
 *   name="Media",
 *   description="Media realated API's"
 * )
 * * @SWG\Tag(
 *   name="page_group",
 *   description="Page group realated API's"
 * )
 * 
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
