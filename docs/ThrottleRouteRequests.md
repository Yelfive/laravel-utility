# ThrottleRouteRequests

## Namespace

```
fk\utility\Routing\Middleware\ThrottleRouteRequests
```

## Overview

Based on Laravel `ThrottleRequests` and extends it in three facets:

1. Throttle with route: every route has its own rate limit control
2. Control on the fly: to miss the throttle hit on purpose. This is useful when you want to limit only when the request passes

    ```php
    <?php
    ThrottleRouteRequests::ignore(bool $ignore = true);
    ```

3. Throttle check happens when requested, but recorded when respond. with allow the former trait(**#2**) to be available