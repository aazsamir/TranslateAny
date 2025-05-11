<?php

declare(strict_types=1);

namespace App\System\Document\Chunking;

class FullPageStrategy implements ChunkingStrategy
{
    private const int WINDOW_SIZE = 2;

    public function chunk(iterable $pages): iterable
    {
        $pages = iterator_to_array($pages, false);
        $pagesWindow = [];
        $i = 0;

        foreach ($pages as $i => $text) {
            $text = $this->cleanPage($text);
            $pagesWindow[] = $text;

            if (count($pagesWindow) >= self::WINDOW_SIZE) {
                // handle first page from the window
                yield new Chunk(
                    page: $i,
                    text: $this->handlePagesWindow($pagesWindow),
                );
                // remove first page from the window
                array_shift($pagesWindow);
            }
        }

        foreach ($pagesWindow as $page) {
            yield new Chunk(
                page: ++$i,
                text: $page,
            );
        }
    }

    private function cleanPage(string $page): string
    {
        $lines = explode("\n", $page);
        // TODO: some preprocessing should happen here, like removing lines with page numbers
        // array_pop($lines);

        return implode("\n", $lines);
    }

    /**
     * @param array<int, string> $pages
     */
    private function handlePagesWindow(array &$pages): string
    {
        $page = &$pages[0];

        if (! \str_contains($page, '.')) {
            return $page;
        }

        if (count($pages) < self::WINDOW_SIZE) {
            return $page;
        }

        $nextPage = &$pages[1];

        // move last phrase (by dot) to the next page
        $lines = explode("\n", $page);
        $lastLine = '';

        do {
            $lastLine = array_pop($lines) . "\n" . $lastLine;
            $lastPhrasePos = strrpos($lastLine, '. ');
        } while ($lastPhrasePos === false && count($lines) > 0);

        if ($lastPhrasePos !== false) {
            $lastPhrase = substr($lastLine, $lastPhrasePos + 1);

            // remove this phrase from the page
            $lines[] = substr($lastLine, 0, $lastPhrasePos + 1);

            // add this phrase to the next page to start
            $nextPage = trim($lastPhrase) . ' ' . $nextPage;
        } else {
            // "return" the last line to the page
            $lines[] = $lastLine;
        }

        return implode("\n", $lines);
    }
}
