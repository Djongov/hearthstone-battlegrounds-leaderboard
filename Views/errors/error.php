<?php

use Components\ErrorPageHandler;

echo ErrorPageHandler::render(404, 'Not found', 'The page you are looking for is not there.... or you are just trying random stuff...', $theme);
