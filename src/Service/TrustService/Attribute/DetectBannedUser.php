<?php

namespace App\Service\TrustService\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class DetectBannedUser {}
