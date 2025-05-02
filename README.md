# TranslateAny

TranslateAny is a project that turns any OpenAI compatible system into translate engine, exposing [LibreTranslate](https://github.com/LibreTranslate/LibreTranslate) compatible API.

# Usage

> **_NOTE:_** Before you start, [configure your application](#configuration).

```
git clone https://github.com/aazsamir/translateany.git
cd translateany
docker compose up
```
or
```
git clone https://github.com/aazsamir/translateany.git
cd translateany
composer install
./tempest serve
```

Now, on `http://localhost:8000`, you can test your configuration on translation page and use API.

![View](./docs/page.png)

# Configuration

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

# License

This project is licensed under the MIT License.
