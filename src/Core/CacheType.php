<?php

namespace PowerDI\Core;

enum CacheType {
    case Uncacheable;
    case AutoCacheable;
    case ManuallyCacheable;
    case PerUserCacheable;
}
