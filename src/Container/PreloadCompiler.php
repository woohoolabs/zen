<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

final class PreloadCompiler
{
    /**
     * @param string[] $preloadedDefinitions
     */
    public function compile(array $preloadedDefinitions): string
    {
        $files = array_unique($preloadedDefinitions);

        $preloader = "<?php\n\n";

        foreach ($files as $file) {
            $preloader .= "opcache_compile_file('$file');\n";
        }

        return $preloader;
    }
}
