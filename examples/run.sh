#!/usr/bin/env bash

./bin/zen --memory-limit="128M" --preload="/var/www/examples/preload.php" build /var/www/examples/Container.php "WoohooLabs\\Zen\\Examples\\CompilerConfig"
