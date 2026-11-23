<?php

namespace App;

/**
 * Collects the CSS that views would otherwise write as style="…" attributes,
 * which the Content-Security-Policy blocks. Each set of declarations becomes a
 * class named after its hash, and the layout prints every collected rule in
 * one nonced <style> block in the head.
 *
 * Use it for values that come from config or props, such as accent colours
 * and icon masks. Fixed styles belong in resources/css/app.css.
 */
class PageStyles
{
    /** @var array<string, string> */
    private array $rules = [];

    /**
     * The class to put on an element that needs these declarations.
     */
    public function classFor(string $declarations): string
    {
        $class = 'ps-'.substr(hash('xxh3', $declarations), 0, 10);
        $this->rules[$class] = $declarations;

        return $class;
    }

    /**
     * Every collected rule as CSS. "<" is escaped so that no value can close
     * the surrounding <style> element.
     */
    public function css(): string
    {
        return collect($this->rules)
            ->map(fn (string $declarations, string $class): string => ".{$class} { {$declarations} }")
            ->map(fn (string $rule): string => str_replace('<', '\3c ', $rule))
            ->implode("\n");
    }
}
