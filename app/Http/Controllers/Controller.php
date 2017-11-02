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
 *   name="Organization",
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
 *   name="Page",
 *   description="Page related API's"
 * )
 * @SWG\Tag(
 *   name="Section",
 *   description="Section related API's"
 * )
 * @SWG\Tag(
 *   name="Question",
 *   description="Question related API's"
 * )
 * @SWG\Tag(
 *   name="Resource",
 *   description="Resource related API's"
 * )
 * @SWG\Tag(
 *   name="Layout",
 *   description="Layout related API's"
 * )
 * @SWG\Tag(
 *   name="Class",
 *   description="Class related API's"
 * )
 * @SWG\Tag(
 *   name="Class Flow",
 *   description="Class Flow related API's"
 * )
 * @SWG\Tag(
 *   name="Keyword",
 *   description="Keyword related API's"
 * )
 * @SWG\Tag(
 *   name="Domain",
 *   description="Domain related API's"
 * )
 * @SWG\Tag(
 *   name="Sub Domain",
 *   description="Sub Domain related API's"
 * )
 * @SWG\Tag(
 *   name="Knowledge Unit",
 *   description="Knowledge Unit related API's"
 * )
 * @SWG\Tag(
 *   name="Particular",
 *   description="Particular related API's"
 * )
 * @SWG\Tag(
 *   name="Resource Category",
 *   description="Resource Category related API's"
 * )
 * @SWG\Tag(
 *   name="Codex",
 *   description="Codex related API's"
 *   name="Bulk Upload Page Group",
 *   description="Bulk upload api for creating page groups"
 * )
 * 
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
