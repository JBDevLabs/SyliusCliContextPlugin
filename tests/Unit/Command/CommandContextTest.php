<?php
/**
 * @copyright Macintoshplus (c) 2022
 * Added by : Macintoshplus at 03/02/2022 21:54
 */

declare(strict_types=1);

namespace JbDevLabs\Tests\SyliusCliContextPlugin\Unit\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CommandContextTest extends KernelTestCase
{
    /** @dataProvider commandAndResultProvider */
    public function testExecute(string $commandName, string $result): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find($commandName);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString($result, $output);

    }

    public function commandAndResultProvider(): array
    {
        return [
            'app:command-without-context' => ['app:command-without-context', 'Channel name: Fashion Web Store'],
            'app:command-with-context' => ['app:command-with-context', 'Channel name: Fashion Web Store'],
            'app:other-command' => ['app:other-command', 'Channel name: Fashion Web Store'],
            'app:change-channel' => ['app:change-channel', 'Current channel: "Fashion Web Store" - FASHION_WEB'],
        ];
    }

    public function testExecuteFail(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:change-channel');
        $commandTester = new CommandTester($command);

        $this->expectExceptionMessage('Channel with code "TEST" not found');

        $commandTester->execute(['--channel' => 'TEST']);
    }
}
