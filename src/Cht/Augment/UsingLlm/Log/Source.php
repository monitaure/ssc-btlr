<?php

declare(strict_types=1);

namespace Ssc\Btlr\Cht\Augment\UsingLlm\Log;

class Source
{
    public const USER_PROMPT = 'user_prompt';
    public const AUGMENTED_PROMPT = 'augmented_prompt';
    public const MODEL_COMPLETION = 'model_completion';

    public const PRIORITIES = [
        self::USER_PROMPT => '000',
        self::AUGMENTED_PROMPT => '500',
        self::MODEL_COMPLETION => '900',
    ];
}
