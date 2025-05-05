# TranslateAny

TranslateAny is a project that turns any OpenAI compatible system into translate engine, exposing API compatible with Google Translate, DeepL and LibreTranslate.

# Usage

> **_NOTE:_** Before you start, [configure your application](#configuration).

```
git clone https://github.com/aazsamir/translateany.git
cd translateany
cp .env.example .env
docker compose up
```
or
```
git clone https://github.com/aazsamir/translateany.git
cd translateany
cp .env.example .env
composer install
./tempest serve --host=0.0.0.0 --port=8000
```

Now, on `http://localhost:8000`, you can test your configuration on translation page and use API.

![View](./docs/page.png)

# Configuration

## Configuration builder wizard

If you aren't familiar with PHP try configuration builder wizard.

```shell
php tempest create-config
```

If you don't have `php` installed, use `docker compose` to run wizard.
```shell
docker compose run --rm config-builder
```

## Manual configuration

Copy `app/Config/app.config.php.example` to `app/Config/app.config.php`.
```php
<?php
// app/Config/app.config.php
use App\System\AppConfig;
use App\Engine\OpenAI\OpenAIConfig;

return new AppConfig(
    translate: OpenAIEngine::new(
        // ollama host, /v1 is OpenAI API compatible endpoint 
        host: 'http://localhost:11434/v1',
        // or if you are using docker-compose 
        // host: 'http://host.docker.internal:11434/v1', 
        model: 'Bielik-11B-v2.3:IQ4_XS',
        systemPrompt: 'You are an automated translation system. Translate text to the target language. Do not add any additional information or context, just the translation.',
    ),
);
```

You may use different engines depending on target language by wrapping them in `RouteEngine`.
```php
<?php
// app/Config/app.config.php
use App\System\AppConfig;
use App\Engine\OpenAI\OpenAIEngine;
use App\Engine\Route\RouteEngine;
use App\Engine\Route\TranslateRoute;
use App\System\Language;

return new AppConfig(
    translate: RouteEngine::new(
        TranslateRoute::new(
            engine: OpenAIEngine::new(
                host: 'http://localhost:11434/v1',
                model: 'Bielik-11B-v2.3:IQ4_XS',
            ),
            languages: [
                Language::pl,
            ],
        ),
        TranslateRoute::new(
            engine: OpenAIEngine::new(
                host: 'http://localhost:11434/v1',
                model: 'qwen3:14b',
            ),
        ),
    ),
);
```

Depending on your needs, you may want to cache the translation results. You can do this by using `CacheEngine`
```php
<?php
// app/Config/app.config.php
use App\Engine\Cache\CacheEngine;
use App\Engine\OpenAI\OpenAIEngine;
use App\System\AppConfig;

return new AppConfig(
    translate: CacheEngine::new(
        engine: OpenAIEngine::new(
            model: 'Bielik-11B-v2.3:IQ4_XS',
            host: 'http://localhost:11434/v1',
        ),
        cacheMinutes: 5,
    ),
);
```

To access language detection, configure `detection` in `app/Config/app.config.php`:
```php
<?php
// app/Config/app.config.php
use App\Engine\OpenAI\OpenAIDetectEngine;
use App\System\AppConfig;

return new AppConfig(
    detection: OpenAIDetectEngine::new(
        host: 'http://localhost:11434/v1',
        model: 'hf.co/unsloth/Qwen3-1.7B-GGUF:IQ4_XS',
    ),
);
```

# API

TranslateAny exposes schema from different translation providers and differentates them by path prefix.
> **_NOTE:_** Not all endpoints are exposed right now, for example: document translation isn't handled.

## Google Translate V2
- `POST /google/v2/language/translate/v2`
- `POST /google/v2/language/translate/v2/detect`
- `GET /google/v2/language/translate/v2/languages`

## DeepL
- `POST /deepl/v2/translate`
- `GET /deepl/v2/languages`
- `POST /deepl/v2/glossary-language-pairs`
- `POST /deepl/v2/glossaries`
- `GET /deepl/v2/glossaries`
- `GET /deepl/v2/glossaries/{glossary_id}`
- `GET /deepl/v2/glossaries/{glossary_id}/entries`
- `DELETE /deepl/v2/glossaries/{glossary_id}`

## DeepLX
- `POST /deeplx/translate`
- `POST /deeplx/v1/translate`

## LibreTranslate
- `POST /libre/detect`
- `POST /libre/translate`
- `GET /libre/languages`

# Features
- ✅ OpenAI Compatible API Translation Engine
- ✅ LibreTranslate Translation Engine
- ❌ Native Ollama Translation Engine
- ❌ Native Llama.cpp Translation Engine
- ❌ Native vLLM Translation Engine
- ❌ Google Translate Translation Engine
- ❌ DeepL Translation Engine
- ✅ DeepL API
    > **_NOTE:_** Currently missing `/v2/write/rephrase` endpoint
- ✅ DeepLX API
- ✅ Google Translate v2 API
- ❌ Google Translate v3 API
- ✅ LibreTranslate API
- ✅ Language Detection
- ❌ Rephrasing
- ❌ API authorization
- ❌ Document Translation
- ⚠️ HTML Playground to test integration
    > **_NOTE:_** This is a work in progress, and not all features are possible to test. Basic translation is available.
- ❌ Examples of integration with other projects, like SillyTavern

# License

This project is licensed under the MIT License.
