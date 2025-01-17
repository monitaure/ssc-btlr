<?php

declare(strict_types=1);

namespace tests\Ssc\Btlr\Cht\Augment\UsingLlm;

use Ssc\Btlr\Cht\Augment\UsingLlm\Augment;
use Ssc\Btlr\Cht\Augment\UsingLlm\Augment\GetLastMessages;
use Ssc\Btlr\Cht\Augment\UsingLlm\Log;
use Ssc\Btlr\Cht\Augment\UsingLlm\Log\Source;
use Ssc\Btlr\Framework\Filesystem\ReadFile;
use Ssc\Btlr\Framework\Template\Replace;
use Ssc\Btlr\TestFramework\BtlrServiceTestCase;

class AugmentTest extends BtlrServiceTestCase
{
    /**
     * @test
     */
    public function it_augments_user_prompt(): void
    {
        // Fixtures
        $userPrompt = 'Write code for me, please';
        $augmentedPromptTemplateFilename = './templates/cht/prompts/augmented.txt';
        $lastMessagesFilename = './var/cht/logs/last_messages';
        $withConfig = [
            'augmented_prompt_template_filename' => $augmentedPromptTemplateFilename,
            'llm_engine' => 'chatgpt-gpt-3.5-turbo',
            'last_messages_filename' => $lastMessagesFilename,
        ];
        $lastMessages = "USER: Do you read me?\nBTLR: Affirmative dev, I read you\n";
        $augmentedPromptTemplate = "%last_messages%USER: %user_prompt%\n";
        $thoseParameters = [
            'last_messages' => $lastMessages,
            'user_prompt' => $userPrompt,
        ];
        $augmentedPrompt = "{$lastMessages}USER: {$userPrompt}\n";

        // Dummies
        $getLastMessages = $this->prophesize(GetLastMessages::class);
        $log = $this->prophesize(Log::class);
        $readFile = $this->prophesize(ReadFile::class);
        $replace = $this->prophesize(Replace::class);

        // Stubs & Mocks
        $getLastMessages->from($lastMessagesFilename)
            ->willReturn($lastMessages);
        $readFile->in($augmentedPromptTemplateFilename)
            ->willReturn($augmentedPromptTemplate);
        $replace->in($augmentedPromptTemplate, $thoseParameters)
            ->willReturn($augmentedPrompt);
        $log->entry($augmentedPrompt, $withConfig, Source::AUGMENTED_PROMPT)
            ->shouldBeCalled();

        // Assertion
        $augment = new Augment(
            $getLastMessages->reveal(),
            $log->reveal(),
            $readFile->reveal(),
            $replace->reveal(),
        );
        $actualAugmentedPrompt = $augment->the(
            $userPrompt,
            $withConfig,
        );
        self::assertSame($augmentedPrompt, $actualAugmentedPrompt);
    }
}
