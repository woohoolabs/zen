<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Container\Definition;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Zen\Container\Definition\SelfDefinition;

class SelfDefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function ToPhpCode()
    {
        $definition = new SelfDefinition("");

        $this->assertEquals($this->getDefinitionSourceCode("SelfDefinition.php"), $definition->toPhpCode());
    }

    private function getDefinitionSourceCode(string $fileName)
    {
        return str_replace("<?php\n", "", file_get_contents(realpath(__DIR__ . "/../../Fixture/Definition/" . $fileName)));
    }
}
