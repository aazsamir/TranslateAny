<?php

declare(strict_types=1);

namespace App\Api\DeepL\V2\Translate;

use Tempest\Router\IsRequest;
use Tempest\Router\Request;

// {
//     "text": [
//       "Hello, World!"
//     ],
//     "source_lang": "EN",
//     "target_lang": "DE",
//     "context": "string",
//     "show_billed_characters": true,
//     "split_sentences": "1",
//     "preserve_formatting": false,
//     "formality": "default",
//     "model_type": "quality_optimized",
//     "glossary_id": "def3a26b-3e84-45b3-84ae-0c0aaf3525f7",
//     "tag_handling": "xml",
//     "outline_detection": true,
//     "non_splitting_tags": [
//       "a",
//       "p",
//       "span"
//     ],
//     "splitting_tags": [
//       "a",
//       "p",
//       "span"
//     ],
//     "ignore_tags": [
//       "a",
//       "p",
//       "span"
//     ]
//   }
class TranslateRequest implements Request
{
    use IsRequest;

    /** @var string[] */
    public array $text;
    public ?string $source_lang = null;
    public string $target_lang;
    public ?string $context = null;
    public ?bool $show_billed_characters = null;
    /** '0'|'1'|'nonewlines' */
    public ?string $split_sentences = null;
    public ?bool $preserve_formatting = null;
    /** 'default'|'more'|'less'|'prefer_more'|'prefer_less' */
    public ?string $formality = null;
    /** 'latency_optimized'|'quality_optimized'|'prefer_quality_optimized' */
    public ?string $model_type = null;
    public ?string $glossary_id = null;
    /** 'xml'|'html' */
    public ?string $tag_handling = null;
    public ?bool $outline_detection = null;
    /** @var string[] */
    public ?array $non_splitting_tags = null;
    /** @var string[] */
    public ?array $splitting_tags = null;
    /** @var string[] */
    public ?array $ignore_tags = null;
}
