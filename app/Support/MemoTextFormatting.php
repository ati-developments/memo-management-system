<?php

namespace App\Support;

class MemoTextFormatting
{
    public static function sanitize(string $html): string
    {
        $document = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><body>'.$html.'</body>', LIBXML_NONET);
            $render = function (\DOMNode $node) use (&$render): string {
                if ($node instanceof \DOMText) {
                    return htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }
                if (!($node instanceof \DOMElement)) return '';
                $tag = strtolower($node->tagName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math'], true)) return '';
                $content = '';
                foreach ($node->childNodes as $child) $content .= $render($child);
                $tag = ['b' => 'strong', 'i' => 'em'][$tag] ?? $tag;
                if ($tag === 'br') return '<br>';
                if (in_array($tag, ['strong', 'em', 'u', 'p', 'div'], true)) return '<'.$tag.'>'.$content.'</'.$tag.'>';
                return $content;
            };
            $body = $document->getElementsByTagName('body')->item(0);
            $result = '';
            foreach ($body->childNodes as $child) $result .= $render($child);
            return $result;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}
