<?php

namespace App\Business\Website2;

class T_PageMeta
{
    /**
     * @var string|null
     */
    public string $description;
    /**
     * @var string|null
     */
    public string $title;
    /**
     * @var string|null
     */
    public string $short_description;

    /**
     * A http link starting with /. Keep the case and the slashes from the original link.
     * Only translate the parts between the slashes.
     *
     * @var string|null
     */
    public string $permalink;
}
