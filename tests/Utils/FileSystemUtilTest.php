<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Utils;

use PHPUnit\Framework\TestCase;
use stdClass;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointA;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointC1;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointC2;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointEInterface;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPoint\EntryPointGAbstract;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPointD1\EntryPointD1;
use WoohooLabs\Zen\Tests\Fixture\DependencyGraph\EntryPointD2\EntryPointD2;
use WoohooLabs\Zen\Utils\FileSystemUtil;
use function dirname;

class FileSystemUtilTest extends TestCase
{
    /**
     * @test
     */
    public function getRelativeFilenameWhenNotFound()
    {
        $filename = FileSystemUtil::getRelativeFilenameForClass("", "InexistentClass");

        $this->assertEquals("", $filename);
    }

    /**
     * @test
     */
    public function getRelativeFilenameWhenInternalClass()
    {
        $filename = FileSystemUtil::getRelativeFilenameForClass("", stdClass::class);

        $this->assertEquals("", $filename);
    }

    /**
     * @test
     */
    public function getRelativeFilenameWithoutTrailingSlash()
    {
        $filename = FileSystemUtil::getRelativeFilenameForClass(dirname(__DIR__) . "/Fixture/DependencyGraph/EntryPoint", EntryPointA::class);

        $this->assertEquals("EntryPointA.php", $filename);
    }

    /**
     * @test
     */
    public function getRelativeFilenameWithTrailingSlash()
    {
        $filename = FileSystemUtil::getRelativeFilenameForClass(dirname(__DIR__) . "/Fixture/DependencyGraph/EntryPoint/", EntryPointA::class);

        $this->assertEquals("EntryPointA.php", $filename);
    }

    /**
     * @test
     */
    public function getRelativeFilenameWhenInSubdirectory()
    {
        $filename = FileSystemUtil::getRelativeFilenameForClass(dirname(__DIR__) . "/Fixture/DependencyGraph/", EntryPointA::class);

        $this->assertEquals("EntryPoint/EntryPointA.php", $filename);
    }

    /**
     * @test
     */
    public function getRelativeFilenameWhenOutOfRootDirectory()
    {
        $filename = FileSystemUtil::getRelativeFilenameForClass(dirname(__DIR__) . "/Fixture/DependencyGraph/Mixed", EntryPointA::class);

        $this->assertEquals(dirname(__DIR__) . "/Fixture/DependencyGraph/EntryPoint/EntryPointA.php", $filename);
    }

    /**
     * @test
     */
    public function getClassesInPathWhenCOnlyConcreteClasses()
    {
        $classes = FileSystemUtil::getClassesInPath(dirname(__DIR__) . "/Fixture/DependencyGraph/EntryPoint", true);

        $this->assertEqualsCanonicalizing(
            [
                EntryPointA::class,
                "EntryPointB",
                EntryPointC1::class,
                EntryPointC2::class,
                EntryPointD1::class,
                EntryPointD2::class,
            ],
            $classes
        );
    }

    /**
     * @test
     */
    public function getClassesInPathWhenAllClasses()
    {
        $classes = FileSystemUtil::getClassesInPath(dirname(__DIR__) . "/Fixture/DependencyGraph/EntryPoint", false);

        $this->assertContains(EntryPointEInterface::class, $classes);
        $this->assertContains(EntryPointGAbstract::class, $classes);
    }
}
