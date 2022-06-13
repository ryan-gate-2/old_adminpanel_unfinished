<?php

namespace App\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;

abstract class ResourceRegular extends NovaResource
{
	    public static $perPageViaRelationship = 25;


}
