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
 *   description="Media related API's"
 * )
 * @SWG\Tag(
 *   name="page_group",
 *   description="Page group related API's"
 * )
 * @SWG\Tag(
 *   name="Question Type",
 *   description="Question Type related API's"
 * )
 * @SWG\Tag(
 *   name="organization",
 *   description="Organization related API's"
 * )
 * @SWG\Tag(
 *   name="Subject",
 *   description="Subject related API's"
 * )
 * @SWG\Tag(
 *   name="Book",
 *   description="Book related API's"
 * )
 * @SWG\Tag(
 *   name="Section",
 *   description="Section related API's"
 * )
 * @SWG\Tag(
 *   name="Question",
 *   description="Question related API's"
 * )
 * 
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
